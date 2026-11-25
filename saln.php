<?php
/* =========================================================
   saln.php - SALN (Statement of Assets, Liabilities and Net Worth)
   GET  saln.php?key=...           public: view the posted SALN of an SK official
   GET  saln.php?key=...&view=1    public: viewer page with a Back button
   POST action=upload              officers only: upload / replace a SALN (PDF, JPG, PNG, max 5 MB)
   POST action=delete              officers only: remove a posted SALN
   Uploaded files are kept in uploads/saln/ (blocked from direct access) and
   details are saved in app_data under the key "skSaln".
   The ready-made SALN PDFs are read from the "saln" folder (saln/sample-saln-*.pdf).
   The old saln_config.php is now merged here, so you can delete that file.
   ========================================================= */
require_once __DIR__ . '/config.php';

/* ---------- settings (dating saln_config.php) ---------- */
/* true  = kapag wala pang na-upload na SALN, bubuksan ang PDF mula sa "saln" folder */
/* false = "No SALN has been posted" lang ang lalabas */
if (!defined('SALN_DEMO')) define('SALN_DEMO', true);
const SALN_DEMO_DIR = __DIR__ . '/saln';
const SALN_DEMO_SLUGS = ['chair', 'secretary', 'treasurer', 'kagawad1', 'kagawad2', 'kagawad3'];

const SALN_MAX_BYTES = 5 * 1024 * 1024;
const SALN_TYPES = ['application/pdf' => 'pdf', 'image/jpeg' => 'jpg', 'image/png' => 'png'];

function saln_dir() {
    $dir = __DIR__ . '/uploads/saln';
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    $guard = __DIR__ . '/uploads/.htaccess';
    if (!is_file($guard)) {
        file_put_contents($guard, "Require all denied\n<IfModule !mod_authz_core.c>\nDeny from all\n</IfModule>\n");
    }
    return $dir;
}

/* hanapin ang PDF sa saln folder para sa isang official (chair, secretary, treasurer, kagawad1-3) */
function saln_demo_file($slug) {
    if (!in_array($slug, SALN_DEMO_SLUGS, true)) $slug = '';
    $names = $slug !== '' ? ['sample-saln-' . $slug . '.pdf', $slug . '.pdf', 'saln-' . $slug . '.pdf'] : [];
    $names[] = 'sample-saln.pdf';
    foreach ($names as $name) {
        $file = SALN_DEMO_DIR . '/' . $name;
        if (is_file($file)) return $file;
    }
    return '';
}

function saln_flash($type, $msg) {
    $_SESSION['saln_flash'] = ['type' => $type, 'msg' => $msg];
    header('Location: index.php#officials');
    exit;
}

function saln_key_is_valid($key) {
    if (!preg_match('/^[A-Za-z0-9_-]{1,80}$/', $key)) return false;
    if ($key === 'acct_chair' || $key === 'acct_secretary') return true;
    $raw = db()->query("SELECT data_value FROM app_data WHERE data_key = 'kkMembers'")->fetchColumn();
    $members = $raw === false ? [] : json_decode((string)$raw, true);
    if (!is_array($members)) return false;
    foreach ($members as $m) {
        if (is_array($m) && isset($m['id']) && preg_replace('/[^A-Za-z0-9_-]/', '', (string)$m['id']) === $key) return true;
    }
    return false;
}

/* ---------- view (public) ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'GET' && !empty($_GET['view'])) {
    /* viewer page: the SALN file inside a page that has a Back button */
    $fileQuery = ['key' => preg_replace('/[^A-Za-z0-9_-]/', '', (string)($_GET['key'] ?? ''))];
    if (!empty($_GET['demo'])) $fileQuery['demo'] = preg_replace('/[^a-z0-9]/', '', (string)$_GET['demo']);
    if (!empty($_GET['v'])) $fileQuery['v'] = substr((string)$_GET['v'], 0, 30);
    $fileUrl = 'saln.php?' . http_build_query($fileQuery);
    ?><!DOCTYPE html>
