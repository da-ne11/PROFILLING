<?php
require_once __DIR__ . '/config.php';
require_official();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review Requests | KK Youth Profiling System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" integrity="sha384-tViUnnbYAV00FLIhhi3v/dWt3Jxw4gZQcNoSCxCIFNJVCx7/D55/wXsrNIRANwdD" crossorigin="anonymous">
    <link href="sidebar.css?v=32" rel="stylesheet">
    <style>
        /* ================= TOKENS: solid colors, light + dark ================= */
        :root, body {
            --rq-bg: #f5f7fb;
            --rq-surface: #ffffff;
            --rq-surface-2: #f8fafc;
            --rq-line: #e5e7eb;
            --rq-text: #1f2937;
            --rq-muted: #6b7280;
            --rq-accent: #2563eb;
            --rq-select: #eff6ff;
            --rq-danger: #dc3545;
            color-scheme: light;
        }
        [data-bs-theme="dark"], [data-theme="dark"], html.dark, html.dark-mode, body.dark, body.dark-mode,
        [data-bs-theme="dark"] body, [data-theme="dark"] body, html.dark body, html.dark-mode body {
            --rq-bg: #111827;
            --rq-surface: #1f2937;
            --rq-surface-2: #273449;
            --rq-line: #374151;
            --rq-text: #f3f4f6;
            --rq-muted: #9ca3af;
            --rq-accent: #93c5fd;
            --rq-select: #253247;
            --rq-danger: #f87171;
            color-scheme: dark;
        }
        * { box-sizing: border-box; }
        body { margin: 0; background: var(--rq-bg); color: var(--rq-text); font-family: Arial, sans-serif; }
        body.review-requests-page .page-content { background: var(--rq-bg); }

        /* ================= HEADER ================= */
        .header { min-height: 64px; background: var(--rq-surface); border-bottom: 1px solid var(--rq-line); display: flex; align-items: center; justify-content: space-between; padding: 0 26px; gap: 14px; }
        .header-title { font-size: 16px; font-weight: 700; color: var(--rq-text); }

        /* ================= PAGE ================= */
        body.review-requests-page .page-content > .content { width: 100%; max-width: none; margin: 0; padding: 1rem !important; background: var(--rq-bg); }
        @media (min-width: 992px) { body.review-requests-page .page-content > .content { padding: 1.5rem !important; } }
        .page-head { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 20px; padding: 16px 20px; background: var(--rq-surface); border: 1px solid var(--rq-line); border-radius: 14px; }
        .page-head h1 { font-size: 28px; font-weight: 800; margin: 0 0 4px; color: var(--rq-text); }
        .page-head p { margin: 0; color: var(--rq-muted); font-size: 14px; }
        .btn-export { display: inline-flex; align-items: center; gap: 8px; background: var(--rq-surface); color: var(--rq-text); border: 1px solid var(--rq-line); border-radius: 10px; padding: 9px 16px; font-size: 14px; font-weight: 600; }
        .btn-export:hover { background: var(--rq-surface-2); }

        /* ================= SUMMARY CARDS ================= */
        .review-summary-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 12px; margin-bottom: 16px; }
        .review-summary-card { position: relative; display: block; width: 100%; min-height: 130px; text-align: left; padding: 16px; background: var(--rq-surface); color: var(--rq-text); border: 1px solid var(--rq-line); border-radius: 18px; transition: border-color .18s ease, background .18s ease; }
        .review-summary-card:hover { border-color: var(--rq-accent); }
        .review-summary-card.active { border-color: var(--rq-accent); background: var(--rq-surface-2); }
        .review-summary-label { display: block; color: var(--rq-muted); font-size: 13px; }
        .review-summary-number { display: block; font-size: 28px; font-weight: 800; line-height: 1.2; margin: 2px 0; }
        .review-summary-icon { position: absolute; top: 12px; right: 12px; color: var(--rq-muted); font-size: 1rem; }

        /* ================= FILTERS ================= */
        .filter-bar { display: grid; grid-template-columns: minmax(220px, 2fr) minmax(150px, 1fr) minmax(190px, 1fr) minmax(130px, .8fr); gap: 12px; margin-bottom: 12px; }
        .filter-bar .form-control, .filter-bar .form-select, .filter-bar .dropdown-toggle { height: 42px; background-color: var(--rq-surface); border: 1px solid var(--rq-line); border-radius: 10px; font-size: 14px; color: var(--rq-text); }
        .filter-bar .form-select { background-color: var(--rq-surface); }
        .filter-bar .form-control::placeholder { color: var(--rq-muted); }
        .search-wrap { position: relative; }
        .search-wrap i { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--rq-muted); }
        .search-wrap .form-control { padding-left: 38px; }
        .date-toggle, .more-toggle { width: 100%; text-align: left; display: flex; align-items: center; gap: 8px; padding: 0 14px; }
        .date-toggle::after { margin-left: auto; }
        .more-toggle::after { display: none; }
        .date-menu { min-width: 280px; padding: 12px; background: var(--rq-surface); color: var(--rq-text); border: 1px solid var(--rq-line); border-radius: 12px; }
        .date-menu .form-control { height: 36px; font-size: 13px; background: var(--rq-surface-2); border: 1px solid var(--rq-line); color: var(--rq-text); }
        .date-menu .btn { font-size: 12px; font-weight: 600; background: var(--rq-surface-2); border: 1px solid var(--rq-line); color: var(--rq-text); }
        .date-menu .btn.active { background: var(--rq-accent); border-color: var(--rq-accent); color: #fff; }
        .status-row { display: flex; flex-wrap: wrap; align-items: center; gap: 8px; margin-bottom: 18px; }
        .status-row .lbl { color: var(--rq-muted); font-size: 13px; margin-right: 4px; }
        .status-chip { background: var(--rq-surface); color: var(--rq-text); border: 1px solid var(--rq-line); border-radius: 8px; padding: 5px 14px; font-size: 13px; }
        .status-chip.active { background: var(--rq-select); border-color: var(--rq-accent); }
        .clear-link { margin-left: auto; background: none; border: 0; color: var(--rq-accent); font-size: 13px; }
        #requestDateLabel { font-size: 12px; color: var(--rq-muted); }

        /* ================= WORKSPACE ================= */
        .workspace { display: grid; grid-template-columns: minmax(0, 1.7fr) minmax(440px, 1fr); gap: 18px; align-items: stretch; }
        .panel { background: var(--rq-surface); color: var(--rq-text); border: 1px solid var(--rq-line); border-radius: 14px; overflow: hidden; }
        .panel-head { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 8px; padding: 16px 20px 0; }
        .request-list-head { display: grid; grid-template-columns: minmax(0, 1fr) auto auto; align-items: center; gap: 12px; padding: 14px 16px; background: var(--rq-surface-2); border-bottom: 1px solid var(--rq-line); }
        .panel-title { font-size: 17px; font-weight: 700; display: flex; align-items: center; gap: 10px; margin: 0; color: var(--rq-text); }
        .count-pill { background: var(--rq-select); color: var(--rq-text); border-radius: 6px; padding: 1px 8px; font-size: 12px; font-weight: 700; }
        .accept-all-btn { min-width: 120px; height: 42px; border: 0; border-radius: 8px; background: #2563eb; color: #fff; font-size: 13px; font-weight: 700; padding: 0 16px; white-space: nowrap; }
        .accept-all-btn:hover { filter: brightness(1.08); }
        .accept-all-btn:disabled { opacity: .55; cursor: not-allowed; }
        .sort-select { width: 150px; height: 42px; padding: 0 30px 0 16px; font-size: 13px; background-color: var(--rq-surface); color: var(--rq-text); border: 1px solid var(--rq-line); border-radius: 8px; }
        .link-btn { background: none; border: 0; color: var(--rq-accent); font-size: 13px; padding: 0; }

        /* table */
        .table-responsive { background: var(--rq-surface); }
        .table { --bs-table-color: var(--rq-text); --bs-table-bg: var(--rq-surface); --bs-table-hover-bg: var(--rq-surface-2); --bs-table-hover-color: var(--rq-text); --bs-table-border-color: var(--rq-line); font-size: 14px; margin: 0; }
        .table th { background: var(--rq-surface-2); color: var(--rq-muted); font-size: 12px; font-weight: 600; white-space: nowrap; padding: 12px 16px; }
        .request-table { min-width: 720px; table-layout: fixed; }
        .request-table col:nth-child(1) { width: 22%; }
        .request-table col:nth-child(2) { width: 15%; }
        .request-table col:nth-child(3) { width: 32%; }
        .request-table col:nth-child(4) { width: 13%; }
        .request-table col:nth-child(5) { width: 18%; }
        .request-list-panel, .detail { min-width: 0; }
        .request-table-wrap { width: calc(100% - 32px); max-height: 520px; overflow: auto; border: 1px solid var(--rq-line); border-radius: 8px; }
        body.review-requests-page .page-content .request-list-panel .request-table-wrap { margin: 0 auto; }
        .request-list-panel .request-table > thead > tr > th,
        .request-list-panel .request-table > tbody > tr > td { padding: 12px 16px; }
        .table td { padding: 12px 16px; vertical-align: middle; }
        .table tbody tr { cursor: pointer; }
        .table tbody tr.is-selected { --bs-table-bg: var(--rq-select); --bs-table-hover-bg: var(--rq-select); }
        .table tbody tr.is-selected td:first-child { box-shadow: inset 3px 0 0 var(--rq-accent), inset 0 0 0 9999px var(--bs-table-bg-state, var(--bs-table-bg)); }
        #requestsBody .btn { background: var(--rq-surface-2); border: 1px solid var(--rq-line); color: var(--rq-text); border-radius: 8px; font-size: 13px; font-weight: 600; padding: 6px 14px; }
        #requestsBody .btn::after { content: "\F285"; font-family: bootstrap-icons; margin-left: 6px; font-size: 11px; }
        #requestsBody tr.is-selected .btn { background: var(--rq-accent); border-color: var(--rq-accent); color: #fff; }
        #requestsBody .badge { border-radius: 6px; font-weight: 600; font-size: 11px; }
        .rq-change-preview { overflow: hidden; font-size: 12px; line-height: 1.5; text-overflow: ellipsis; white-space: nowrap; }
        .rq-change-preview strong { font-weight: 700; }
        .rq-change-old { color: var(--rq-muted); }
        .rq-change-new { color: var(--rq-text); font-weight: 600; }
        .rq-check-btn { width: 42px; height: 42px; display: inline-flex; align-items: center; justify-content: center; padding: 0 !important; margin-right: 8px; color: #93c5fd !important; border: 1px solid #93c5fd !important; border-radius: 8px !important; background: rgba(147, 197, 253, .08) !important; }
        #requestsBody .rq-check-btn::after { content: none; margin: 0; }
        .rq-check-btn i { font-size: 18px; }
        .rq-check-btn.is-selected { background: #2563eb !important; border-color: #2563eb !important; color: #fff !important; }
        .rq-check-btn:disabled { opacity: .45; cursor: not-allowed; }
        .rq-age { margin-left: 8px; font-size: 11px; color: #d6a62b; }
        .table-foot { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 8px; padding: 12px 20px; font-size: 12px; color: var(--rq-muted); border-top: 1px solid var(--rq-line); background: var(--rq-surface); }
        .request-list-panel .table-foot { width: calc(100% - 32px); margin: 0 auto; padding: 12px 16px; }
        .pager { display: flex; gap: 6px; }
        .pg { min-width: 28px; height: 28px; border-radius: 6px; border: 1px solid var(--rq-line); background: var(--rq-surface); color: var(--rq-text); font-size: 12px; }
        .pg.active { background: var(--rq-select); border-color: var(--rq-accent); }
        .pg:disabled { opacity: .4; }
        .empty-row td { text-align: center; color: var(--rq-muted); padding: 36px 16px; cursor: default; }

        /* ================= DETAILS PANEL ================= */
        .detail { position: sticky; top: 16px; height: 100%; display: flex; flex-direction: column; }
        #detailView:not([hidden]) { display: flex; flex: 1; min-height: 0; flex-direction: column; }
        .detail-body { flex: 1; min-height: 0; padding: 4px 20px 16px; }
        .detail-name { font-size: 22px; font-weight: 800; margin: 0; color: var(--rq-text); }
        .detail-sub { font-size: 12px; color: var(--rq-muted); margin-top: 2px; }
        .detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px 16px; margin: 16px 0 0; padding-top: 16px; border-top: 1px solid var(--rq-line); }
        .detail-grid dt { font-size: 11px; color: var(--rq-muted); font-weight: 600; margin: 0 0 2px; }
        .detail-grid dd { font-size: 14px; margin: 0; word-break: break-word; }
        .section-title { font-size: 13px; font-weight: 700; margin: 18px 0 8px; }
        .detail-changes { min-height: 220px; max-height: 360px; overflow: auto; background: var(--rq-surface-2); border: 1px solid var(--rq-line); border-radius: 10px; padding: 10px 12px; font-size: 13px; color: var(--rq-text); }
        .detail-changes .table { --bs-table-bg: transparent; font-size: 12px; }
        .detail-changes .table th { background: transparent; }
        .detail-changes .card, .detail-changes .alert, .detail-changes .list-group-item { background: transparent; color: var(--rq-text); border-color: var(--rq-line); }
        .detail-changes > :last-child { margin-bottom: 0; }
        .detail-actions { display: grid; grid-template-columns: 1fr 2fr; gap: 10px; margin-top: auto; padding: 14px 20px; border-top: 1px solid var(--rq-line); background: var(--rq-surface); }
        .btn-reject { background: var(--rq-surface-2); color: var(--rq-danger); border: 1px solid var(--rq-line); border-radius: 8px; font-weight: 700; padding: 9px 14px; }
        .btn-reject:hover { border-color: var(--rq-danger); }
        .btn-approve { background: var(--rq-accent); color: #fff; border: 0; border-radius: 8px; font-weight: 700; padding: 9px 14px; }
        .btn-approve:hover { filter: brightness(1.1); }
        .detail-empty { flex: 1; min-height: 320px; display: grid; align-content: center; padding: 48px 24px; text-align: center; color: var(--rq-muted); font-size: 14px; }
        .detail-empty i { font-size: 32px; display: block; margin-bottom: 8px; }

        /* modal buttons used by request.js */
        .btn-outline { background: var(--rq-surface); color: var(--rq-text); border: 1px solid var(--rq-line); padding: 8px 14px; border-radius: 8px; font-weight: 700; }
        .btn-black { background: #8b2c36; color: #fff; border: 0; padding: 8px 14px; border-radius: 8px; font-weight: 700; }
        .btn-black:hover { background: #a3333f; color: #fff; }
        .footer-note { text-align: center; color: var(--rq-muted); font-size: 13px; padding: 20px; }
        #toastBox { position: fixed; right: 20px; bottom: 20px; z-index: 2000; width: min(400px, calc(100% - 40px)); }

        /* the details panel reads data from the review modal without showing it */
        body.rq-silent { overflow: auto !important; padding-right: 0 !important; }
        body.rq-silent .modal, body.rq-silent .modal .modal-dialog { transition: none !important; opacity: 0; pointer-events: none; }
        body.rq-silent .modal-backdrop { display: none !important; }

        /* ================= RESPONSIVE ================= */
        @media (max-width: 1450px) { .workspace { grid-template-columns: 1fr; } .detail { position: static; height: auto; } #detailView:not([hidden]) { display: block; } .detail-body { overflow: visible; } }
        @media (max-width: 1200px) { .filter-bar { grid-template-columns: 1fr 1fr; } .search-wrap { grid-column: 1 / -1; } }
        @media (max-width: 991px) { .review-summary-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); } }
        @media (max-width: 575px) { .header { padding: 0 16px; } .review-summary-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } .filter-bar { grid-template-columns: 1fr; } .clear-link { margin-left: 0; } .request-list-head { grid-template-columns: 1fr; } .accept-all-btn, .sort-select { width: 100%; } .request-table { min-width: 680px; } }
    </style>
    <?php include __DIR__ . '/init.php'; ?>
    <?php
    $requestEditItems = json_decode($__data['kkEditRequests'] ?? '[]', true);
    $requestEditItems = is_array($requestEditItems)
        ? array_filter($requestEditItems, function ($request) {
            return is_array($request) && ($request['status'] ?? '') === 'Edit Request';
        })
        : [];
    $requestAttentionCount = count($requestEditItems);
    ?>
    <script>
        window.SK_CAN_REVIEW_MEMBER_REQUESTS = <?= in_array(urole(), MEMBER_DATA_ADMIN_ROLES, true) ? 'true' : 'false' ?>;
    </script>
</head>
<body class="review-requests-page">
<div class="top-strip"></div>
<script src="theme.js"></script>
<script src="sidebar.js?v=16"></script>
<main class="main page-content">
    <header class="header">
        <div class="d-flex align-items-center gap-2">
            <button class="btn btn-sm sidebar-toggle d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebar" aria-controls="sidebar" aria-label="Open menu"><i class="bi bi-list fs-5"></i></button>
            <div><div class="header-title">KK Youth Profiling System</div></div>
        </div>
        <div class="header-account-area">
            <a href="request.php" class="header-notification" aria-label="<?= (int)$requestAttentionCount ?> profile edits need review">
                <i class="bi bi-bell" aria-hidden="true"></i>
                <?php if ($requestAttentionCount > 0): ?><span class="header-notification-count"><?= (int)$requestAttentionCount ?></span><?php endif; ?>
            </a>
            <span class="header-avatar"><?= e(uinit()) ?></span>
            <div class="d-none d-md-block header-identity"><div class="header-identity-name"><?= e(uname()) ?></div><div class="header-identity-role"><?= e(strtoupper(urole())) ?></div></div>
            <a href="logout.php" class="btn btn-sm header-logout"><i class="bi bi-box-arrow-right me-1"></i>Logout</a>
        </div>
    </header>

    <div class="content">
        <div class="page-head">
            <div>
                <h1>Review requests</h1>
                <p>Review member profile update requests, all in one place.</p>
            </div>
            <button type="button" class="btn-export" id="exportRequests"><i class="bi bi-download"></i>Export requests</button>
        </div>

        <div class="review-summary-grid" aria-label="Request totals">
            <button type="button" class="review-summary-card active" style="--review-accent:#d6a62b" data-request-status="pending"><span class="review-summary-icon"><i class="bi bi-clock"></i></span><span class="review-summary-label">Pending requests</span><span class="review-summary-number" id="pendingRequestTotal">0</span></button>
            <button type="button" class="review-summary-card" style="--review-accent:#2fb67c" data-request-status="approved"><span class="review-summary-icon"><i class="bi bi-check-lg"></i></span><span class="review-summary-label">Approved</span><span class="review-summary-number" id="approvedRequestTotal">0</span></button>
            <button type="button" class="review-summary-card" style="--review-accent:#e5565b" data-request-status="rejected"><span class="review-summary-icon"><i class="bi bi-x-lg"></i></span><span class="review-summary-label">Rejected</span><span class="review-summary-number" id="rejectedRequestTotal">0</span></button>
            <button type="button" class="review-summary-card" style="--review-accent:#8a97b2" data-request-status="all"><span class="review-summary-icon"><i class="bi bi-file-earmark-text"></i></span><span class="review-summary-label">All requests</span><span class="review-summary-number" id="allRequestTotal">0</span></button>
        </div>

        <div class="filter-bar" aria-label="Filter requests">
            <div class="search-wrap">
                <i class="bi bi-search" aria-hidden="true"></i>
                <input type="search" class="form-control" id="requestSearch" placeholder="Search by name or request ID" aria-label="Search requests">
            </div>
            <select class="form-select" id="requestPurok" aria-label="Filter by purok">
                <option value="">All puroks</option>
                <option value="Purok 1">Purok 1</option>
                <option value="Purok 2">Purok 2</option>
                <option value="Purok 3">Purok 3</option>
                <option value="Purok 4">Purok 4</option>
                <option value="Purok 5">Purok 5</option>
                <option value="Purok 6">Purok 6</option>
                <option value="Purok 7">Purok 7</option>
            </select>
            <div class="dropdown">
                <button class="dropdown-toggle date-toggle" type="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false"><i class="bi bi-calendar3"></i><span id="dateToggleText">All dates</span></button>
                <div class="dropdown-menu date-menu" aria-label="Filter requests by date">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <label class="visually-hidden" for="requestDateFrom">From date</label>
                        <input class="form-control" type="date" id="requestDateFrom" aria-label="From date">
                        <span class="small" style="color:var(--rq-muted)">to</span>
                        <label class="visually-hidden" for="requestDateTo">To date</label>
                        <input class="form-control" type="date" id="requestDateTo" aria-label="To date">
                    </div>
                    <div class="d-flex flex-wrap gap-1">
                        <button type="button" class="btn btn-sm" data-request-range="today">Today</button>
                        <button type="button" class="btn btn-sm" data-request-range="week">This week</button>
                        <button type="button" class="btn btn-sm" data-request-range="first-half">First half</button>
                        <button type="button" class="btn btn-sm" data-request-range="second-half">Second half</button>
                        <button type="button" class="btn btn-sm" data-request-range="month">This month</button>
                        <button type="button" class="btn btn-sm active" data-request-range="all">Show all dates</button>
                    </div>
                </div>
            </div>
            <div class="dropdown">
                <button class="dropdown-toggle more-toggle" type="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false"><i class="bi bi-sliders"></i>More filters</button>
                <div class="dropdown-menu date-menu dropdown-menu-end">
                    <div class="form-check"><input class="form-check-input" type="checkbox" id="agedOnly"><label class="form-check-label" for="agedOnly">Waiting 3+ days only</label></div>
                </div>
            </div>
        </div>
        <div class="status-row" aria-label="Status filter">
            <span class="lbl">Status</span>
            <button type="button" class="status-chip active" data-chip-status="pending">Pending <span data-chip-count="pendingRequestTotal"></span></button>
            <button type="button" class="status-chip" data-chip-status="approved">Approved <span data-chip-count="approvedRequestTotal"></span></button>
            <button type="button" class="status-chip" data-chip-status="rejected">Rejected <span data-chip-count="rejectedRequestTotal"></span></button>
            <button type="button" class="status-chip" data-chip-status="all">All <span data-chip-count="allRequestTotal"></span></button>
            <span id="requestDateLabel" class="ms-2">Showing requests from all dates</span>
            <button type="button" class="clear-link" id="clearFilters">Clear filters</button>
        </div>

        <div class="workspace">
            <section class="panel request-list-panel">
                <div class="panel-head request-list-head">
                    <h2 class="panel-title">Requests to review <span class="count-pill" id="requestTotalCount">0 pending</span></h2>
                    <button type="button" class="accept-all-btn" id="acceptAllRequests" disabled>Accept all (0)</button>
                    <select class="form-select sort-select" id="requestSort" aria-label="Sort requests"><option value="desc">Newest first</option><option value="asc">Oldest first</option></select>
                </div>
                <div class="table-responsive request-table-wrap"><table class="table table-hover align-middle request-table">
                    <colgroup><col><col><col><col><col></colgroup>
                    <thead><tr><th>Member</th><th>Requested On</th><th>Changes</th><th>Status</th><th class="text-end"><label class="d-inline-flex align-items-center gap-1"><input type="checkbox" id="selectAllRequests" aria-label="Select all pending requests">Select all</label></th></tr></thead><tbody id="requestsBody"></tbody>
                </table></div>
                <div class="table-foot"><span id="tableFoot">Showing 0 requests</span><div class="pager" id="pager"></div></div>
            </section>

            <aside class="panel detail" aria-live="polite">
                <div class="panel-head pb-3"><h2 class="panel-title">Request details</h2><button type="button" class="link-btn" id="detailFull">View full review</button></div>
                <div id="detailEmpty" class="detail-empty"><i class="bi bi-inbox"></i>Select a request to see its details.</div>
                <div id="detailView" hidden>
                    <div class="detail-body">
                        <div class="d-flex justify-content-between align-items-start gap-2">
                            <div><h3 class="detail-name" id="dName">-</h3><div class="detail-sub" id="dSub">-</div></div>
                            <span id="dStatus"></span>
                        </div>
                        <dl class="detail-grid" id="dGrid"></dl>
                        <div class="section-title">Changes requested</div>
                        <div class="detail-changes" id="dChanges"></div>
                    </div>
                    <div class="detail-actions" id="detailActions">
                        <button type="button" class="btn-reject" id="detailReject">Reject</button>
                        <button type="button" class="btn-approve" id="detailApprove"><i class="bi bi-check-lg me-1"></i>Approve request</button>
                    </div>
                </div>
            </aside>
        </div>

        <div class="footer-note">KK Youth Profiling System &bull; SK Barangay Sikat, Alfonso, Cavite &bull; FY 2026&ndash;2027</div>
    </div>
</main>

<div id="toastBox" aria-live="polite"></div>
<div class="modal fade" id="reviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable"><div class="modal-content">
        <div class="modal-header bg-dark text-white"><h6 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i>Edit Request</h6><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button></div>
        <div class="modal-body" id="reviewBody"></div>
        <div class="modal-footer"><button type="button" class="btn-outline" data-bs-dismiss="modal">Close</button><button type="button" class="btn-black" id="rejectBtn"><i class="bi bi-x-circle"></i> Reject Changes</button><button type="button" class="btn btn-primary" id="approveBtn"><i class="bi bi-check-circle"></i> Approve Changes</button></div>
    </div></div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script src="request.js?v=<?= filemtime(__DIR__ . '/request.js') ?>"></script>
<script>
/* Layout + details-panel logic. Runs after request.js and only reads the rows it draws.
   Approve / Reject in the panel reuse request.js's own modal buttons. */
(function () {
    var $ = function (s, r) { return (r || document).querySelector(s); };
    var $$ = function (s, r) { return Array.prototype.slice.call((r || document).querySelectorAll(s)); };
    var PER_PAGE = 6, page = 1, selected = null, lastMatches = [], observer = null, refreshing = false;
    var search = $('#requestSearch'), purokSel = $('#requestPurok'), agedOnly = $('#agedOnly'), sortSel = $('#requestSort'), requestBody = $('#requestsBody');

    /* ---------- helpers ---------- */
    function rowsOf(body) { return $$('tr', body).filter(function (r) { return r.cells.length > 1; }); }
    function esc(v) { var d = document.createElement('div'); d.textContent = v || ''; return d.innerHTML; }
    function cellLines(cell) { return (cell ? cell.innerText : '').split('\n').map(function (t) { return t.trim(); }).filter(Boolean); }
    function rowDate(row) { var t = Date.parse(row.cells[1].innerText.replace(/\s+/g, ' ').trim()); return isNaN(t) ? null : t; }
    function purokOf(row) {
        var value = String(row.dataset.purok || '').trim();
        var match = /^(?:purok\s*)?([1-7])$/i.exec(value);
        return match ? 'Purok ' + match[1] : value;
    }
    function isPending(row) { return !!row.cells[3] && /pending|edit request/i.test(row.cells[3].innerText); }
    function ageDays(row) { var t = rowDate(row); return t === null ? null : Math.floor((Date.now() - t) / 86400000); }
    function isAged(row) { return isPending(row) && (ageDays(row) || 0) >= 3; }

    /* ---------- sort and age tag ---------- */
    function sortRows() {
        var rows = rowsOf(requestBody);
        if (!rows.length || rows.some(function (r) { return rowDate(r) === null; })) return;
        var dir = sortSel.value === 'asc' ? 1 : -1;
        var sorted = rows.map(function (r, i) { return { r: r, i: i, t: rowDate(r) }; })
            .sort(function (a, b) { return ((a.t - b.t) * dir) || (a.i - b.i); })
            .map(function (x) { return x.r; });
        if (sorted.some(function (r, i) { return r !== rows[i]; })) {
            sorted.forEach(function (r) { requestBody.appendChild(r); });
            if (observer) observer.takeRecords();
        }
    }
    function tagAges() {
        rowsOf(requestBody).forEach(function (r) {
            var cell = r.cells[3];
            if (!cell || cell.querySelector('.rq-age') || !isAged(r)) return;
            var s = document.createElement('span');
            s.className = 'rq-age'; s.textContent = ageDays(r) + 'd';
            cell.appendChild(s);
        });
    }
    /* ---------- filter + pagination ---------- */
    function applyFilters() {
        var q = search.value.trim().toLowerCase(), purok = purokSel.value, aged = agedOnly.checked;
        var rows = rowsOf(requestBody);
        lastMatches = rows.filter(function (r) {
            return (!q || r.innerText.toLowerCase().indexOf(q) !== -1) &&
                   (!purok || purokOf(r) === purok) && (!aged || isAged(r));
        });
        var pages = Math.max(1, Math.ceil(lastMatches.length / PER_PAGE));
        if (page > pages) page = pages;
        rows.forEach(function (r) {
            var i = lastMatches.indexOf(r);
            r.style.display = (i >= (page - 1) * PER_PAGE && i < page * PER_PAGE) ? '' : 'none';
        });
        $('#tableFoot').textContent = 'Showing ' + $$('tr', requestBody).filter(function (r) { return r.style.display !== 'none' && r.cells.length > 1; }).length + ' of ' + lastMatches.length + ' profile edits';
        var h = '<button class="pg" data-pg="prev" aria-label="Previous page"' + (page <= 1 ? ' disabled' : '') + '><i class="bi bi-chevron-left"></i></button>';
        for (var i = 1; i <= pages; i++) h += '<button class="pg' + (i === page ? ' active' : '') + '" data-pg="' + i + '">' + i + '</button>';
        h += '<button class="pg" data-pg="next" aria-label="Next page"' + (page >= pages ? ' disabled' : '') + '><i class="bi bi-chevron-right"></i></button>';
        $('#pager').innerHTML = pages > 1 ? h : '';
        if (selected && selected.style.display === 'none') clearSelection();
    }

    /* ---------- silent access to request.js's review modal ---------- */
    var busy = false, queued = null;
    function openFor(row, onShown, keepOpen) {
        var btn = row && row.querySelector('[data-act="review"]');
        if (!btn) return;
        if (busy) { queued = { row: row, onShown: onShown, keepOpen: keepOpen }; return; }
        busy = true;
        var modalEl = $('#reviewModal'), inst = bootstrap.Modal.getOrCreateInstance(modalEl), finished = false, timer;
        document.body.classList.add('rq-silent');
        function cleanup() {
            if (finished) return;
            finished = true; clearTimeout(timer);
            modalEl.removeEventListener('shown.bs.modal', shown);
            modalEl.removeEventListener('hidden.bs.modal', cleanup);
            document.body.classList.remove('rq-silent');
            busy = false;
            if (queued) { var q = queued; queued = null; openFor(q.row, q.onShown, q.keepOpen); }
        }
        function shown() {
            onShown(modalEl);
            if (keepOpen) setTimeout(cleanup, 400); else inst.hide();
        }
        timer = setTimeout(cleanup, 2500);
        modalEl.addEventListener('shown.bs.modal', shown);
        modalEl.addEventListener('hidden.bs.modal', cleanup);
        btn.click();
    }

    /* ---------- details panel ---------- */
    function clearSelection() {
        if (selected) selected.classList.remove('is-selected');
        selected = null;
        $('#detailView').hidden = true; $('#detailEmpty').hidden = false;
    }
    function select(row) {
        if (selected) selected.classList.remove('is-selected');
        selected = row; row.classList.add('is-selected');
        var first = cellLines(row.cells[0]);
        $('#dName').textContent = first[0] || '-';
        $('#dSub').textContent = (first[1] ? first[1] + ' \u00b7 ' : '') + 'Profile edit';
        $('#dStatus').innerHTML = row.cells[3] ? row.cells[3].innerHTML : '';
        $$('.rq-age', $('#dStatus')).forEach(function (n) { n.remove(); });
        var changedFieldCount = row.cells[2] ? row.cells[2].querySelectorAll('.rq-change-preview').length : 0;
        $('#dGrid').innerHTML = '<div><dt>REQUESTED ON</dt><dd>' + esc(row.cells[1].innerText.trim() || '-') + '</dd></div>' +
                                '<div><dt>FIELDS CHANGED</dt><dd>' + changedFieldCount + '</dd></div>';
        var pending = isPending(row);
        $('#detailActions').hidden = !pending;
        $('#detailEmpty').hidden = true; $('#detailView').hidden = false;
        loadChanges(row);
        placeAfterLoad();
    }
    function placeAfterLoad() { /* keeps panel scrolled to top on new selection */ var d = $('#dChanges'); if (d) d.scrollTop = 0; }
    function loadChanges(row) {
        var box = $('#dChanges');
        box.innerHTML = '<span style="color:var(--rq-muted)">Loading changes...</span>';
        openFor(row, function (modalEl) {
            if (selected !== row) return;
            var src = $('#reviewBody');
            var clone = src.cloneNode(true);
            $$('[id]', clone).forEach(function (n) { n.removeAttribute('id'); });
            $$('input, textarea, select, button', clone).forEach(function (n) { n.disabled = true; });
            box.innerHTML = '';
            if (src.innerText.trim()) box.appendChild(clone);
            else box.innerHTML = '<span style="color:var(--rq-muted)">No change details are available for this request.</span>';
        }, false);
    }
    function decide(kind) {
        if (!selected) return;
        if (kind === 'approve' && document.querySelectorAll('#requestsBody [data-select-request].is-selected').length > 0) {
            $('#acceptAllRequests').click();
            return;
        }
        openFor(selected, function (modalEl) {
            var b = $(kind === 'reject' ? '#rejectBtn' : '#approveBtn');
            if (b) b.click();
        }, true);
    }
    $('#detailApprove').addEventListener('click', function () { decide('approve'); });
    $('#detailReject').addEventListener('click', function () { decide('reject'); });
    $('#detailFull').addEventListener('click', function () {
        var b = selected && selected.querySelector('[data-act="review"]'); if (b) b.click();
    });

    /* ---------- events ---------- */
    requestBody.addEventListener('click', function (e) {
        if (e.target.closest('button, a')) { var tr = e.target.closest('tr'); if (tr && tr !== selected && tr.cells.length > 1) { selected && selected.classList.remove('is-selected'); selected = tr; tr.classList.add('is-selected'); } return; }
        var row = e.target.closest('tr');
        if (row && row.cells.length > 1 && row !== selected) select(row);
    });
    function resetPage() { page = 1; applyFilters(); }
    search.addEventListener('input', resetPage);
    purokSel.addEventListener('change', resetPage);
    agedOnly.addEventListener('change', resetPage);
    sortSel.addEventListener('change', function () { sortRows(); resetPage(); });
    $('#pager').addEventListener('click', function (e) {
        var b = e.target.closest('[data-pg]'); if (!b || b.disabled) return;
        var v = b.dataset.pg; page = v === 'prev' ? page - 1 : v === 'next' ? page + 1 : parseInt(v, 10);
        applyFilters(); clearSelection(); refresh();
    });
    $$('[data-chip-status]').forEach(function (chip) {
        chip.addEventListener('click', function () {
            var card = $('.review-summary-card[data-request-status="' + chip.dataset.chipStatus + '"]');
            if (card) card.click();
        });
    });
    function syncChips() {
        $$('[data-chip-status]').forEach(function (chip) {
            var card = $('.review-summary-card[data-request-status="' + chip.dataset.chipStatus + '"]');
            chip.classList.toggle('active', !!card && card.classList.contains('active'));
        });
        $$('[data-chip-count]').forEach(function (c) { var n = document.getElementById(c.dataset.chipCount); c.textContent = n ? n.textContent : ''; });
        var label = $('#requestDateLabel').textContent.replace('Showing requests from ', '');
        $('#dateToggleText').textContent = label.charAt(0).toUpperCase() + label.slice(1);
    }
    $('#clearFilters').addEventListener('click', function () {
        search.value = ''; purokSel.value = ''; agedOnly.checked = false; sortSel.value = 'desc'; page = 1;
        var all = $('[data-request-range="all"]'); if (all) all.click();
        var pend = $('.review-summary-card[data-request-status="pending"]'); if (pend) pend.click();
        sortRows(); applyFilters();
    });
    $('#exportRequests').addEventListener('click', function () {
        var heads = $$('th', requestBody.closest('table')).slice(0, -1).map(function (c) { return c.innerText.trim(); });
        var lines = [heads].concat(lastMatches.map(function (r) {
            return $$('td', r).slice(0, -1).map(function (c) { return c.innerText.replace(/\s*\n\s*/g, ' ').trim(); });
        }));
        var csv = lines.map(function (l) { return l.map(function (v) { return '"' + v.replace(/"/g, '""') + '"'; }).join(','); }).join('\r\n');
        var a = document.createElement('a');
        a.href = URL.createObjectURL(new Blob([csv], { type: 'text/csv' }));
        a.download = 'profile-edit-requests.csv'; a.click(); URL.revokeObjectURL(a.href);
    });

    /* ---------- refresh whenever request.js redraws ---------- */
    function refresh() {
        if (refreshing) return;
        refreshing = true;
        sortRows(); tagAges(); applyFilters(); syncChips();
        if (selected && !document.body.contains(selected)) clearSelection();
        if (!selected) {
            var first = rowsOf(requestBody).filter(function (r) { return r.style.display !== 'none'; })[0];
            if (first) select(first);
        }
        refreshing = false;
    }
    observer = new MutationObserver(refresh);
    observer.observe(requestBody, { childList: true });
    $$('.review-summary-card, #requestDateLabel, .review-summary-number').forEach(function (el) {
        observer.observe(el, { attributes: true, childList: true, characterData: true, subtree: true });
    });
    refresh();
})();
</script>
</body>
</html>
