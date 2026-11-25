<?php
require_once __DIR__ . '/config.php';
require_audit_log_access();

function audit_log_filter_value($key, $maximum = 120) {
    $value = $_GET[$key] ?? '';
    return is_scalar($value) ? substr(trim((string)$value), 0, $maximum) : '';
}

$pageSize = 50;
$pageValue = $_GET['page'] ?? null;
$page = is_scalar($pageValue) && ctype_digit((string)$pageValue) ? max(1, (int)$pageValue) : 1;
$filters = [
    'start' => audit_log_filter_value('start', 10),
    'end' => audit_log_filter_value('end', 10),
    'user' => audit_log_filter_value('user'),
];
foreach (['start', 'end'] as $dateFilter) {
    if ($filters[$dateFilter] !== '') {
        $date = DateTimeImmutable::createFromFormat('!Y-m-d', $filters[$dateFilter]);
        if (!$date || $date->format('Y-m-d') !== $filters[$dateFilter]) $filters[$dateFilter] = '';
    }
}
$where = [];
$params = [];
if ($filters['start'] !== '') {
    $where[] = 'created_at >= ?';
    $params[] = $filters['start'] . ' 00:00:00';
}
if ($filters['end'] !== '') {
    $where[] = 'created_at < DATE_ADD(?, INTERVAL 1 DAY)';
    $params[] = $filters['end'] . ' 00:00:00';
}
if ($filters['user'] !== '') {
    $where[] = '(actor_full_name LIKE ? OR actor LIKE ?)';
    $params[] = '%' . $filters['user'] . '%';
    $params[] = '%' . $filters['user'] . '%';
}
$whereSql = $where ? ' WHERE ' . implode(' AND ', $where) : '';
$filterQuery = http_build_query(array_filter($filters, function ($value) { return $value !== ''; }));
$auditCount = 0;
$auditRows = [];
$auditError = '';
if (($_GET['export'] ?? '') === 'csv') {
    try {
        $export = db()->prepare('SELECT created_at, actor_full_name, actor, actor_role, action, module, description,
            target_legacy_id, device_type, browser, user_agent, ip_address, location, old_value, new_value
            FROM audit_log' . $whereSql . ' ORDER BY created_at DESC, id DESC');
        $export->execute($params);
    } catch (PDOException $error) {
        error_log('Audit log CSV export could not load: ' . $error->getMessage());
        http_response_code(500);
        exit('Audit export unavailable. Confirm that the audit activity metadata migration has been applied.');
    }
    require_once __DIR__ . '/role_store.php';
    sk_audit(db(), current_user(), null, 'audit_exported', null, ['filters' => $filters], 'Exported audit activity records as CSV.');
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="audit-activity.csv"');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['Date and time', 'Full name', 'Username', 'Role', 'Activity', 'Module', 'Description',
        'Affected record', 'Device Type', 'Browser', 'User-Agent', 'IP address', 'Location', 'Previous data', 'Updated data']);
    while ($record = $export->fetch(PDO::FETCH_NUM)) {
        foreach ($record as &$cell) {
            $cell = (string)($cell ?? '');
            if ($cell !== '' && preg_match('/^[=+\-@\t\r]/', $cell)) $cell = "'" . $cell;
        }
        unset($cell);
        fputcsv($output, $record);
    }
    fclose($output);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Audit Log | KK Youth Profiling System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" integrity="sha384-XGjxtQfXaH2tnPFa9x+ruJTuLE3Aa6LhHSWRr1XeTyhezb4abCG4ccI5AkVDxqC+" crossorigin="anonymous">
    <link href="sidebar.css?v=32" rel="stylesheet">
    <style>
        :root {
            --line:#e2e8ef;
            --muted:#687586;
            --audit-ink:#27364a;
            --audit-primary:#2874c8;
            --audit-primary-dark:#1d5798;
            --audit-soft:#eff6fd;
            --audit-teal:#188b9a;
            --audit-amber:#b77818;
            --audit-blue:#356bb1;
        }
        * { box-sizing:border-box; }
        body { margin:0; background:var(--bs-tertiary-bg); color:var(--audit-ink); font-family:Arial,sans-serif; }
        .top-strip { height:5px; background:linear-gradient(90deg,var(--audit-primary) 0%,var(--audit-teal) 58%,#83aee0 100%); }
        .header { min-height:70px; background:var(--bs-body-bg); border-bottom:1px solid var(--line); display:flex; align-items:center; justify-content:space-between; padding:0 26px; gap:14px; }
        .header-title { font-size:19px; font-weight:750; color:var(--audit-ink); }
        .content { padding:20px; max-width:1500px; margin:0 auto; }
        .records-card { background:var(--bs-body-bg); border:1px solid var(--line); border-radius:14px; overflow:hidden; }
        .records-inner { padding:16px; }
        .filter-panel { padding:15px 16px; margin-bottom:14px; background:linear-gradient(110deg,#f0f6fd 0%,#f7f9fc 62%,#eff8fa 100%); border:1px solid #e1eaf4; border-left:4px solid var(--audit-primary); border-radius:7px; }
        .filter-grid { display:grid; grid-template-columns:repeat(3,minmax(140px,1fr)); gap:13px 15px; align-items:end; }
        .filter-field label { display:block; margin-bottom:5px; color:#56625b; font-size:12px; font-weight:650; }
        .filter-field .form-control,.filter-field .form-select { min-height:39px; border-color:#d6dcd7; border-radius:5px; background-color:#fff; color:var(--audit-ink); font-size:14px; }
        .filter-field .form-control:focus,.filter-field .form-select:focus { border-color:var(--audit-primary); box-shadow:0 0 0 3px rgba(40,116,200,.14); }
        .filter-note { color:var(--muted); font-size:12px; margin:10px 0 0; }
        .audit-card { overflow:hidden; background:var(--bs-body-bg); border:1px solid var(--line); border-radius:10px; }
        .audit-heading { padding:14px 16px; border-bottom:1px solid var(--line); display:flex; justify-content:space-between; align-items:center; gap:12px; background:var(--bs-body-bg); }
        .audit-heading h2 { font-size:16px; font-weight:700; margin:0; color:var(--audit-ink); display:flex; align-items:center; gap:8px; }
        .audit-heading h2 i { color:var(--audit-teal); }
        .audit-list { overflow-x:auto; }
        .audit-table { width:100%; min-width:860px; margin:0; border-collapse:collapse; font-size:13px; }
        .audit-table th { padding:11px 13px; border:1px solid #dfe5ec; background:#e9edf2; color:#344256; font-size:12px; font-weight:700; text-align:left; white-space:nowrap; }
        .audit-table td { padding:12px 13px; border:1px solid #e3e7ec; color:#344256; vertical-align:top; }
        .audit-table tbody tr { background:var(--bs-body-bg); transition:background-color .15s ease; }
        .audit-table tbody tr:hover { background:#f4f8fd; }
        .audit-user-name { color:var(--audit-ink); font-weight:700; }
        .audit-subline { display:block; margin-top:4px; color:var(--muted); font-size:12px; overflow-wrap:anywhere; }
        .audit-action { font-weight:600; color:var(--audit-ink); }
        .audit-time { white-space:nowrap; color:#4f5c6e; }
        .audit-type { color:#4f5c6e; }
        .audit-details { min-width:230px; }
        .audit-description { overflow-wrap:anywhere; }
        .audit-details-link { display:inline-flex; align-items:center; gap:6px; margin-top:7px; color:var(--audit-primary-dark); font-weight:650; text-decoration:none; white-space:nowrap; }
        .audit-details-link:hover { color:var(--audit-primary); text-decoration:underline; }
        .audit-pagination { display:flex; align-items:center; justify-content:space-between; gap:12px; padding:12px 16px; }
        .audit-pagination .btn { border-color:#d6dcd7; border-radius:5px; color:#56625b; }
        .audit-pagination .btn:hover { border-color:var(--audit-primary); color:var(--audit-primary-dark); }
        .export-link { display:inline-flex; align-items:center; gap:7px; color:var(--audit-primary-dark); font-size:13px; font-weight:650; text-decoration:none; }
        .export-link:hover { color:var(--audit-primary); text-decoration:underline; }
        .footer-note { text-align:center; color:var(--muted); font-size:13px; padding:20px; }
        html[data-bs-theme="dark"] body { background:var(--bs-tertiary-bg); color:var(--bs-body-color); }
        html[data-bs-theme="dark"] .records-card { background:var(--bs-body-bg); }
        html[data-bs-theme="dark"] .header-title,
        html[data-bs-theme="dark"] .audit-heading h2 { color:var(--bs-body-color); }
        html[data-bs-theme="dark"] .filter-field label,
        html[data-bs-theme="dark"] .filter-note,
        html[data-bs-theme="dark"] .activity-account,
        html[data-bs-theme="dark"] .activity-meta-label { color:var(--bs-secondary-color); }
        html[data-bs-theme="dark"] .filter-panel { background:var(--bs-tertiary-bg); border-color:var(--line); }
        html[data-bs-theme="dark"] .filter-field .form-control,
        html[data-bs-theme="dark"] .filter-field .form-select { background-color:var(--bs-body-bg); color:var(--bs-body-color); }
        html[data-bs-theme="dark"] .audit-heading { background:var(--bs-body-bg); }
        html[data-bs-theme="dark"] .audit-card { background:var(--bs-body-bg); }
        html[data-bs-theme="dark"] .audit-table th { background:var(--bs-tertiary-bg); color:var(--bs-body-color); border-color:var(--bs-border-color); }
        html[data-bs-theme="dark"] .audit-table td { color:var(--bs-body-color); border-color:var(--bs-border-color); }
        html[data-bs-theme="dark"] .audit-table tbody tr,
        html[data-bs-theme="dark"] .audit-table tbody tr:hover { background:var(--bs-body-bg); }
        html[data-bs-theme="dark"] .audit-user-name,
        html[data-bs-theme="dark"] .audit-action { color:var(--bs-body-color); }
        html[data-bs-theme="dark"] .audit-subline { color:var(--bs-secondary-color); }
        html[data-bs-theme="dark"] .audit-type { color:var(--bs-secondary-color); }
        html[data-bs-theme="dark"] .audit-details-link { color:#b3d4ff; }
        html[data-bs-theme="dark"] .audit-details-link:hover { color:#fff; }
        @media(max-width:767px) {
            .content { padding:16px 14px; }
            .header { padding:0 16px; }
            .records-inner { padding:12px; }
            .audit-heading { align-items:flex-start; flex-direction:column; }
            .audit-pagination { align-items:flex-start; flex-direction:column; }
            .filter-grid { grid-template-columns:repeat(2,minmax(0,1fr)); }
        }
        @media(max-width:480px) { .filter-grid { grid-template-columns:1fr; } }
    </style>
    <?php include __DIR__ . '/init.php'; ?>
    <?php
    try {
        $pdo = db();
        $countStatement = $pdo->prepare('SELECT COUNT(*) FROM audit_log' . $whereSql);
        $countStatement->execute($params);
        $auditCount = (int)$countStatement->fetchColumn();
        $pageCount = max(1, (int)ceil($auditCount / $pageSize));
        $page = min($page, $pageCount);
        $offset = ($page - 1) * $pageSize;
        $statement = $pdo->prepare('SELECT id, actor, actor_full_name, target_legacy_id, action, description, device_type, created_at
            FROM audit_log' . $whereSql . '
            ORDER BY created_at DESC, id DESC
            LIMIT ? OFFSET ?');
        $queryParams = array_merge($params, [$pageSize, $offset]);
        foreach ($queryParams as $index => $value) {
            $type = $index >= count($params) ? PDO::PARAM_INT : PDO::PARAM_STR;
            $statement->bindValue($index + 1, $value, $type);
        }
        $statement->execute();
        $auditRows = $statement->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $error) {
        error_log('Audit log page could not load: ' . $error->getMessage());
        http_response_code(500);
        $auditError = 'The audit log could not be loaded. Confirm that the audit activity metadata migration has been applied.';
        $pageCount = 1;
    }
    $firstRecord = $auditRows ? (($page - 1) * $pageSize + 1) : 0;
    $lastRecord = $auditRows ? min($page * $pageSize, $auditCount) : 0;
    ?>
</head>
<body>
<div class="top-strip"></div>
<script src="theme.js"></script>
<script src="sidebar.js?v=16"></script>
<main class="main page-content">
    <header class="header">
        <div class="d-flex align-items-center gap-2">
            <button class="btn btn-sm sidebar-toggle d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebar" aria-controls="sidebar" aria-label="Open menu"><i class="bi bi-list fs-5"></i></button>
            <div>
                <div class="header-title">Audit Log</div>
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
        <section class="records-card">
        <div class="records-inner">
        <form class="filter-panel" method="get" action="audit-log.php" aria-label="Filter audit activities">
            <div class="filter-grid">
                <div class="filter-field">
                    <label for="filterStart">Date from</label>
                    <input class="form-control" type="date" id="filterStart" name="start" value="<?= e($filters['start']) ?>" onchange="this.form.requestSubmit()">
                </div>
                <div class="filter-field">
                    <label for="filterEnd">Date to</label>
                    <input class="form-control" type="date" id="filterEnd" name="end" value="<?= e($filters['end']) ?>" onchange="this.form.requestSubmit()">
                </div>
                <div class="filter-field">
                    <label for="filterUser">Member or username</label>
                    <input class="form-control" type="search" id="filterUser" name="user" maxlength="120" value="<?= e($filters['user']) ?>" placeholder="Search user" oninput="clearTimeout(this.form.searchTimer); this.form.searchTimer=setTimeout(() => this.form.requestSubmit(), 500)" onsearch="this.form.requestSubmit()">
                </div>
            </div>
            <p class="filter-note mb-0">Login coordinates are recorded when the browser grants location access. Denying access does not block login.</p>
        </form>

        <div class="d-flex justify-content-end mb-2">
            <a class="export-link" href="audit-log.php?<?= e($filterQuery ? $filterQuery . '&' : '') ?>export=csv"><i class="bi bi-download" aria-hidden="true"></i>Download CSV</a>
        </div>

        <section class="audit-card" aria-labelledby="auditHeading">
            <div class="audit-heading">
                <h2 id="auditHeading"><i class="bi bi-clock-history" aria-hidden="true"></i>Recorded activity</h2>
                <?php if ($auditError === ''): ?>
                    <span class="small text-muted">Showing <?= (int)$firstRecord ?>–<?= (int)$lastRecord ?> of <?= (int)$auditCount ?> entries</span>
                <?php endif; ?>
            </div>
            <?php if ($auditError !== ''): ?>
                <div class="alert alert-danger m-3 mb-0"><?= e($auditError) ?></div>
            <?php else: ?>
                <div class="audit-list">
                    <?php if (!$auditRows): ?>
                        <div class="text-center text-muted py-5">No audit entries match your search.</div>
                    <?php else: ?>
                        <table class="audit-table">
                            <thead>
                                <tr>
                                    <th scope="col">Time</th>
                                    <th scope="col">User</th>
                                    <th scope="col">Action</th>
                                    <th scope="col">Device Type</th>
                                    <th scope="col">Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($auditRows as $row): ?>
                                    <tr>
                                        <td class="audit-time">
                                            <i class="bi bi-clock me-1" aria-hidden="true"></i><?= e($row['created_at']) ?>
                                        </td>
                                        <td>
                                            <div class="audit-user-name"><?= e($row['actor_full_name'] ?: $row['actor']) ?></div>
                                            <small class="audit-subline"><?= e($row['actor']) ?></small>
                                        </td>
                                        <td><span class="audit-action"><?= e(ucwords(str_replace('_', ' ', $row['action']))) ?></span></td>
                                        <td>
                                            <span class="audit-type"><?= e($row['device_type'] ?: 'Device unknown') ?></span>
                                        </td>
                                        <td class="audit-details">
                                            <div class="audit-description"><?= e($row['description'] ?: 'Activity recorded') ?></div>
                                            <small class="audit-subline">Affected: <?= e($row['target_legacy_id'] ?? '—') ?></small>
                                            <a class="audit-details-link" href="audit-log-detail.php?id=<?= (int)$row['id'] ?>">View details <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
                <nav class="audit-pagination" aria-label="Audit log pages">
                    <span class="small text-muted">Page <?= (int)$page ?> of <?= (int)$pageCount ?></span>
                    <div class="d-flex gap-2">
                        <?php if ($page > 1): ?>
                            <a class="btn btn-outline-secondary btn-sm" href="audit-log.php?<?= e($filterQuery ? $filterQuery . '&' : '') ?>page=<?= (int)($page - 1) ?>">Previous</a>
                        <?php endif; ?>
                        <?php if ($page < $pageCount): ?>
                            <a class="btn btn-outline-secondary btn-sm" href="audit-log.php?<?= e($filterQuery ? $filterQuery . '&' : '') ?>page=<?= (int)($page + 1) ?>">Next</a>
                        <?php endif; ?>
                    </div>
                </nav>
            <?php endif; ?>
        </section>
        </div>
        </section>
        <div class="footer-note">KK Youth Profiling System &bull; SK Barangay Sikat, Alfonso, Cavite</div>
    </div>
</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
