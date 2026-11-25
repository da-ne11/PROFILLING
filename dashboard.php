<?php require_once __DIR__ . '/config.php'; require_once __DIR__ . '/transparency_budget.php'; require_viewer(); ?>
<!DOCTYPE html>
<html lang="en"<?= is_public() ? ' class="is-public"' : '' ?>>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KK Youth Profiling System | Dashboard</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" integrity="sha384-XGjxtQfXaH2tnPFa9x+ruJTuLE3Aa6LhHSWRr1XeTyhezb4abCG4ccI5AkVDxqC+" crossorigin="anonymous">
    <link href="sidebar.css?v=32" rel="stylesheet">
    <style>
        .dashboard-page{background:var(--ui-paper)}
        .dashboard-page{
            --dashboard-canvas:#eef4ff;
            --dashboard-card:#fff;
            --dashboard-outline:#dce7fb;
            --dashboard-blue:#1558c7;
            --dashboard-blue-light:#e9f1ff;
            --dashboard-shadow:0 10px 28px rgba(37,76,151,.08);
        }
        html[data-bs-theme="dark"] .dashboard-page{
            --dashboard-canvas:#171d2d;
            --dashboard-card:#222d40;
            --dashboard-outline:#35435d;
            --dashboard-blue:#82aaff;
            --dashboard-blue-light:#293b58;
            --dashboard-shadow:0 10px 28px rgba(0,0,0,.2);
        }
        html[data-bs-theme="dark"] .dashboard-stat-members{--stat-decoration:#293c5b}
        html[data-bs-theme="dark"] .dashboard-stat-events{--stat-decoration:#233d4b}
        html[data-bs-theme="dark"] .dashboard-stat-requests{--stat-decoration:#383355}
        html[data-bs-theme="dark"] .dashboard-stat-budget{--stat-decoration:#293b53}
        .dashboard-page main.container-fluid{
            min-height:calc(100vh - 68px);
            background:var(--dashboard-canvas);
        }
        .dashboard-toolbar{
            min-height:68px;
            border-bottom:1px solid var(--ui-line);
        }
        .dashboard-search{
            width:min(340px,38vw);
            min-height:40px;
            border:1px solid var(--ui-line);
            border-radius:999px;
            background:var(--ui-paper);
        }
        .dashboard-search:focus{background:var(--ui-surface)}
        .dashboard-welcome{
            position:relative;
            isolation:isolate;
            overflow:hidden;
            min-height:160px;
            padding:1.5rem clamp(1.25rem,3vw,2rem);
            border:1px solid rgba(255,255,255,.2);
            border-radius:20px;
            color:#fff;
            background:linear-gradient(112deg,#1249a8 0%,#1765d6 58%,#4385ec 100%);
            box-shadow:0 14px 32px rgba(24,78,174,.2);
        }
        .dashboard-welcome::before,.dashboard-welcome::after{
            position:absolute;
            z-index:-1;
            content:"";
            border:1px solid rgba(255,255,255,.14);
            border-radius:50%;
            pointer-events:none;
        }
        .dashboard-welcome::before{
            width:330px;height:330px;right:7%;top:-225px;
            box-shadow:0 0 0 30px rgba(255,255,255,.045),0 0 0 70px rgba(255,255,255,.035);
        }
        .dashboard-welcome::after{
            width:190px;height:190px;right:-34px;bottom:-133px;
            box-shadow:0 0 0 25px rgba(255,255,255,.05),0 0 0 55px rgba(255,255,255,.035);
        }
        .dashboard-welcome h2{color:#fff;font-size:clamp(1.35rem,2.4vw,1.9rem)}
        .dashboard-welcome p{color:rgba(255,255,255,.82)}
        .dashboard-welcome-copy{position:relative;z-index:1}
        .dashboard-welcome-note{max-width:620px}
        .dashboard-welcome-meta{display:flex;flex-wrap:wrap;gap:.5rem;margin-top:1rem}
        .dashboard-welcome-meta span{
            display:inline-flex;align-items:center;gap:.4rem;
            padding:.38rem .7rem;border:1px solid rgba(255,255,255,.22);
            border-radius:999px;background:rgba(255,255,255,.13);
            color:#fff;font-size:.78rem;font-weight:650;
        }
        .dashboard-welcome-action{
            border:1px solid rgba(255,255,255,.75)!important;
            border-radius:999px!important;
            background:#fff!important;
            color:#1558c7!important;
            white-space:nowrap;
        }
        .dashboard-welcome-action:hover{background:#edf4ff!important;color:#1249a8!important}
        .dashboard-stat,.dashboard-panel{
            height:100%;
            background:var(--dashboard-card);
            border:1px solid var(--dashboard-outline);
            border-radius:18px;
            box-shadow:var(--dashboard-shadow);
        }
        .dashboard-stat{
            min-height:130px;
            padding:1rem 1.1rem;
            display:flex;
            flex-direction:column;
            justify-content:center;
            gap:.12rem;
            position:relative;
            overflow:hidden;
            transition:transform .18s ease,box-shadow .18s ease;
        }
        .dashboard-stat::after{
            position:absolute;right:-22px;bottom:-34px;width:104px;height:104px;
            border-radius:50%;background:var(--stat-decoration,#e9f1ff);
            content:"";pointer-events:none;
        }
        .dashboard-stat:hover{transform:translateY(-2px);box-shadow:0 14px 32px rgba(37,76,151,.13)}
        .dashboard-stat>*{position:relative;z-index:1}
        .dashboard-stat-members{--stat-accent:#1765d6;--stat-decoration:#edf4ff}
        .dashboard-stat-events{--stat-accent:#1689b9;--stat-decoration:#e9f8ff}
        .dashboard-stat-requests{--stat-accent:#7057d9;--stat-decoration:#f1edff}
        .dashboard-stat-budget{--stat-accent:#2678c9;--stat-decoration:#eaf3ff}
        .dashboard-stat-icon{
            display:grid;
            place-items:center;
            width:2.35rem;
            height:2.35rem;
            margin-bottom:.35rem;
            border-radius:50%;
            color:var(--stat-accent,var(--dashboard-blue));
            background:var(--stat-decoration,var(--dashboard-blue-light));
            font-size:1.05rem;
        }
        .dashboard-panel{padding:1.2rem 1.3rem;overflow:hidden}
        .dashboard-panel h2{color:var(--bs-emphasis-color)}
        .dashboard-panel-title{display:flex;align-items:center;gap:.65rem}
        .dashboard-panel-icon{
            display:grid;place-items:center;flex:none;width:2.1rem;height:2.1rem;
            border-radius:11px;background:var(--dashboard-blue-light);color:var(--dashboard-blue);
            font-size:1rem;
        }
        .dashboard-panel .btn-outline-primary{
            border-radius:999px;
        }
        .dashboard-attention{
            display:block;
            padding:1.2rem 1.3rem;
            border:1px solid var(--dashboard-outline);
            border-left:4px solid var(--dashboard-blue);
        }
        .attention-pill{
            display:inline-flex;
            align-items:center;
            gap:.4rem;
            background:var(--dashboard-blue-light);
            color:var(--dashboard-blue);
            padding:.25rem .55rem;
            border-radius:999px;
            font-size:.75rem;
            font-weight:700;
        }
        .dashboard-review-list{display:grid;gap:.65rem;margin:1rem 0}
        .dashboard-review-item{
            display:flex;align-items:center;gap:.65rem;
            padding:.65rem .7rem;border:1px solid var(--dashboard-outline);
            border-radius:12px;color:var(--bs-body-color);text-decoration:none;
            font-size:.84rem;
        }
        .dashboard-review-item:hover{background:var(--dashboard-blue-light);color:var(--dashboard-blue)}
        .dashboard-review-item i{color:var(--dashboard-blue);font-size:1.05rem}
        .dashboard-review-item strong{margin-left:auto;white-space:nowrap}
        .dashboard-upcoming-list{display:grid}
        .dashboard-upcoming-item{
            display:flex;align-items:center;gap:.7rem;padding:.7rem 0;
            border-top:1px solid var(--dashboard-outline);color:inherit;
            text-decoration:none;min-width:0;
        }
        .dashboard-upcoming-item:first-child{border-top:0}
        .dashboard-upcoming-item:hover .dashboard-upcoming-title{color:var(--dashboard-blue)}
        .dashboard-date-chip{
            display:flex;flex-direction:column;align-items:center;justify-content:center;
            flex:none;width:48px;min-height:48px;border-radius:12px;
            background:var(--dashboard-blue-light);color:var(--dashboard-blue);
            font-size:.68rem;font-weight:750;line-height:1.15;text-transform:uppercase;
        }
        .dashboard-date-chip strong{font-size:1.05rem}
        .dashboard-upcoming-copy{min-width:0;flex:1}
        .dashboard-upcoming-title{overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size:.86rem}
        .dashboard-upcoming-subtitle{overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size:.74rem}
        .dashboard-upcoming-arrow{color:var(--dashboard-blue);font-size:.9rem}
        .dashboard-stat .fs-2{
            color:var(--bs-emphasis-color);
            font-size:clamp(1.2rem,2vw,1.65rem)!important;
            font-weight:700;
            letter-spacing:-.035em;
            overflow-wrap:anywhere;
        }
        .budget-stat{
            height:100%;
            padding:.6rem .8rem;
            border-left:1px solid var(--dashboard-outline);
        }
        .announcement-row+.announcement-row{border-top:1px solid var(--dashboard-outline)}
        .dashboard-chart-wrap{position:relative;height:270px;min-height:220px}
        .dashboard-chart-wrap.dashboard-chart-gauge{height:175px;min-height:175px}
        .dashboard-chart-fallback{font-size:.875rem;color:var(--bs-secondary-color)}
        .dashboard-page .table{
            --bs-table-bg:transparent;
            --bs-table-border-color:var(--dashboard-outline);
        }
        .dashboard-page .table thead th{
            color:var(--bs-secondary-color);
            font-size:.72rem;
            font-weight:750;
            text-transform:uppercase;
            letter-spacing:.045em;
        }
        .dashboard-budget-center{
            margin-top:-1.55rem;
            text-align:center;
            pointer-events:none;
        }
        .dashboard-budget-center strong{display:block;color:var(--bs-emphasis-color);font-size:1.35rem}
        .dashboard-budget-center span{color:var(--bs-secondary-color);font-size:.75rem}
        .dashboard-stat-row>.col-6{min-width:0}
        @media(max-width:575.98px){
            .dashboard-toolbar .dashboard-search{width:100%;order:3}
            .dashboard-toolbar{flex-wrap:wrap}
            .dashboard-welcome{min-height:0;padding:1.2rem;border-radius:16px}
            .dashboard-welcome-action{margin-top:.25rem}
            .dashboard-panel{padding:1rem}
            .budget-stat{border-left:0;border-top:1px solid var(--dashboard-outline);padding:.75rem 0}
            .dashboard-chart-wrap{height:240px}
        }
    </style>
    <?php include __DIR__ . '/init.php'; ?>
    <?php
    $dashboardMembers = json_decode($__data['kkMembers'] ?? '[]', true);
    $dashboardMembers = is_array($dashboardMembers) ? $dashboardMembers : [];
    $dashboardActiveMembers = count(array_filter($dashboardMembers, function ($member) {
        return is_array($member) && ($member['status'] ?? '') === 'Active';
    }));
    $dashboardAgeGroups = ['15-17' => 0, '18-24' => 0, '25-30' => 0];
    $dashboardToday = new DateTimeImmutable('today');
    foreach ($dashboardMembers as $member) {
        if (!is_array($member) || ($member['status'] ?? '') !== 'Active' || empty($member['birthDate'])) continue;
        $birthDateValue = trim((string)$member['birthDate']);
        $birthDate = DateTimeImmutable::createFromFormat('!Y-m-d', $birthDateValue);
        if (!$birthDate || $birthDate->format('Y-m-d') !== $birthDateValue || $birthDate > $dashboardToday) continue;
        $age = $birthDate->diff($dashboardToday)->y;
        if ($age >= 15 && $age <= 17) $dashboardAgeGroups['15-17']++;
        elseif ($age >= 18 && $age <= 24) $dashboardAgeGroups['18-24']++;
        elseif ($age >= 25 && $age <= 30) $dashboardAgeGroups['25-30']++;
    }
    $dashboardAgeParticipationCount = array_sum($dashboardAgeGroups);
    $dashboardAnnouncements = json_decode($__data['skAnnouncements'] ?? '[]', true);
    $dashboardAnnouncements = is_array($dashboardAnnouncements) ? $dashboardAnnouncements : [];
    $today = date('Y-m-d');
    $publishedAnnouncements = array_values(array_filter($dashboardAnnouncements, function ($item) use ($today) {
        return is_array($item)
            && ($item['publishState'] ?? '') === 'Published'
            && (empty($item['publishDate']) || $item['publishDate'] <= $today);
    }));
    $upcomingAnnouncements = array_values(array_filter($publishedAnnouncements, function ($item) use ($today) {
        return !empty($item['date']) && $item['date'] >= $today && empty($item['cancelled']);
    }));
    usort($upcomingAnnouncements, function ($a, $b) {
        return strcmp((string)$a['date'], (string)$b['date']);
    });
    $upcomingAnnouncementCount = count($upcomingAnnouncements);
    $upcomingAnnouncements = array_slice($upcomingAnnouncements, 0, 5);
    $dashboardBudget = json_decode($__data['skTransparencyBudget'] ?? 'null', true);
    $dashboardBudget = is_array($dashboardBudget) ? $dashboardBudget : [];
    if (isset($dashboardBudget['budgets']) && is_array($dashboardBudget['budgets'])) {
        $dashboardBudgetYear = trim((string)($dashboardBudget['fy'] ?? ''));
        if ($dashboardBudgetYear !== '' && isset($dashboardBudget['budgets'][$dashboardBudgetYear])
            && is_array($dashboardBudget['budgets'][$dashboardBudgetYear])) {
            $dashboardBudget = $dashboardBudget['budgets'][$dashboardBudgetYear];
        }
    }
    $dashboardBudgetYear = trim((string)($dashboardBudget['fy'] ?? ''));
    $dashboardBudgetTotal = transparency_budget_year_total($dashboardBudget);
    $dashboardBudgetPublished = $dashboardBudgetTotal !== null;
    $dashboardBudgetAmount = $dashboardBudgetPublished ? $dashboardBudgetTotal : 0;
    $dashboardExpenses = json_decode($__data['skTransparencyExpenses'] ?? '[]', true);
    $dashboardExpenses = is_array($dashboardExpenses) ? $dashboardExpenses : [];
    $dashboardExpenseRows = [];
    $dashboardTotalSpent = 0;
    foreach ($dashboardExpenses as $expense) {
        if (!is_array($expense) || !isset($expense['amount']) || !is_numeric($expense['amount'])) continue;
        if ($dashboardBudgetYear !== '' && !empty($expense['fiscalYear'])
            && (string)$expense['fiscalYear'] !== $dashboardBudgetYear) continue;
        $amount = max(0, (float)$expense['amount']);
        $category = trim((string)($expense['category'] ?? '')) ?: 'Other';
        $dashboardTotalSpent += $amount;
        $dashboardExpenseRows[] = [
            'date' => (string)($expense['date'] ?? ''),
            'title' => trim((string)($expense['title'] ?? '')) ?: 'Expense',
            'category' => $category,
            'amount' => $amount,
        ];
    }
    usort($dashboardExpenseRows, function ($a, $b) {
        return strcmp($b['date'], $a['date']);
    });
    $dashboardExpenseRows = array_slice($dashboardExpenseRows, 0, 5);
    $dashboardEditRequests = json_decode($__data['kkEditRequests'] ?? '[]', true);
    $dashboardEditRequests = is_array($dashboardEditRequests) ? array_values(array_filter($dashboardEditRequests, function ($item) {
        return is_array($item) && ($item['status'] ?? '') === 'Edit Request';
    })) : [];
    $dashboardAttentionCount = count($dashboardEditRequests);
    $dashboardBudgetUtilization = $dashboardBudgetPublished && $dashboardBudgetAmount > 0
        ? ($dashboardTotalSpent / $dashboardBudgetAmount) * 100
        : 0;
    $dashboardGaugeSpent = min($dashboardTotalSpent, $dashboardBudgetAmount);
    $dashboardGaugeRemaining = max(0, $dashboardBudgetAmount - $dashboardTotalSpent);
    $dashboardCurrency = function ($amount) {
        return '₱' . number_format((float)$amount, 2);
    };

    /* Rank completed programs by confirmed attendance for planning. */
    $dashboardPrograms = json_decode($__data['skAnnouncements'] ?? '[]', true);
    $dashboardPrograms = is_array($dashboardPrograms) ? $dashboardPrograms : [];
    $dashboardProgramById = [];
    $dashboardProgramInsights = [];
    $dashboardInsightCutoff = (new DateTimeImmutable('today'))->modify('-12 months')->format('Y-m-d');
    foreach ($dashboardPrograms as $program) {
        if (!is_array($program) || empty($program['id']) || empty($program['date'])
            || $program['date'] < $dashboardInsightCutoff || $program['date'] > $today
            || !empty($program['cancelled'])) continue;
        $dashboardProgramById[(string)$program['id']] = [
            'title' => trim((string)($program['title'] ?? 'Untitled program')) ?: 'Untitled program',
            'category' => trim((string)($program['category'] ?? 'Other')) ?: 'Other',
            'date' => (string)$program['date'],
            'attendance' => 0,
            'members' => [],
        ];
    }
    $dashboardAttendedSignupMembers = [];
    $dashboardRegistrations = json_decode($__data['skEventRegistrations'] ?? '[]', true);
    $dashboardRegistrations = is_array($dashboardRegistrations) ? $dashboardRegistrations : [];
    foreach ($dashboardRegistrations as $registration) {
        if (!is_array($registration)) continue;
        $eventId = (string)($registration['eventId'] ?? '');
        if (!isset($dashboardProgramById[$eventId]) || ($registration['attendanceStatus'] ?? '') !== 'Attended') continue;
        $dashboardProgramById[$eventId]['attendance']++;
        if (!empty($registration['memberId'])) {
            $memberId = (string)$registration['memberId'];
            $dashboardProgramById[$eventId]['members'][$memberId] = true;
            $dashboardAttendedSignupMembers[$eventId . ':' . $memberId] = true;
        }
    }
    $dashboardWalkins = json_decode($__data['skEventAttendance'] ?? '[]', true);
    $dashboardWalkins = is_array($dashboardWalkins) ? $dashboardWalkins : [];
    $dashboardWalkinSeen = [];
    foreach ($dashboardWalkins as $walkin) {
        if (!is_array($walkin) || !empty($walkin['removedAt'])) continue;
        $eventId = (string)($walkin['eventId'] ?? '');
        if (!isset($dashboardProgramById[$eventId])) continue;
        $memberId = trim((string)($walkin['memberId'] ?? ''));
        if ($memberId !== '' && isset($dashboardAttendedSignupMembers[$eventId . ':' . $memberId])) continue;
        $walkinKey = $eventId . ':' . ($memberId !== '' ? $memberId : (string)($walkin['id'] ?? ''));
        if ($walkinKey !== $eventId . ':' && isset($dashboardWalkinSeen[$walkinKey])) continue;
        $dashboardWalkinSeen[$walkinKey] = true;
        $dashboardProgramById[$eventId]['attendance']++;
        if ($memberId !== '') $dashboardProgramById[$eventId]['members'][$memberId] = true;
    }
    foreach ($dashboardProgramById as $program) {
        if ($program['attendance'] > 0) $dashboardProgramInsights[] = $program;
    }
    usort($dashboardProgramInsights, function ($a, $b) {
        return $b['attendance'] <=> $a['attendance']
            ?: strcmp($b['date'], $a['date'])
            ?: strcmp($a['title'], $b['title']);
    });
    $dashboardProgramInsights = array_slice($dashboardProgramInsights, 0, 5);
    $dashboardProgramChart = array_map(function ($program) {
        return [
            'label' => $program['title'],
            'value' => (int)$program['attendance'],
        ];
    }, $dashboardProgramInsights);
    $dashboardChartData = json_encode([
        'programs' => $dashboardProgramChart,
        'ageGroups' => array_map(function ($label, $count) {
            return ['label' => $label, 'value' => $count];
        }, array_keys($dashboardAgeGroups), array_values($dashboardAgeGroups)),
        'budgetProgress' => [
            'spent' => round($dashboardGaugeSpent, 2),
            'remaining' => round($dashboardGaugeRemaining, 2),
            'utilization' => round($dashboardBudgetUtilization, 1),
        ],
    ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE | JSON_THROW_ON_ERROR);
    $dashboardGreeting = (int)date('G') < 12 ? 'Good morning' : ((int)date('G') < 18 ? 'Good afternoon' : 'Good evening');
    ?>
</head>

<body class="dashboard-page">
<div class="top-strip"></div>
<script src="theme.js?v=8"></script>
<script src="sidebar.js?v=16"></script>
<script type="application/json" id="dashboardChartData"><?= $dashboardChartData ?></script>

<div class="page-content">
    <header class="bg-white border-bottom sticky-top">
        <div class="dashboard-toolbar d-flex align-items-center gap-2 gap-md-3 px-3 px-lg-4 py-2">
            <button class="btn btn-outline-primary d-lg-none" type="button"
                    data-bs-toggle="offcanvas" data-bs-target="#sidebar" aria-controls="sidebar">
                <i class="bi bi-list fs-5"></i>
            </button>
            <div>
                <h1 class="h5 fw-bold mb-0">Dashboard</h1>
            </div>
            <label class="dashboard-search input-group align-items-center px-3 ms-auto me-auto" aria-label="Search dashboard information">
                <i class="bi bi-search text-muted me-2" aria-hidden="true"></i>
                <input class="form-control border-0 bg-transparent shadow-none p-0" type="search" id="dashboardSearch" placeholder="Search activities and expenses..." autocomplete="off">
            </label>
            <div class="ms-auto header-account-area">
                <a class="header-notification" href="request.php" aria-label="<?= (int)$dashboardAttentionCount ?> profile edits need review">
                    <i class="bi bi-bell" aria-hidden="true"></i>
                    <?php if ($dashboardAttentionCount > 0): ?><span class="header-notification-count"><?= (int)$dashboardAttentionCount ?></span><?php endif; ?>
                </a>
                <span class="header-avatar"><?= e(uinit()) ?></span>
                <div class="d-none d-md-block header-identity">
                    <div class="header-identity-name"><?= e(uname()) ?></div>
                    <div class="header-identity-role"><?= e(strtoupper(urole())) ?></div>
                </div>
                <a href="<?= is_public() ? 'login.php' : 'logout.php' ?>" class="btn btn-sm header-logout">
                    <i class="bi bi-box-arrow-right me-1"></i><?= is_public() ? 'Official Login' : 'Logout' ?>
                </a>
            </div>
        </div>
    </header>

    <main class="container-fluid p-3 p-lg-4">
        <div class="row g-3 mb-3 align-items-stretch">
            <section class="col-12 col-xl-8" aria-labelledby="dashboardWelcomeTitle">
                <div class="dashboard-welcome h-100 d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div class="dashboard-welcome-copy">
                        <h2 class="fw-bold mb-2" id="dashboardWelcomeTitle"><?= e($dashboardGreeting) ?>, <?= e(uname()) ?>!</h2>
                        <p class="dashboard-welcome-note mb-0">Your overview of KK members, SK activities, youth participation, and community resources.</p>
                        <div class="dashboard-welcome-meta" aria-label="Dashboard highlights">
                            <span><i class="bi bi-people-fill" aria-hidden="true"></i><?= (int)$dashboardActiveMembers ?> active KK members</span>
                            <span><i class="bi bi-calendar-event" aria-hidden="true"></i><?= (int)$upcomingAnnouncementCount ?> upcoming activities</span>
                            <span><i class="bi bi-clipboard-check" aria-hidden="true"></i><?= (int)$dashboardAttentionCount ?> profile edits to review</span>
                        </div>
                    </div>
                    <a href="announcements.php" class="btn btn-sm dashboard-welcome-action px-3">
                        <i class="bi bi-plus-lg me-1"></i>New activity
                    </a>
                </div>
            </section>
            <section class="col-12 col-xl-4" aria-labelledby="upcomingTitle">
                <div class="dashboard-panel h-100">
                    <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
                        <div class="dashboard-panel-title">
                            <span class="dashboard-panel-icon"><i class="bi bi-megaphone" aria-hidden="true"></i></span>
                            <div>
                                <h2 class="h6 fw-bold mb-1" id="upcomingTitle">Upcoming activities</h2>
                                <p class="small text-muted mb-0">Published community activities</p>
                            </div>
                        </div>
                        <a href="announcements.php" class="small text-decoration-none text-nowrap">View all <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
                    </div>
                    <?php if (!$upcomingAnnouncements): ?>
                        <div class="py-4 text-center text-muted small">No published activities are scheduled right now.</div>
                    <?php else: ?>
                        <div class="dashboard-upcoming-list">
                            <?php foreach (array_slice($upcomingAnnouncements, 0, 3) as $item): ?>
                                <?php
                                $activitySearch = trim(
                                    (string)($item['title'] ?? '') . ' '
                                    . (string)($item['category'] ?? '') . ' '
                                    . (string)($item['venue'] ?? '')
                                );
                                $activityTimestamp = strtotime((string)($item['date'] ?? ''));
                                ?>
                                <a class="dashboard-upcoming-item" href="announcements.php" data-dashboard-search="<?= e($activitySearch) ?>">
                                    <span class="dashboard-date-chip" aria-hidden="true">
                                        <?= $activityTimestamp !== false ? e(date('M', $activityTimestamp)) : 'Date' ?>
                                        <strong><?= $activityTimestamp !== false ? e(date('j', $activityTimestamp)) : '—' ?></strong>
                                    </span>
                                    <span class="dashboard-upcoming-copy">
                                        <span class="dashboard-upcoming-title d-block fw-semibold"><?= e($item['title'] ?? 'Untitled activity') ?></span>
                                        <span class="dashboard-upcoming-subtitle d-block text-muted"><?= e($item['venue'] ?? $item['category'] ?? 'SK activity') ?></span>
                                    </span>
                                    <i class="bi bi-arrow-right dashboard-upcoming-arrow" aria-hidden="true"></i>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </section>
        </div>

        <section class="row g-3 mb-4 dashboard-stat-row" aria-label="System summary">
            <div class="col-6 col-xl-3">
                <div class="dashboard-stat dashboard-stat-members">
                    <span class="dashboard-stat-icon"><i class="bi bi-people" aria-hidden="true"></i></span>
                    <div class="fs-2 fw-bold"><?= $dashboardActiveMembers ?></div>
                    <div class="text-muted">Active KK members</div>
                </div>
            </div>
            <div class="col-6 col-xl-3">
                <div class="dashboard-stat dashboard-stat-events">
                    <span class="dashboard-stat-icon"><i class="bi bi-calendar-event" aria-hidden="true"></i></span>
                    <div class="fs-2 fw-bold"><?= $upcomingAnnouncementCount ?></div>
                    <div class="text-muted">Upcoming events</div>
                </div>
            </div>
            <div class="col-6 col-xl-3">
                <div class="dashboard-stat dashboard-stat-requests">
                    <span class="dashboard-stat-icon"><i class="bi bi-inbox" aria-hidden="true"></i></span>
                    <div class="fs-2 fw-bold"><?= (int)$dashboardAttentionCount ?></div>
                    <div class="text-muted">Pending profile edits</div>
                </div>
            </div>
            <div class="col-6 col-xl-3">
                <div class="dashboard-stat dashboard-stat-budget">
                    <span class="dashboard-stat-icon"><i class="bi bi-wallet2" aria-hidden="true"></i></span>
                    <div class="fs-2 fw-bold"><?= $dashboardBudgetPublished ? e($dashboardCurrency($dashboardBudgetAmount - $dashboardTotalSpent)) : '—' ?></div>
                    <div class="text-muted">Budget remaining</div>
                </div>
            </div>
        </section>

        <div class="row g-3 mb-4 align-items-stretch">
            <section class="col-12 col-xl-5" aria-labelledby="programInsightsTitle">
                <div class="dashboard-panel h-100">
                    <div class="d-flex flex-wrap align-items-start justify-content-between gap-2 mb-2">
                        <div class="dashboard-panel-title">
                            <span class="dashboard-panel-icon"><i class="bi bi-bar-chart-line-fill" aria-hidden="true"></i></span>
                            <div>
                                <h2 class="h6 fw-bold mb-1" id="programInsightsTitle">Program participation</h2>
                                <p class="small text-muted mb-0">Confirmed attendance in the past 12 months</p>
                            </div>
                        </div>
                        <a href="participation.php" class="small text-decoration-none">Records <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
                    </div>
                    <?php if (!$dashboardProgramInsights): ?>
                        <div class="py-5 text-center text-muted small">No confirmed attendance data is available yet.</div>
                    <?php else: ?>
                        <div class="dashboard-chart-wrap">
                            <canvas id="programAttendanceChart" role="img" aria-label="Confirmed attendance for the most attended SK programs"></canvas>
                        </div>
                        <p class="dashboard-chart-fallback mb-0" id="programChartFallback" hidden>Chart is unavailable; see the participation records for attendance information.</p>
                    <?php endif; ?>
                </div>
            </section>
            <section class="col-12 col-md-6 col-xl-3" aria-labelledby="ageParticipationTitle">
                <div class="dashboard-panel h-100">
                    <div class="d-flex flex-wrap align-items-start justify-content-between gap-2 mb-2">
                        <div class="dashboard-panel-title">
                            <span class="dashboard-panel-icon"><i class="bi bi-pie-chart-fill" aria-hidden="true"></i></span>
                            <div>
                                <h2 class="h6 fw-bold mb-1" id="ageParticipationTitle">Youth age groups</h2>
                                <p class="small text-muted mb-0">Active members aged 15-30</p>
                            </div>
                        </div>
                        <a href="members.php" class="small text-decoration-none">Members</a>
                    </div>
                    <?php if ($dashboardAgeParticipationCount > 0): ?>
                        <div class="dashboard-chart-wrap">
                            <canvas id="ageParticipationChart" role="img" aria-label="Active KK members by age group: <?= e(implode(', ', array_map(function ($label, $count) { return $label . ': ' . $count; }, array_keys($dashboardAgeGroups), array_values($dashboardAgeGroups)))) ?>"></canvas>
                        </div>
                        <p class="dashboard-chart-fallback mb-0" id="ageParticipationChartFallback" hidden>Chart is unavailable; age-group totals could not be displayed.</p>
                    <?php else: ?>
                        <div class="py-5 text-center text-muted small">No age information is available for active KK members yet.</div>
                    <?php endif; ?>
                </div>
            </section>
            <section class="col-12 col-md-6 col-xl-4" aria-labelledby="budgetGaugeTitle">
                <div class="dashboard-panel h-100">
                    <div class="d-flex align-items-start justify-content-between gap-2">
                        <div class="dashboard-panel-title">
                            <span class="dashboard-panel-icon"><i class="bi bi-wallet2" aria-hidden="true"></i></span>
                            <div>
                                <h2 class="h6 fw-bold mb-1" id="budgetGaugeTitle">SK budget overview</h2>
                                <p class="small text-muted mb-0"><?= $dashboardBudgetYear !== '' ? 'Fiscal year ' . e($dashboardBudgetYear) : 'Recorded budget and expenses' ?></p>
                            </div>
                        </div>
                        <a href="reports.php" class="small text-decoration-none">Details</a>
                    </div>
                    <?php if ($dashboardBudgetPublished && $dashboardBudgetAmount > 0): ?>
                        <div class="dashboard-chart-wrap dashboard-chart-gauge mt-2">
                            <canvas id="budgetProgressChart" role="img" aria-label="SK budget utilization: <?= e(number_format($dashboardBudgetUtilization, 1)) ?> percent spent"></canvas>
                        </div>
                        <div class="dashboard-budget-center" aria-hidden="true">
                            <strong><?= e(number_format($dashboardBudgetUtilization, 1)) ?>%</strong>
                            <span>of budget spent</span>
                        </div>
                        <div class="d-flex justify-content-between gap-3 mt-3">
                            <div>
                                <div class="small text-muted">Recorded expenses</div>
                                <strong><?= e($dashboardCurrency($dashboardTotalSpent)) ?></strong>
                            </div>
                            <div class="text-end">
                                <div class="small text-muted">Remaining</div>
                                <strong><?= e($dashboardCurrency($dashboardBudgetAmount - $dashboardTotalSpent)) ?></strong>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="py-4 text-center">
                            <div class="text-muted small">A positive published budget is needed to show utilization.</div>
                            <a href="reports.php" class="btn btn-sm btn-outline-primary mt-3">View budget transparency</a>
                        </div>
                    <?php endif; ?>
                    <p class="dashboard-chart-fallback mt-2 mb-0" id="budgetProgressFallback" hidden>Chart is unavailable; the budget figures are shown above.</p>
                </div>
            </section>
        </div>

        <div class="row g-3 mb-4 align-items-stretch">
            <section class="col-12 col-xl-4" aria-labelledby="attentionTitle">
                <div class="dashboard-panel dashboard-attention h-100">
                    <div class="d-flex align-items-start justify-content-between gap-2">
                        <div>
                            <span class="attention-pill"><i class="bi bi-clipboard-check" aria-hidden="true"></i>Needs attention</span>
                            <h2 class="h6 fw-bold mt-2 mb-1" id="attentionTitle">Profile edits to review</h2>
                            <p class="small text-muted mb-0">Keep member records up to date.</p>
                        </div>
                        <span class="dashboard-panel-icon"><i class="bi bi-inbox" aria-hidden="true"></i></span>
                    </div>
                    <div class="dashboard-review-list">
                        <?php if ($dashboardEditRequests): ?>
                            <a class="dashboard-review-item" href="request.php">
                                <i class="bi bi-person-lines-fill" aria-hidden="true"></i>
                                <span>Profile edit requests</span>
                                <strong><?= count($dashboardEditRequests) ?></strong>
                            </a>
                        <?php endif; ?>
                        <?php if (!$dashboardAttentionCount): ?>
                            <p class="small text-muted mb-0">Nothing is waiting for review.</p>
                        <?php endif; ?>
                    </div>
                    <a href="request.php" class="btn btn-sm btn-outline-primary w-100">Review profile edits <i class="bi bi-arrow-right ms-1" aria-hidden="true"></i></a>
                </div>
            </section>
            <section class="col-12 col-xl-8" aria-labelledby="recentExpensesTitle">
                <div class="dashboard-panel h-100">
                    <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
                        <div class="dashboard-panel-title">
                            <span class="dashboard-panel-icon"><i class="bi bi-receipt" aria-hidden="true"></i></span>
                            <div>
                                <h2 class="h6 fw-bold mb-1" id="recentExpensesTitle">Recent SK expenses</h2>
                                <p class="small text-muted mb-0">Latest records for the selected fiscal year</p>
                            </div>
                        </div>
                        <a href="reports.php" class="small text-decoration-none">View all <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
                    </div>
                    <?php if (!$dashboardExpenseRows): ?>
                        <p class="small text-muted mb-0 py-3">No expenses have been recorded for this fiscal year.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-sm align-middle mb-0">
                                <thead><tr><th scope="col">Date</th><th scope="col">Activity / expense</th><th scope="col">Type</th><th scope="col" class="text-end">Amount</th></tr></thead>
                                <tbody>
                                    <?php foreach ($dashboardExpenseRows as $expense): ?>
                                        <?php $expenseSearch = trim($expense['title'] . ' ' . $expense['category']); ?>
                                        <tr data-dashboard-search="<?= e($expenseSearch) ?>">
                                            <td class="text-nowrap"><?= e($expense['date']) ?></td>
                                            <td><?= e($expense['title']) ?></td>
                                            <td><?= e($expense['category']) ?></td>
                                            <td class="text-end text-nowrap"><?= e($dashboardCurrency($expense['amount'])) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <p class="small text-muted mt-2 mb-0">Showing the five most recent expenses. Recipient and description fields are not displayed here.</p>
                    <?php endif; ?>
                </div>
            </section>
        </div>

    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="app-alerts.js?v=2"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<script>
    (function () {
        var dataNode = document.getElementById('dashboardChartData');
        var chartData = JSON.parse(dataNode.textContent || '{}');
        var brand = getComputedStyle(document.documentElement).getPropertyValue('--ui-brand').trim() || '#2563eb';
        var grid = getComputedStyle(document.documentElement).getPropertyValue('--ui-line').trim() || '#e5eaf1';

        if (typeof Chart === 'undefined') {
            var programFallback = document.getElementById('programChartFallback');
            var ageParticipationFallback = document.getElementById('ageParticipationChartFallback');
            var budgetProgressFallback = document.getElementById('budgetProgressFallback');
            if (programFallback) programFallback.hidden = false;
            if (ageParticipationFallback) ageParticipationFallback.hidden = false;
            if (budgetProgressFallback) budgetProgressFallback.hidden = false;
            return;
        }

        var progressCanvas = document.getElementById('budgetProgressChart');
        if (progressCanvas && chartData.budgetProgress) {
            new Chart(progressCanvas, {
                type: 'doughnut',
                data: {
                    labels: ['Recorded expenses', 'Remaining budget'],
                    datasets: [{
                        data: [chartData.budgetProgress.spent, chartData.budgetProgress.remaining],
                        backgroundColor: [chartData.budgetProgress.utilization > 100 ? '#dc3545' : brand, grid],
                        borderWidth: 0,
                        hoverOffset: 3
                    }]
                },
                options: {
                    animation: { duration: 180 },
                    circumference: 180,
                    cutout: '78%',
                    maintainAspectRatio: false,
                    rotation: 270,
                    responsive: true,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    return ' ' + context.label + ': ₱' + Number(context.raw).toLocaleString('en-PH', {
                                        minimumFractionDigits: 2,
                                        maximumFractionDigits: 2
                                    });
                                }
                            }
                        }
                    }
                }
            });
        }

        var programCanvas = document.getElementById('programAttendanceChart');
        if (programCanvas && chartData.programs && chartData.programs.length) {
            new Chart(programCanvas, {
                type: 'bar',
                data: {
                    labels: chartData.programs.map(function (item) { return item.label; }),
                    datasets: [{
                        data: chartData.programs.map(function (item) { return item.value; }),
                        backgroundColor: [brand, '#7057d9', '#36a3dc', '#1fa3a8', '#ed9d39'],
                        borderRadius: 7,
                        borderSkipped: false,
                        maxBarThickness: 24
                    }]
                },
                options: {
                    animation: { duration: 180 },
                    indexAxis: 'y',
                    maintainAspectRatio: false,
                    responsive: true,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function (context) { return ' Confirmed attendance: ' + context.parsed.x; }
                            }
                        }
                    },
                    scales: {
                        x: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: grid } },
                        y: { grid: { display: false } }
                    }
                }
            });
        }

        var ageParticipationCanvas = document.getElementById('ageParticipationChart');
        if (ageParticipationCanvas && chartData.ageGroups && chartData.ageGroups.length) {
            new Chart(ageParticipationCanvas, {
                type: 'doughnut',
                data: {
                    labels: chartData.ageGroups.map(function (item) { return item.label; }),
                    datasets: [{
                        data: chartData.ageGroups.map(function (item) { return item.value; }),
                        backgroundColor: [brand, '#7057d9', '#36a3dc'],
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: {
                    animation: { duration: 180 },
                    maintainAspectRatio: false,
                    responsive: true,
                    cutout: '62%',
                    plugins: {
                        legend: { position: 'bottom', labels: { usePointStyle: true, boxWidth: 8 } },
                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    return ' ' + context.label + ': ' + Number(context.raw).toLocaleString('en-PH') + ' members';
                                }
                            }
                        }
                    }
                }
            });
        }

        var dashboardSearch = document.getElementById('dashboardSearch');
        if (dashboardSearch) {
            dashboardSearch.addEventListener('input', function () {
                var query = dashboardSearch.value.trim().toLocaleLowerCase();
                document.querySelectorAll('[data-dashboard-search]').forEach(function (row) {
                    var value = (row.getAttribute('data-dashboard-search') || row.textContent).toLocaleLowerCase();
                    row.hidden = query !== '' && value.indexOf(query) === -1;
                });
            });
        }
    })();
</script>
</body>
</html>