<html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>SALN | KK Youth Profiling System</title>
<style>
html,body{height:100%;margin:0;font-family:Arial,sans-serif;background:#e9ecef}
.bar{height:52px;box-sizing:border-box;display:flex;align-items:center;gap:12px;padding:0 14px;background:#052c65;color:#fff}
.bar a{color:#fff;text-decoration:none;font-weight:bold;font-size:14px}
.bar .back{background:rgba(255,255,255,.15);padding:8px 14px;border-radius:7px}
.bar .back:hover{background:rgba(255,255,255,.28)}
.bar .title{flex:1;font-size:15px}
.bar .open{font-weight:normal;font-size:13px;text-decoration:underline}
iframe{display:block;width:100%;height:calc(100% - 52px);border:0;background:#fff}
</style></head><body>
<div class="bar">
    <a class="back" href="index.php#officials" onclick="if (history.length > 1) { history.back(); return false; }">&larr; Back</a>
    <div class="title">Statement of Assets, Liabilities and Net Worth (SALN)</div>
    <a class="open" href="<?= e($fileUrl) ?>" target="_blank" rel="noopener">Open file only</a>
</div>
<iframe src="<?= e($fileUrl) ?>" title="SALN document"></iframe>
</body></html><?php
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $key = (string)($_GET['key'] ?? '');
    $record = null;
    if (preg_match('/^[A-Za-z0-9_-]{1,80}$/', $key)) {
        $raw = db()->query("SELECT data_value FROM app_data WHERE data_key = 'skSaln'")->fetchColumn();
        $all = $raw === false ? [] : json_decode((string)$raw, true);
        $record = is_array($all) && isset($all[$key]) && is_array($all[$key]) ? $all[$key] : null;
    }
    $path = $record ? saln_dir() . '/' . basename((string)($record['file'] ?? '')) : '';
    if ((!$record || !is_file($path)) && SALN_DEMO && !empty($_GET['demo'])) {
        /* walang na-upload pa: gamitin ang PDF mula sa saln folder */
        $file = saln_demo_file((string)$_GET['demo']);
        if ($file !== '') {
            $record = ['mime' => 'application/pdf', 'year' => ''];
            $path = $file;
        } else {
            http_response_code(404);
            exit('SALN file not found. Put the PDF files in this folder: ' . SALN_DEMO_DIR . DIRECTORY_SEPARATOR . ' (for example sample-saln-' . preg_replace('/[^a-z0-9]/', '', (string)$_GET['demo']) . '.pdf)');
        }
    }
    if (!$record || !is_file($path)) {
        http_response_code(404);
        exit('No SALN has been posted for this official.');
    }
    $mime = (string)($record['mime'] ?? 'application/octet-stream');
    $ext = SALN_TYPES[$mime] ?? 'bin';
    /* config.php's strict headers can stop browsers from showing a PDF, so relax them for this file only */
    header_remove('Content-Security-Policy');
    header_remove('X-Frame-Options');
    header("Content-Security-Policy: frame-ancestors 'self'");
    header('X-Frame-Options: SAMEORIGIN');
    header('Content-Type: ' . $mime);
    header('X-Content-Type-Options: nosniff');
    header('Content-Disposition: inline; filename="SALN-' . preg_replace('/[^0-9]/', '', (string)($record['year'] ?? '')) . '.' . $ext . '"');
    header('Content-Length: ' . filesize($path));
    readfile($path);
    exit;
}

/* ---------- upload / delete (officers only) ---------- */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method not allowed.');
}
$user = current_user();
if (!$user || !can_write_app_data()) saln_flash('error', 'Only SK officials who are logged in can post or remove a SALN.');
if (!empty($user['must_change_password'])) saln_flash('error', 'Change your temporary password before continuing.');
if (!hash_equals(csrf_token(), (string)($_POST['csrf'] ?? ''))) saln_flash('error', 'Your session expired. Reload the page and try again.');

$action = (string)($_POST['action'] ?? '');
$key = (string)($_POST['key'] ?? '');
if (!in_array($action, ['upload', 'delete'], true) || !saln_key_is_valid($key)) saln_flash('error', 'Invalid request.');

$newFile = null;
$newName = null;
$mime = '';
$year = '';
if ($action === 'upload') {
    $year = (string)($_POST['year'] ?? '');
    if (!preg_match('/^(20\d{2}|2100)$/', $year)) saln_flash('error', 'Enter a valid year covered.');
    $f = $_FILES['saln'] ?? null;
    if (!$f || ($f['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        saln_flash('error', ($f['error'] ?? 0) === UPLOAD_ERR_INI_SIZE || ($f['error'] ?? 0) === UPLOAD_ERR_FORM_SIZE
            ? 'The file is too large (max 5 MB).' : 'Choose a SALN file to upload.');
    }
    if ($f['size'] <= 0 || $f['size'] > SALN_MAX_BYTES) saln_flash('error', 'The file must be 5 MB or smaller.');
    $mime = (string)(new finfo(FILEINFO_MIME_TYPE))->file($f['tmp_name']);
    if (!isset(SALN_TYPES[$mime])) saln_flash('error', 'Only PDF, JPG or PNG files are allowed.');
    $newName = bin2hex(random_bytes(16)) . '.' . SALN_TYPES[$mime];
    $newFile = saln_dir() . '/' . $newName;
    if (!move_uploaded_file($f['tmp_name'], $newFile)) saln_flash('error', 'Could not save the file.');
    chmod($newFile, 0644);
}

$pdo = db();
$oldFile = null;
try {
    $pdo->beginTransaction();
    $st = $pdo->prepare("SELECT data_value FROM app_data WHERE data_key = 'skSaln' FOR UPDATE");
    $st->execute();
    $raw = $st->fetchColumn();
    $all = $raw === false ? [] : json_decode((string)$raw, true);
    if (!is_array($all)) $all = [];
    if (isset($all[$key]['file'])) $oldFile = basename((string)$all[$key]['file']);

    if ($action === 'upload') {
        $all[$key] = [
            'file' => $newName,
            'mime' => $mime,
            'year' => $year,
            'uploadedAt' => date('Y-m-d H:i:s'),
            'uploadedBy' => $user['username'],
        ];
    } else {
        unset($all[$key]);
    }
    $json = json_encode($all === [] ? new stdClass() : $all, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    $pdo->prepare('INSERT INTO app_data (data_key, data_value, updated_by) VALUES (?, ?, ?)
                   ON DUPLICATE KEY UPDATE data_value = VALUES(data_value), updated_by = VALUES(updated_by)')
        ->execute(['skSaln', $json, $user['username']]);
    $pdo->commit();
} catch (Throwable $ex) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    if ($newFile && is_file($newFile)) unlink($newFile);
    error_log('SALN save failed: ' . $ex->getMessage());
    saln_flash('error', 'Could not save the SALN. Please try again.');
}

if ($oldFile && $oldFile !== $newName && is_file(saln_dir() . '/' . $oldFile)) unlink(saln_dir() . '/' . $oldFile);
saln_flash('ok', $action === 'upload' ? 'SALN posted successfully.' : 'SALN removed.');