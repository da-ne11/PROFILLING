<?php
/* =========================================================
   receipts.php - INTERNAL expense receipts (SK officials only)
   GET  receipts.php?id=EXPENSE_ID   logged-in SK officials only: view the receipt
   POST action=upload                officers only: upload / replace a receipt (PDF, JPG, PNG, max 5 MB)
   POST action=delete                officers only: remove a receipt
   Files are kept in uploads/receipts/ (the uploads/.htaccess blocks direct access)
   and the details are saved in app_data under the key "skReceipts".
   The PUBLIC never gets a receipt: the landing page only shows project, item,
   supplier and amount. Put this file in the same folder as config.php.
   ========================================================= */
require_once __DIR__ . '/config.php';

const RECEIPT_MAX_BYTES = 5 * 1024 * 1024;
const RECEIPT_TYPES = ['application/pdf' => 'pdf', 'image/jpeg' => 'jpg', 'image/png' => 'png'];

function receipt_dir() {
    $dir = __DIR__ . '/uploads/receipts';
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    $guard = __DIR__ . '/uploads/.htaccess';
    if (!is_file($guard)) {
        file_put_contents($guard, "Require all denied\n<IfModule !mod_authz_core.c>\nDeny from all\n</IfModule>\n");
    }
    return $dir;
}

function receipt_flash($type, $msg) {
    $_SESSION['receipt_flash'] = ['type' => $type, 'msg' => $msg];
    header('Location: index.php#expenses');
    exit;
}

/* the expense must really exist, so nobody can attach files to made-up ids */
function receipt_expense_exists($id) {
    if (!preg_match('/^[A-Za-z0-9_-]{1,80}$/', $id)) return false;
    $raw = db()->query("SELECT data_value FROM app_data WHERE data_key = 'skTransparencyExpenses'")->fetchColumn();
    $list = $raw === false ? [] : json_decode((string)$raw, true);
    if (!is_array($list)) return false;
    foreach ($list as $x) {
        if (is_array($x) && isset($x['id']) && preg_replace('/[^A-Za-z0-9_-]/', '', (string)$x['id']) === $id) return true;
    }
    return false;
}

$user = current_user();
$isOfficer = $user !== null && ($user['role'] ?? '') !== 'KK Member';

/* ---------- view (SK officials only) ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!$isOfficer) {
        http_response_code(403);
        exit('Receipts are internal and visible to SK officials only. Please log in.');
    }
    $id = (string)($_GET['id'] ?? '');
    $record = null;
    if (preg_match('/^[A-Za-z0-9_-]{1,80}$/', $id)) {
        $raw = db()->query("SELECT data_value FROM app_data WHERE data_key = 'skReceipts'")->fetchColumn();
        $all = $raw === false ? [] : json_decode((string)$raw, true);
        $record = is_array($all) && isset($all[$id]) && is_array($all[$id]) ? $all[$id] : null;
    }
    $path = $record ? receipt_dir() . '/' . basename((string)($record['file'] ?? '')) : '';
    if (!$record || !is_file($path)) {
        http_response_code(404);
        exit('No receipt has been uploaded for this expense.');
    }
    $mime = (string)($record['mime'] ?? 'application/octet-stream');
    $ext = RECEIPT_TYPES[$mime] ?? 'bin';
    /* config.php's strict headers can stop browsers from showing a PDF, so relax them for this file only */
    header_remove('Content-Security-Policy');
    header_remove('X-Frame-Options');
    header("Content-Security-Policy: frame-ancestors 'self'");
    header('X-Frame-Options: SAMEORIGIN');
    header('Content-Type: ' . $mime);
    header('X-Content-Type-Options: nosniff');
    header('Cache-Control: private, no-store');
    header('Content-Disposition: inline; filename="receipt-' . preg_replace('/[^A-Za-z0-9_-]/', '', $id) . '.' . $ext . '"');
    header('Content-Length: ' . filesize($path));
    readfile($path);
    exit;
}

/* ---------- upload / delete (officers only) ---------- */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method not allowed.');
}
if (!$isOfficer || !can_write_app_data()) receipt_flash('error', 'Only SK officials who are logged in can upload or remove a receipt.');
if (!empty($user['must_change_password'])) receipt_flash('error', 'Change your temporary password before continuing.');
if (!hash_equals(csrf_token(), (string)($_POST['csrf'] ?? ''))) receipt_flash('error', 'Your session expired. Reload the page and try again.');

$action = (string)($_POST['action'] ?? '');
$id = (string)($_POST['id'] ?? '');
if (!in_array($action, ['upload', 'delete'], true) || !receipt_expense_exists($id)) receipt_flash('error', 'Invalid request.');

$newFile = null;
$newName = null;
$mime = '';
if ($action === 'upload') {
    $f = $_FILES['receipt'] ?? null;
    if (!$f || ($f['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        receipt_flash('error', ($f['error'] ?? 0) === UPLOAD_ERR_INI_SIZE || ($f['error'] ?? 0) === UPLOAD_ERR_FORM_SIZE
            ? 'The file is too large (max 5 MB).' : 'Choose a receipt file to upload.');
    }
    if ($f['size'] <= 0 || $f['size'] > RECEIPT_MAX_BYTES) receipt_flash('error', 'The file must be 5 MB or smaller.');
    $mime = (string)(new finfo(FILEINFO_MIME_TYPE))->file($f['tmp_name']);
    if (!isset(RECEIPT_TYPES[$mime])) receipt_flash('error', 'Only PDF, JPG or PNG files are allowed.');
    $newName = bin2hex(random_bytes(16)) . '.' . RECEIPT_TYPES[$mime];
    $newFile = receipt_dir() . '/' . $newName;
    if (!move_uploaded_file($f['tmp_name'], $newFile)) receipt_flash('error', 'Could not save the file.');
    chmod($newFile, 0644);
}

$pdo = db();
$oldFile = null;
try {
    $pdo->beginTransaction();
    $st = $pdo->prepare("SELECT data_value FROM app_data WHERE data_key = 'skReceipts' FOR UPDATE");
    $st->execute();
    $raw = $st->fetchColumn();
    $all = $raw === false ? [] : json_decode((string)$raw, true);
    if (!is_array($all)) $all = [];
    if (isset($all[$id]['file'])) $oldFile = basename((string)$all[$id]['file']);

    if ($action === 'upload') {
        $all[$id] = [
            'file' => $newName,
            'mime' => $mime,
            'uploadedAt' => date('Y-m-d H:i:s'),
            'uploadedBy' => $user['username'],
        ];
    } else {
        unset($all[$id]);
    }
    $json = json_encode($all === [] ? new stdClass() : $all, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    $pdo->prepare('INSERT INTO app_data (data_key, data_value, updated_by) VALUES (?, ?, ?)
                   ON DUPLICATE KEY UPDATE data_value = VALUES(data_value), updated_by = VALUES(updated_by)')
        ->execute(['skReceipts', $json, $user['username']]);
    $pdo->commit();
} catch (Throwable $ex) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    if ($newFile && is_file($newFile)) unlink($newFile);
    error_log('Receipt save failed: ' . $ex->getMessage());
    receipt_flash('error', 'Could not save the receipt. Please try again.');
}

if ($oldFile && $oldFile !== $newName && is_file(receipt_dir() . '/' . $oldFile)) unlink(receipt_dir() . '/' . $oldFile);
receipt_flash('ok', $action === 'upload' ? 'Receipt saved. It is visible to SK officials only.' : 'Receipt removed.');