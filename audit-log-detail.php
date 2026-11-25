<?php
require_once __DIR__ . '/config.php';
require_audit_log_access();

$idValue = $_GET['id'] ?? null;
$auditId = is_scalar($idValue) && ctype_digit((string)$idValue) ? (int)$idValue : 0;
if ($auditId < 1) {
    http_response_code(404);
    exit('The requested audit record was not found.');
}

$auditRow = null;
try {
    $statement = db()->prepare('SELECT id, actor, actor_full_name, actor_role, target_legacy_id, action, module,
        description, old_value, new_value, device_type, browser, ip_address, location, created_at
        FROM audit_log WHERE id = ?');
    $statement->execute([$auditId]);
    $auditRow = $statement->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $error) {
    error_log('Audit detail could not load: ' . $error->getMessage());
    http_response_code(500);
    exit('Audit details could not be loaded. Confirm that the audit metadata migration has been applied.');
}

if (!$auditRow) {
    http_response_code(404);
    exit('The requested audit record was not found.');
}

require_once __DIR__ . '/role_store.php';
sk_audit(db(), current_user(), (string)$auditId, 'audit_detail_viewed', null, ['audit_record_id' => $auditId]);

$siteName = trim(ucwords(str_replace(['_', '-'], ' ', (string)($auditRow['module'] ?? ''))));
$auditDescription = $auditRow['description'] ?: 'No description was recorded.';
if ($auditRow['action'] === 'page_view' && $siteName !== '') {
    $auditDescription = 'Opened the ' . $siteName . ' page.';
}
$browserLocation = json_decode((string)($auditRow['location'] ?? ''), true);
if (is_array($browserLocation) && ($browserLocation['source'] ?? '') === 'browser'
    && isset($browserLocation['latitude'], $browserLocation['longitude'], $browserLocation['accuracy_m'])
    && is_numeric($browserLocation['latitude'])
    && is_numeric($browserLocation['longitude'])
    && is_numeric($browserLocation['accuracy_m'])) {
    $latitude = (float)$browserLocation['latitude'];
    $longitude = (float)$browserLocation['longitude'];
    $accuracy = (float)$browserLocation['accuracy_m'];
    $browserLocation = is_finite($latitude) && is_finite($longitude) && is_finite($accuracy)
        && $latitude >= -90 && $latitude <= 90 && $longitude >= -180 && $longitude <= 180
        && $accuracy >= 0
        ? ['latitude' => $latitude, 'longitude' => $longitude, 'accuracy_m' => $accuracy]
        : null;
} else {
    $browserLocation = null;
}

function audit_detail_value($value, $action = null) {
    if ($value === null || $value === '') return 'No data recorded.';
    $decoded = json_decode((string)$value, true);
    if (json_last_error() !== JSON_ERROR_NONE) return (string)$value;
    if ($action === 'page_view' && is_array($decoded) && !empty($decoded['page'])) {
        $pageName = pathinfo((string)$decoded['page'], PATHINFO_FILENAME);
        $pageName = trim(ucwords(str_replace(['_', '-'], ' ', $pageName)));
        return $pageName === '' ? 'Page opened.' : 'Opened the ' . $pageName . ' page.';
    }
    return json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Audit Log Details | KK Youth Profiling System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" integrity="sha384-XGjxtQfXaH2tnPFa9x+ruJTuLE3Aa6LhHSWRr1XeTyhezb4abCG4ccI5AkVDxqC+" crossorigin="anonymous">
    <link href="sidebar.css?v=32" rel="stylesheet">
    <link href="vendor/adminlte/css/adminlte.min.css" rel="stylesheet">
    <script src="vendor/adminlte/js/adminlte.min.js" defer></script>
    <style>
        :root{--audit-line:#e4e8e5;--audit-muted:#69736d;--audit-ink:#26342d;--audit-green:#188a52;--audit-soft:#f4f6f4}
        body{margin:0;background:#fff;color:var(--audit-ink);font-family:Arial,sans-serif}
        .top-strip{height:5px;background:var(--audit-green)}
        .header{min-height:70px;background:#fff;border-bottom:1px solid var(--audit-line);display:flex;align-items:center;justify-content:space-between;padding:0 26px;gap:14px}
        .header-title{font-size:19px;font-weight:750;color:var(--audit-ink)}
        .content{padding:22px 26px;max-width:1180px;margin:0 auto}
        .detail-back{display:inline-flex;align-items:center;gap:7px;margin:0 0 14px;color:var(--audit-green);font-size:13px;font-weight:650;text-decoration:none}
        .detail-back:hover{color:#126b40;text-decoration:underline}
        .detail-card{background:#fff;border:1px solid var(--audit-line);border-radius:6px;overflow:hidden}
        .detail-heading{padding:18px 20px;background:#f7f8f7;border-bottom:1px solid var(--audit-line)}
        .detail-heading h1{font-size:21px;font-weight:750;margin:0 0 4px;color:var(--audit-ink)}
        .detail-heading p{color:var(--audit-muted);margin:0;font-size:13px}
        .detail-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:0 22px;padding:8px 20px 20px}
        .detail-item{padding:13px 0;border-bottom:1px solid var(--audit-line);min-width:0}
        .detail-item.full{grid-column:1/-1}
        .detail-label{display:block;color:var(--audit-muted);font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.035em;margin-bottom:5px}
        .detail-value{overflow-wrap:anywhere}
        .detail-data{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px;padding:0 20px 20px}
        .data-card{min-width:0;border:1px solid var(--audit-line);border-radius:5px;overflow:hidden}
        .data-card h2{font-size:13px;font-weight:700;margin:0;padding:11px 13px;color:var(--audit-ink);background:var(--audit-soft);border-bottom:1px solid var(--audit-line)}
        .data-value{max-height:420px;overflow:auto;white-space:pre-wrap;overflow-wrap:anywhere;margin:0;padding:13px;font-size:13px;color:#36423b;background:#fff}
        .header-account-area .header-logout{background:transparent;border:0;color:var(--audit-muted);padding:.25rem .4rem}
        .header-account-area .header-logout:hover{color:var(--audit-green);text-decoration:underline}
        html[data-bs-theme="dark"] body,
        html[data-bs-theme="dark"] .header,
        html[data-bs-theme="dark"] .detail-card,
        html[data-bs-theme="dark"] .data-value{background:var(--bs-body-bg);color:var(--bs-body-color)}
        html[data-bs-theme="dark"] .detail-heading,
        html[data-bs-theme="dark"] .data-card h2{background:var(--bs-tertiary-bg)}
        html[data-bs-theme="dark"] .detail-heading h1{color:var(--bs-body-color)}
        html[data-bs-theme="dark"] .detail-card,
        html[data-bs-theme="dark"] .detail-heading,
        html[data-bs-theme="dark"] .detail-item,
        html[data-bs-theme="dark"] .data-card{border-color:var(--bs-border-color)}
        @media(max-width:767px){.content{padding:16px 14px}.header{padding:0 16px}.detail-grid,.detail-data{grid-template-columns:1fr}.detail-item.full{grid-column:auto}.detail-heading{padding:16px}}
    </style>
    <script src="theme.js"></script>
</head>
<body>
<div class="top-strip"></div>
<script>
document.documentElement.classList.add('sidebar-expand-lg');
window.SK_CAN_VIEW_AUDIT_LOG = true;
window.SK_MEMBER_PORTAL = <?= (current_user()['role'] ?? '') === 'KK Member' && !empty(current_user()['legacy_id']) ? 'true' : 'false' ?>;
</script>
<script src="sidebar.js?v=16"></script>
<main class="main page-content">
    <header class="header">
        <div class="d-flex align-items-center gap-2">
            <button class="btn btn-sm sidebar-toggle d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebar" aria-controls="sidebar" aria-label="Open menu"><i class="bi bi-list fs-5"></i></button>
            <div>
                <div class="header-title">Audit Log Details</div>
                <small>KK Youth Profiling System</small>
            </div>
        </div>
        <div class="header-account-area">
            <span class="header-avatar"><?= e(uinit()) ?></span>
            <div class="d-none d-md-block header-identity">
                <div class="header-identity-name"><?= e(uname()) ?></div>
                <div class="header-identity-role"><?= e(strtoupper(urole())) ?></div>
            </div>
            <a href="logout.php" class="btn btn-sm header-logout"><i class="bi bi-box-arrow-right me-1"></i>Logout</a>
        </div>
    </header>
    <div class="content">
        <a class="detail-back" href="audit-log.php"><i class="bi bi-arrow-left" aria-hidden="true"></i>Back to audit log</a>
        <section class="detail-card" aria-labelledby="activityTitle">
            <div class="detail-heading">
                <h1 id="activityTitle"><?= e(ucwords(str_replace('_', ' ', (string)$auditRow['action']))) ?></h1>
            </div>
            <div class="detail-grid">
                <div class="detail-item"><span class="detail-label">User</span><div class="detail-value"><?= e($auditRow['actor_full_name'] ?: $auditRow['actor']) ?></div></div>
                <div class="detail-item"><span class="detail-label">Account username</span><div class="detail-value"><?= e($auditRow['actor']) ?></div></div>
                <div class="detail-item"><span class="detail-label">Assigned role</span><div class="detail-value"><?= e($auditRow['actor_role'] ?: 'Not recorded') ?></div></div>
                <?php if ($auditRow['action'] !== 'page_view'): ?>
                    <div class="detail-item"><span class="detail-label">Module</span><div class="detail-value"><?= e($siteName ?: 'Not recorded') ?></div></div>
                <?php endif; ?>
                <div class="detail-item"><span class="detail-label">Date and time</span><div class="detail-value"><?= e($auditRow['created_at']) ?></div></div>
                <div class="detail-item full"><span class="detail-label">What happened</span><div class="detail-value"><?= e($auditDescription) ?></div></div>
                <?php if (!empty($auditRow['target_legacy_id'])): ?>
                    <div class="detail-item"><span class="detail-label">Affected record</span><div class="detail-value"><?= e($auditRow['target_legacy_id']) ?></div></div>
                <?php endif; ?>
                <div class="detail-item"><span class="detail-label">Device Type</span><div class="detail-value"><?= e($auditRow['device_type'] ?: 'Not recorded') ?></div></div>
                <div class="detail-item"><span class="detail-label">Browser</span><div class="detail-value"><?= e($auditRow['browser'] ?: 'Not recorded') ?></div></div>
                <div class="detail-item"><span class="detail-label">IP address</span><div class="detail-value"><?= e($auditRow['ip_address'] ?: 'Not recorded') ?></div></div>
                <div class="detail-item full">
                    <span class="detail-label">Login location</span>
                    <div class="detail-value">
                        <?php if ($browserLocation !== null): ?>
                            Browser-reported coordinates: <?= e(number_format($browserLocation['latitude'], 6, '.', '')) ?>, <?= e(number_format($browserLocation['longitude'], 6, '.', '')) ?>
                            <span class="text-muted">(accuracy ±<?= e(number_format($browserLocation['accuracy_m'], 1, '.', '')) ?> m)</span>
                        <?php else: ?>
                            <?= e($auditRow['location'] ?: 'Not recorded') ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="detail-data">
                <section class="data-card">
                    <h2>Previous data</h2>
                    <div class="data-value"><?= e(audit_detail_value($auditRow['old_value'], $auditRow['action'])) ?></div>
                </section>
                <section class="data-card">
                    <h2>Updated data</h2>
                    <div class="data-value"><?= e(audit_detail_value($auditRow['new_value'], $auditRow['action'])) ?></div>
                </section>
            </div>
        </section>
    </div>
</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
