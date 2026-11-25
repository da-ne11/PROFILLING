<?php require_once __DIR__ . '/config.php'; require_viewer(); ?>
<!DOCTYPE html>
<html lang="en"<?= is_public() ? ' class="is-public"' : '' ?>>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KK Youth Profiling System | SK Meetings</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" integrity="sha384-XGjxtQfXaH2tnPFa9x+ruJTuLE3Aa6LhHSWRr1XeTyhezb4abCG4ccI5AkVDxqC+" crossorigin="anonymous">
    <link href="sidebar.css?v=32" rel="stylesheet">
    <link href="vendor/summernote/summernote-bs5.min.css" rel="stylesheet">

    <style>
:root{
    --kk-primary:var(--bs-primary);
    --black:var(--bs-body-color);
    --line:var(--bs-border-color);
}

*{
    box-sizing:border-box;
}

body{
    margin:0;
    background:var(--bs-tertiary-bg);
    color:var(--black);
    font-family:Inter,system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;
}

a{
    text-decoration:none;
}

.top-strip{
    height:7px;
    background:var(--bs-primary);
}

header{
    background:#fff!important;
    border-bottom:1px solid var(--line)!important;
}

header h1{
    font-weight:900;
}

.btn-success{
    --bs-btn-bg:var(--bs-body-color);
    --bs-btn-border-color:var(--bs-body-color);
    --bs-btn-hover-bg:var(--kk-primary);
    --bs-btn-hover-border-color:var(--kk-primary);
    --bs-btn-hover-color:#fff;
    --bs-btn-active-bg:var(--bs-body-color);
    --bs-btn-active-border-color:var(--bs-body-color);
    --bs-btn-focus-shadow-rgb:13,110,253;
    font-weight:800;
}

.btn-outline-success{
    --bs-btn-color:var(--bs-body-color);
    --bs-btn-border-color:var(--bs-body-color);
    --bs-btn-hover-bg:var(--kk-primary);
    --bs-btn-hover-border-color:var(--kk-primary);
    --bs-btn-hover-color:#fff;
    --bs-btn-active-bg:var(--kk-primary);
    --bs-btn-active-border-color:var(--kk-primary);
    font-weight:700;
}

.dashboard-card{
    border:1px solid var(--line)!important;
    border-radius:22px!important;
    overflow:hidden;
    box-shadow:0 8px 20px rgba(20,20,20,.045)!important;
}

.dashboard-card .card-header{
    background:#fff!important;
    border-bottom:1px solid var(--line);
}

.dashboard-card .card-header h2{
    font-weight:900;
}

.table{
    --bs-table-hover-bg:rgba(var(--bs-primary-rgb),.08);
}

.table thead th{
    background:rgba(var(--bs-primary-rgb),.08);
    color:var(--bs-secondary-color);
    font-size:.76rem;
    text-transform:uppercase;
    letter-spacing:.4px;
}

.table td{
    font-size:.88rem;
}

.badge.text-bg-success{
    color:var(--bs-body-color)!important;
    background:var(--bs-primary)!important;
}

.badge.text-bg-warning{
    color:#fff!important;
}

.date-box{
    width:56px;
    flex:none;
    text-align:center;
    padding:.45rem 0;
    border-radius:15px;
    background:var(--bs-primary);
    color:var(--bs-body-color);
}

/* ---- SK Members page ---- */
.section-label{
    display:inline-flex;
    align-items:center;
    gap:.4rem;
    background:var(--bs-primary);
    color:var(--bs-body-color);
    font-size:.7rem;
    font-weight:800;
    letter-spacing:.6px;
    padding:.3rem .75rem;
    border-radius:999px;
}
.form-control,
.form-select{
    border:1px solid var(--bs-border-color);
    border-radius:12px;
    box-shadow:none!important;
}
.form-control:focus,
.form-select:focus{
    border-color:var(--bs-body-color);
    box-shadow:0 0 0 3px rgba(var(--bs-primary-rgb),.28)!important;
}
html{
    scroll-behavior:smooth;
}
    
/* sidebar.css makes modal titles white, so the header needs the primary colour */
.modal-header{ background:var(--bs-primary); border-bottom:0; border-radius:22px 22px 0 0; }

/* ---- meeting view / print ---- */
.kv-l{ font-size:.74rem; color:var(--bs-secondary-color); }
.kv-v{ font-size:.9rem; font-weight:700; word-break:break-word; }
.group-title{ font-weight:800; font-size:.82rem; padding-bottom:6px; margin:20px 0 10px; border-bottom:2px solid var(--bs-primary); }
.modal-content{ border-radius:22px; }
.min-w-0{ min-width:0; }
#printArea{ display:none; }
@media print{
    body > *:not(#printArea){ display:none!important; }
    #printArea{ display:block!important; padding:10px; color:#000; font-size:12px; }
    #printArea .group-title{ border-bottom:2px solid #000; }
    #printArea table{ width:100%; border-collapse:collapse; }
    #printArea th,#printArea td{ border:1px solid #999; padding:4px 6px; text-align:left; vertical-align:top; }
    #printArea .minutes-content p{ margin:0 0 6px; }
    #printArea .minutes-content ul,#printArea .minutes-content ol{ margin:4px 0 8px; padding-left:22px; }
    #printArea .minutes-content img{ display:none!important; }
}

/* ---- add / edit meeting: scrollable form, Save always visible ---- */
#meetingModal .modal-body{ overflow-y:auto; max-height:calc(100vh - 190px); }
#meetingModal .modal-footer{ position:sticky; bottom:0; background:#fff; border-top:1px solid var(--bs-border-color); border-radius:0 0 22px 22px; }

/* Meeting dashboard refresh */
body{background:#f2f6ff;color:#15244b}
.page-content{min-width:0}
.page-content>header{background:rgba(255,255,255,.94)!important;border-bottom:1px solid #e3eaf8!important;backdrop-filter:blur(14px)}
.page-content>header h1{color:#12234b;font-size:1.05rem}
.page-content>main.container-fluid{width:100%;max-width:none;margin:0;padding:1.35rem clamp(1rem,2vw,2rem)!important}
body:not(.dashboard-page) .page-content>main.container-fluid{width:100%;max-width:none;margin:0;padding:1.35rem clamp(1rem,2vw,2rem)!important}
.meeting-welcome{display:flex;align-items:flex-end;justify-content:space-between;gap:1rem;margin:0 0 1.1rem}
.meeting-welcome h2{color:#14234c;font-size:clamp(1.35rem,2vw,1.8rem);font-weight:800;letter-spacing:-.04em;margin:0 0 .28rem}
.meeting-welcome p{color:#6e7c99;margin:0;font-size:.9rem}
.meeting-date{display:inline-flex;align-items:center;gap:.45rem;padding:.65rem .9rem;background:#fff;border:1px solid #e3eaf8;border-radius:999px;color:#234c9a;font-size:.83rem;font-weight:700;box-shadow:0 5px 16px rgba(27,71,150,.06)}
.meeting-dashboard{--dash-blue:#1464ed;--dash-ink:#14234c;--dash-muted:#74819b;--dash-line:#e4ebf9;--dash-shadow:0 7px 20px rgba(36,77,151,.08)}
.meeting-hero{position:relative;display:flex;align-items:center;min-height:164px;overflow:hidden;padding:1.25rem 1.4rem;border-radius:19px;color:#fff;background:linear-gradient(120deg,#0754db 0%,#176ff3 65%,#438bff 100%);box-shadow:0 10px 24px rgba(22,91,220,.2)}
.meeting-hero:before,.meeting-hero:after{position:absolute;content:"";border-radius:50%;pointer-events:none}
.meeting-hero:before{width:230px;height:230px;right:-68px;top:-137px;background:rgba(255,255,255,.12);box-shadow:0 0 0 25px rgba(255,255,255,.05),0 0 0 55px rgba(255,255,255,.035)}
.meeting-hero:after{width:180px;height:180px;right:80px;bottom:-142px;background:rgba(255,255,255,.1)}
.meeting-hero-copy{position:relative;z-index:1;max-width:78%}
.meeting-hero-label{font-size:.77rem;opacity:.86;font-weight:650}
.meeting-hero h3{font-size:clamp(1.2rem,2vw,1.55rem);font-weight:800;letter-spacing:-.03em;margin:.22rem 0 .55rem}
.meeting-hero-meta{display:flex;flex-wrap:wrap;gap:.35rem .85rem;font-size:.78rem;color:rgba(255,255,255,.91)}
.meeting-hero-art{position:absolute;right:1.25rem;bottom:1.25rem;z-index:1;width:90px;height:90px;display:grid;place-items:center;color:#fff;font-size:4rem;opacity:.82}
.meeting-stat-icon{display:grid;place-items:center;width:37px;height:37px;border-radius:50%;background:#eaf1ff;color:#1260e8;font-size:1.05rem}
.dashboard-stat-row{display:flex;align-items:center;gap:.65rem;padding:.65rem 0;border-top:1px solid #edf1f8}
.dashboard-stat-row:first-child{padding-top:0;border-top:0}
.dashboard-stat-label{color:#34466f;font-size:.78rem;font-weight:700}
.dashboard-stat-row strong{margin-left:auto;color:var(--dash-ink);font-size:1.35rem;font-weight:850;line-height:1}
.meeting-panel{height:100%;overflow:hidden;border:1px solid #e2eafa!important;border-radius:19px!important;background:#fff!important;box-shadow:var(--dash-shadow)!important}
.meeting-panel>.card-header{padding:1rem 1.1rem!important;background:#fff!important;border-bottom:1px solid #e9eef8!important}
.meeting-panel>.card-header h2{margin:0;color:#14234c;font-size:.95rem;font-weight:800}
.meeting-panel>.card-body{padding:1rem 1.1rem}
.meeting-panel-title{display:flex;align-items:center;gap:.55rem}
.meeting-panel-title>i{color:#1768ef;font-size:1.1rem}
.create-meeting-prompt{display:flex;min-height:132px;flex-direction:column;align-items:center;justify-content:center;text-align:center;border-radius:14px;background:#edf3ff;color:#2853a5;padding:1rem}
.create-meeting-prompt i{color:#397ef4;font-size:2.45rem;line-height:1}
.create-meeting-prompt p{max-width:230px;margin:.65rem 0 0;font-size:.78rem}
.create-meeting-button{width:100%;margin-top:.65rem;border-radius:11px!important;padding:.65rem!important}
.upcoming-meeting-summary{display:flex;align-items:center;gap:.8rem;min-width:0}
.upcoming-meeting-copy{min-width:0;flex:1}
.upcoming-meeting-copy strong,.upcoming-meeting-copy span{display:block;overflow:hidden;text-overflow:ellipsis}
.upcoming-meeting-copy strong{color:#17284f;font-size:.86rem}
.upcoming-meeting-copy span{margin-top:.25rem;color:#71809b;font-size:.75rem}
.upcoming-meeting-actions{display:flex;flex-wrap:wrap;align-items:center;gap:.55rem;margin-top:.85rem}
.upcoming-meeting-invites{color:#71809b;font-size:.74rem}
.meeting-list{gap:.65rem}
.meeting-record{display:grid!important;grid-template-columns:56px minmax(0,1fr) 190px;align-items:center!important;gap:.9rem!important;padding:1rem!important;border:1px solid #e3eaf8!important;border-radius:15px!important;background:#fff!important}
.meeting-record-details{min-width:0}
.meeting-record-heading{display:flex;flex-wrap:wrap;align-items:center;gap:.4rem;color:#17284f}
.meeting-record-heading strong{overflow-wrap:anywhere}
.meeting-record-meta{margin-top:.35rem;color:#667796;font-size:.77rem}
.meeting-record-agenda{margin-top:.25rem;color:#506083;font-size:.76rem}
.meeting-record-counts{margin-top:.35rem;color:#74819b;font-size:.73rem}
.meeting-record-actions{display:flex;flex-wrap:wrap;justify-content:flex-end;gap:.35rem}
.meeting-record-actions .btn{display:grid;width:34px;height:34px;flex:none;place-items:center;padding:.25rem}
.attendance-visual{display:grid;grid-template-columns:minmax(245px,.9fr) minmax(0,1.6fr);gap:1rem;align-items:center}
.attendance-summary{display:flex;align-items:center;justify-content:space-around;gap:.8rem;min-width:0}
.attendance-ring-wrap{display:flex;flex:none;justify-content:center;align-items:center;min-height:176px}
.attendance-ring{--ring-value:0%;position:relative;display:grid;place-items:center;width:152px;height:152px;border-radius:50%;background:conic-gradient(#1464ed var(--ring-value),#dce9ff 0)}
.attendance-ring:before{content:"";position:absolute;inset:18px;border-radius:50%;background:#fff}
.attendance-ring-center{position:relative;text-align:center;color:#14234c}
.attendance-ring-center strong{display:block;font-size:1.65rem;line-height:1;font-weight:850}
.attendance-ring-center span{display:block;margin-top:.25rem;font-size:.7rem;color:#73809a}
.attendance-legend{display:grid;gap:.55rem}
.attendance-legend-item{display:flex;align-items:center;gap:.5rem;color:#506083;font-size:.8rem}
.attendance-legend-item strong{margin-left:auto;color:#1b2b53}
.attendance-dot{width:10px;height:10px;border-radius:50%;background:#1768ef}
.attendance-dot.late{background:#69b1ff}.attendance-dot.excused{background:#c9d4eb}.attendance-dot.absent{background:#dce4f2}
.attendance-trend{min-width:0;height:186px;border-top:1px solid #edf1f8}
.attendance-trend-chart{display:block;width:100%;height:100%;overflow:visible}
.attendance-trend-grid{stroke:#e6edf9;stroke-width:1}
.attendance-trend-line{fill:none;stroke:#1768ef;stroke-width:3;stroke-linecap:round;stroke-linejoin:round}
.attendance-trend-area{fill:url(#attendanceTrendFill)}
.attendance-trend-point{fill:#1768ef;stroke:#fff;stroke-width:2}
.attendance-trend-label{fill:#667796;font:12px system-ui,-apple-system,"Segoe UI",sans-serif}
.dashboard-overview-row .attendance-ring-wrap{min-height:132px}
.dashboard-overview-row .attendance-ring{width:120px;height:120px}
.dashboard-overview-row .attendance-ring:before{inset:14px}
.dashboard-overview-row .attendance-ring-center strong{font-size:1.3rem}
.dashboard-overview-row .attendance-trend{height:132px}
.recent-attendance-list{display:grid;gap:0}
.recent-attendance-row{display:grid;grid-template-columns:minmax(88px,.9fr) minmax(0,1.5fr) auto;align-items:center;gap:.55rem;padding:.62rem 0;border-top:1px solid #edf1f8;font-size:.75rem}
.recent-attendance-row:first-child{border-top:0;padding-top:0}
.recent-attendance-row span{overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.recent-attendance-row .recent-date{color:#61708f}
.recent-attendance-row .recent-title{color:#24365f;font-weight:650}
.recent-attendance-status{padding:.2rem .48rem;border-radius:999px;background:#dff7f0;color:#078568;font-size:.68rem;font-weight:750}
.meeting-dashboard .btn-success{--bs-btn-bg:#1464ed;--bs-btn-border-color:#1464ed;--bs-btn-hover-bg:#0d54d0;--bs-btn-hover-border-color:#0d54d0;--bs-btn-color:#fff}
.meeting-dashboard .btn-outline-success{--bs-btn-color:#1761d9;--bs-btn-border-color:#cddcf8;--bs-btn-hover-bg:#eaf1ff;--bs-btn-hover-border-color:#b8cdf5;--bs-btn-hover-color:#124eb2}
.dashboard-card{border-color:#e2eafa!important;border-radius:18px!important;box-shadow:var(--dash-shadow)!important}
.dashboard-card .card-header{border-color:#e9eef8}
.date-box{background:#edf3ff;color:#145ce1}
.section-label{background:#eaf1ff;color:#145ce1}
.btn-success{--bs-btn-bg:#1464ed;--bs-btn-border-color:#1464ed;--bs-btn-hover-bg:#0d54d0;--bs-btn-hover-border-color:#0d54d0;--bs-btn-active-bg:#0d54d0;--bs-btn-active-border-color:#0d54d0;--bs-btn-color:#fff}
.btn-outline-success{--bs-btn-color:#1761d9;--bs-btn-border-color:#cddcf8;--bs-btn-hover-bg:#eaf1ff;--bs-btn-hover-border-color:#b8cdf5;--bs-btn-hover-color:#124eb2}
.form-control,.form-select{border-color:#dfe7f4;border-radius:10px}
.table{--bs-table-border-color:#edf1f8}
.table thead th{background:#f1f5ff;color:#607092;text-transform:none;letter-spacing:0;font-size:.76rem}
.list-group-item{border-color:#edf1f8}
@media(max-width:767.98px){
    .meeting-welcome{align-items:flex-start;flex-direction:column}
    .meeting-hero{min-height:150px}
    .meeting-hero-art{right:.75rem;font-size:3.2rem}
    .attendance-visual{grid-template-columns:1fr;gap:.7rem}
    .attendance-summary{justify-content:center}
    .attendance-ring-wrap{min-height:140px}
    .attendance-trend{height:170px}
    .attendance-ring{width:132px;height:132px}
    .attendance-ring:before{inset:16px}
    .meeting-record{grid-template-columns:48px minmax(0,1fr);gap:.7rem!important;padding:.8rem!important}
    .meeting-record .date-box{width:48px}
    .meeting-record-actions{grid-column:2;justify-content:flex-start}
}
@media(max-width:575.98px){
    .page-content>main.container-fluid{padding:1rem!important}
    body:not(.dashboard-page) .page-content>main.container-fluid{padding:1rem!important}
    .meeting-hero-copy{max-width:84%}
    .meeting-hero-meta{display:grid;gap:.25rem}
    .meeting-date{font-size:.75rem}
    .meeting-panel>.card-body{padding:.9rem}
}
html.dark-mode .page-content>header{background:rgba(29,40,61,.96)!important;border-color:#35435d!important}
html.dark-mode .page-content>header h1,
html.dark-mode .meeting-welcome h2,
html.dark-mode .meeting-panel>.card-header h2{color:#e4ebf8}
html.dark-mode .meeting-welcome p{color:#a7b5cf}
html.dark-mode .meeting-date,
html.dark-mode .meeting-panel{
    color:#e4ebf8;background:#222d40!important;border-color:#35435d!important
}
html.dark-mode .dashboard-stat-row{border-color:#35435d}
html.dark-mode .dashboard-stat-label,
html.dark-mode .dashboard-stat-row strong{color:#e4ebf8}
html.dark-mode .create-meeting-prompt{background:#293b58;color:#c5d8ff}
html.dark-mode .upcoming-meeting-copy strong{color:#e4ebf8}
html.dark-mode .upcoming-meeting-copy span,
html.dark-mode .upcoming-meeting-invites{color:#a7b5cf}
html.dark-mode .recent-attendance-row .recent-title{color:#e4ebf8}
html.dark-mode .attendance-ring-center{color:#eef3ff}
html.dark-mode .meeting-panel>.card-header{background:#222d40!important;border-color:#35435d!important}
html.dark-mode .meeting-record{background:#222d40!important;border-color:#35435d!important}
html.dark-mode .meeting-record-heading{color:#e4ebf8}
html.dark-mode .meeting-record-meta,
html.dark-mode .meeting-record-agenda,
html.dark-mode .meeting-record-counts{color:#a7b5cf}
html.dark-mode .attendance-ring:before{background:#222d40}
html.dark-mode .attendance-legend,
html.dark-mode .attendance-legend-item,
html.dark-mode .recent-attendance-row .recent-date{color:#a7b5cf}
html.dark-mode .attendance-legend-item strong{color:#e4ebf8}
html.dark-mode .attendance-trend{border-color:#35435d}
html.dark-mode .attendance-trend-grid{stroke:#35435d}
html.dark-mode .attendance-trend-label{fill:#a7b5cf}
html.dark-mode .attendance-trend-point{stroke:#222d40}
html.dark-mode .recent-attendance-row{border-color:#35435d}
</style>
<?php include __DIR__ . '/init.php'; ?>
</head>

<body>

<div class="top-strip"></div>

<script src="theme.js"></script>
<script src="sidebar.js?v=16"></script>


<div class="page-content">

    <!-- HEADER -->
    <header class="bg-white border-bottom sticky-top">

        <div class="d-flex align-items-center gap-2 gap-md-3 px-3 px-lg-4 py-2 py-lg-3">

            <button class="btn btn-outline-success d-lg-none"
                    type="button"
                    data-bs-toggle="offcanvas"
                    data-bs-target="#sidebar"
                    aria-controls="sidebar">

                <i class="bi bi-list fs-5"></i>

            </button>

            <div>
                <h1 class="h5 fw-bold mb-0">
                    SK Meetings
                </h1>

                <div class="text-muted small d-none d-sm-block">
                    Meetings and attendance of SK officials
                </div>
            </div>

            <div class="ms-auto header-account-area">

                <a href="announcements.php"
                   class="header-notification"
                   aria-label="Announcements">

                    <i class="bi bi-bell"></i>

                </a>

                <span class="header-avatar">
                    <?= e(uinit()) ?>
                </span>

                <div class="d-none d-md-block header-identity">

                    <div class="header-identity-name">
                        <?= e(uname()) ?>
                    </div>

                    <div class="header-identity-role">
                        <?= e(strtoupper(urole())) ?>
                    </div>

                </div>

                <a href="<?= is_public() ? 'login.php' : 'logout.php' ?>"
                   class="btn btn-sm header-logout">

                    <i class="bi bi-box-arrow-right me-1"></i>
                    <?= is_public() ? 'Official Login' : 'Logout' ?>

                </a>

            </div>

        </div>

    </header>


    <main class="container-fluid p-3 p-lg-4">

        <section class="meeting-dashboard" aria-labelledby="meetingDashboardTitle">
            <div class="meeting-welcome">
                <div>
                    <h2 id="meetingDashboardTitle">Mabuhay, SK Officials!</h2>
                    <p>Here’s the latest overview of your meetings and attendance.</p>
                </div>
                <div class="meeting-date"><i class="bi bi-calendar3"></i><span id="dashboardDate"></span></div>
            </div>

            <div class="row g-3 mb-3 align-items-stretch dashboard-overview-row">
                <div class="col-12 col-xl-4">
                    <article class="meeting-hero h-100" aria-live="polite">
                        <div class="meeting-hero-copy">
                            <div class="meeting-hero-label">SK MANAGEMENT</div>
                            <h3>Meetings &amp; Attendance</h3>
                            <div class="meeting-hero-meta">
                                <span><i class="bi bi-calendar-event me-1"></i>Create and manage meeting records</span>
                                <span><i class="bi bi-clipboard2-check me-1"></i>Track attendance in one place</span>
                            </div>
                        </div>
                        <i class="bi bi-calendar2-week meeting-hero-art" aria-hidden="true"></i>
                    </article>
                </div>
                <div class="col-12 col-md-6 col-xl-3">
                    <section class="card meeting-panel h-100" aria-label="Meeting statistics">
                        <div class="card-body d-flex flex-column justify-content-center">
                            <div class="dashboard-stat-row">
                                <span class="meeting-stat-icon"><i class="bi bi-calendar-check"></i></span>
                                <span class="dashboard-stat-label">Total Meetings</span>
                                <strong id="statMeetings">0</strong>
                            </div>
                            <div class="dashboard-stat-row">
                                <span class="meeting-stat-icon"><i class="bi bi-people-fill"></i></span>
                                <span class="dashboard-stat-label">SK Officials</span>
                                <strong id="statActiveSk">0</strong>
                            </div>
                            <div class="dashboard-stat-row">
                                <span class="meeting-stat-icon"><i class="bi bi-check2-circle"></i></span>
                                <span class="dashboard-stat-label">Completed</span>
                                <strong id="statCompletedMeetings">0</strong>
                            </div>
                        </div>
                    </section>
                </div>
                <div class="col-12 col-md-6 col-xl-5">
                    <section class="card meeting-panel h-100" aria-labelledby="attendanceOverviewTitle">
                        <div class="card-header d-flex justify-content-between align-items-center gap-2">
                            <div class="meeting-panel-title"><i class="bi bi-bar-chart-fill"></i><h2 id="attendanceOverviewTitle">Attendance Overview</h2></div>
                        </div>
                        <div class="card-body">
                            <div class="attendance-visual">
                                <div class="attendance-summary">
                                    <div class="attendance-ring-wrap">
                                        <div class="attendance-ring" id="attendanceRing" role="img" aria-label="Attendance rate">
                                            <div class="attendance-ring-center"><strong id="attendanceRingRate">0%</strong><span>Attendance<br>Rate</span></div>
                                        </div>
                                    </div>
                                    <div class="attendance-legend" id="attendanceLegend"></div>
                                </div>
                                <div class="attendance-trend" id="attendanceTrend" aria-label="Attendance by recent meeting"></div>
                            </div>
                            <div class="d-flex justify-content-end mt-3">
                                <button type="button" class="btn btn-success btn-sm" onclick="openAttendanceModal()">
                                    <i class="bi bi-clipboard2-check me-1"></i>Record Attendance
                                </button>
                            </div>
                        </div>
                    </section>
                </div>
            </div>

            <div class="row g-3 mb-3 align-items-stretch">
                <div class="col-12 col-md-6">
                    <section class="card meeting-panel" aria-labelledby="addMeetingTitle">
                        <div class="card-header">
                            <div class="meeting-panel-title"><i class="bi bi-plus-square-fill"></i><h2 id="addMeetingTitle">Add Meeting</h2></div>
                        </div>
                        <div class="card-body">
                            <div class="create-meeting-prompt">
                                <i class="bi bi-calendar2-plus" aria-hidden="true"></i>
                                <p>Schedule a new SK meeting and invite officials.</p>
                            </div>
                            <button type="button" class="btn btn-success create-meeting-button" onclick="openMeetingForm()">
                                <i class="bi bi-plus-lg me-2"></i>Create Meeting
                            </button>
                        </div>
                    </section>
                </div>
                <div class="col-12 col-md-6">
                    <section class="card meeting-panel" id="sk-meetings" aria-labelledby="manageMeetingsTitle">
                        <div class="card-header">
                            <div class="meeting-panel-title"><i class="bi bi-calendar-event"></i><h2 id="manageMeetingsTitle">SK Meetings</h2></div>
                        </div>
                        <div class="card-body">
                            <div class="create-meeting-prompt">
                                <i class="bi bi-calendar2-week" aria-hidden="true"></i>
                                <p>Search, review, edit, and manage your meeting records.</p>
                            </div>
                            <button type="button" class="btn btn-success create-meeting-button" onclick="openMeetingsModal()">
                                <i class="bi bi-list-ul me-2"></i>View Meetings
                            </button>
                        </div>
                    </section>
                </div>
            </div>

            <div class="row g-3 mb-4 align-items-stretch">
                <div class="col-12 col-xl-4">
                    <section class="card meeting-panel" aria-labelledby="upcomingMeetingTitle">
                        <div class="card-header">
                            <div class="meeting-panel-title"><i class="bi bi-calendar-event"></i><h2 id="upcomingMeetingTitle">Upcoming Meeting</h2></div>
                        </div>
                        <div class="card-body" id="upcomingMeetingCard">
                            <div class="small text-muted py-2">No upcoming meetings scheduled.</div>
                        </div>
                    </section>
                </div>
                <div class="col-12 col-xl-8">
                    <section class="card meeting-panel" aria-labelledby="recentAttendanceTitle">
                        <div class="card-header">
                            <div class="meeting-panel-title"><i class="bi bi-calendar-week"></i><h2 id="recentAttendanceTitle">Recent Meetings</h2></div>
                        </div>
                        <div class="card-body"><div class="recent-attendance-list" id="recentAttendanceList"></div></div>
                    </section>
                </div>
            </div>
        </section>

    </main>
</div>

<!-- ================================================================
     ADD / EDIT MEETING POPUP
     ================================================================ -->
<div class="modal fade" id="meetingModal" tabindex="-1" aria-labelledby="meetingModalTitle" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable modal-fullscreen-md-down">
        <div class="modal-content" style="border-radius:22px;">
            <form id="meetingForm" novalidate>

                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="meetingModalTitle">Add Meeting</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">

                        <div class="col-md-8">
                            <label class="form-label fw-semibold" for="meetingTitle">Meeting Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="meetingTitle" maxlength="100" placeholder="e.g. Regular SK Session">
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold" for="meetingType">Meeting Type <span class="text-danger">*</span></label>
                            <select class="form-select" id="meetingType"></select>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="col-12 d-none" id="typeOtherBox">
                            <label class="form-label fw-semibold" for="meetingTypeOther">What kind of meeting is this? <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="meetingTypeOther" maxlength="60" placeholder="e.g. Orientation Meeting, Coordination Meeting">
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold" for="startDate">Start Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="startDate">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" for="startTime">Start Time <span class="text-danger">*</span></label>
                            <input type="time" class="form-control" id="startTime">
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold" for="endDate">End Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="endDate">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" for="endTime">End Time <span class="text-danger">*</span></label>
                            <input type="time" class="form-control" id="endTime">
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="col-md-8">
                            <label class="form-label fw-semibold" for="meetingLocation">Location / Venue <span class="text-danger">*</span></label>
                            <select class="form-select" id="meetingLocation"></select>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="col-12 d-none" id="locOtherBox">
                            <label class="form-label fw-semibold" for="meetingLocationOther">Specify the venue <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="meetingLocationOther" maxlength="120" placeholder="e.g. Purok 3 Chapel">
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold" for="meetingStatus">Status</label>
                            <select class="form-select" id="meetingStatus"></select>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold" for="meetingAgenda">Description / Agenda</label>
                            <textarea class="form-control" id="meetingAgenda" rows="3" maxlength="800" placeholder="e.g. Resolution preparation, budget review"></textarea>
                        </div>

                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label fw-semibold mb-0">Attendees <span class="fw-normal text-muted">(active SK members)</span></label>
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-outline-success" id="attAll">Select all</button>
                                    <button type="button" class="btn btn-outline-success" id="attNone">Clear</button>
                                </div>
                            </div>
                            <div class="border rounded-4 p-2" id="attendeeBox" style="max-height:210px;overflow:auto;"></div>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold" for="meetingRemarks">Remarks</label>
                            <textarea class="form-control" id="meetingRemarks" rows="2" maxlength="500" placeholder="Optional notes about this meeting"></textarea>
                        </div>

                        <div class="col-12" id="minutesPanel">
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
                                <label class="form-label fw-semibold mb-0" for="meetingMinutes">Minutes</label>
                                <span class="badge text-bg-secondary" id="minutesStatus">Draft</span>
                            </div>
                            <textarea class="form-control" id="meetingMinutes"></textarea>
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mt-2">
                                <small class="text-muted" id="minutesCount">0 / 20,000 characters</small>
                                <small class="text-muted" id="minutesUpdated"></small>
                            </div>
                            <div class="d-flex flex-wrap gap-2 mt-2">
                                <button type="button" class="btn btn-outline-success btn-sm" id="minutesSaveBtn">Save Draft</button>
                                <button type="button" class="btn btn-success btn-sm" id="minutesApproveBtn">Approve Minutes</button>
                                <button type="button" class="btn btn-outline-warning btn-sm" id="minutesReopenBtn">Reopen Minutes</button>
                            </div>
                            <div class="form-text">Only the SK Secretary and SK Chairman can edit draft minutes. Only the Chairman can approve or reopen approved minutes.</div>
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success" id="meetingSubmitBtn">Save Meeting</button>
                </div>

            </form>
        </div>
    </div>
</div>

<!-- ================================================================
     SK MEETINGS MANAGEMENT POPUP
     ================================================================ -->
<div class="modal fade" id="meetingsModal" tabindex="-1" aria-labelledby="meetingsModalTitle" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable modal-fullscreen-md-down">
        <div class="modal-content" style="border-radius:22px;">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title fw-bold" id="meetingsModalTitle"><i class="bi bi-calendar-event me-2"></i>SK Meetings</h5>
                    <small class="text-white-50">Search, view, edit, print, and manage meeting records.</small>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="modal-body">
                <div class="row g-2 mb-3">
                    <div class="col-12 col-md-6">
                        <input type="search" class="form-control" id="meetSearch" aria-label="Search meetings" placeholder="Search title, type, location, agenda, attendee...">
                    </div>
                    <div class="col-6 col-md-3">
                        <select class="form-select" id="meetTypeFilter" aria-label="Filter by meeting type"></select>
                    </div>
                    <div class="col-6 col-md-3">
                        <select class="form-select" id="meetStatusFilter" aria-label="Filter by meeting status"></select>
                    </div>
                </div>
                <div class="list-group meeting-list" id="meetingList"></div>
                <div class="small text-muted mt-3" id="meetCount" aria-live="polite"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- ================================================================
     RECORD MEETING ATTENDANCE POPUP
     ================================================================ -->
<div class="modal fade" id="attendanceModal" tabindex="-1" aria-labelledby="attendanceModalTitle" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable modal-fullscreen-md-down">
        <div class="modal-content" style="border-radius:22px;">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title fw-bold" id="attendanceModalTitle"><i class="bi bi-clipboard2-check me-2"></i>SK Meeting Attendance</h5>
                    <small class="text-white-50">Record attendance for active SK officials and invited attendees.</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="attendanceMessage"></div>
                <div class="row g-2 mb-3">
                    <div class="col-12 col-lg-5">
                        <label class="form-label fw-semibold" for="attendanceMeeting">Meeting</label>
                        <select class="form-select" id="attendanceMeeting"></select>
                    </div>
                    <div class="col-12 col-md-7 col-lg-4">
                        <label class="form-label fw-semibold" for="attendanceSearch">Find official</label>
                        <input type="search" class="form-control" id="attendanceSearch" placeholder="Search name or position" autocomplete="off">
                    </div>
                    <div class="col-12 col-md-5 col-lg-3">
                        <label class="form-label fw-semibold" for="attendanceStatusFilter">Attendance status</label>
                        <select class="form-select" id="attendanceStatusFilter"></select>
                    </div>
                </div>
                <div class="small text-muted mb-2" id="attendanceCount" aria-live="polite"></div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr><th>SK Member</th><th>Position</th><th>Meeting Date</th><th>Status</th><th>Remarks</th></tr>
                        </thead>
                        <tbody id="attendanceBody"></tbody>
                    </table>
                </div>
                <div class="small text-muted text-center py-3 d-none" id="attendanceFilterEmpty">No officials match the current search or status filter.</div>
                <small class="text-muted">Attendance is saved when you click Save Attendance.</small>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" id="saveAttendanceBtn" onclick="saveAttendance()">
                    <i class="bi bi-save me-2"></i>Save Attendance
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ================================================================
     VIEW MEETING POPUP
     ================================================================ -->
<div class="modal fade" id="viewModal" tabindex="-1" aria-labelledby="viewModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable modal-fullscreen-md-down">
        <div class="modal-content" style="border-radius:22px;">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="viewModalTitle">Meeting Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="viewBody"></div>
            <div class="modal-footer flex-wrap gap-2" id="viewFooter"></div>
        </div>
    </div>
</div>

<div id="printArea"></div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="app-alerts.js?v=2"></script>
<script src="vendor/jquery/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script src="vendor/summernote/summernote-bs5.min.js"></script>
<script src="vendor/dompurify/purify.min.js"></script>
<script src="minutes-render.js"></script>
<script src="sk-data.js"></script>
<script>
(function () {
    'use strict';

    var D = window.SKData;
    var MEET_KEY = D.KEYS.MEET, ATT_KEY = D.KEYS.ATT;
    var MINUTES_ROLE = <?= json_encode(urole(), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
    var MINUTES_CAN_EDIT = MINUTES_ROLE === 'SK Secretary' || MINUTES_ROLE === 'SK Chairman';
    var MINUTES_IS_CHAIRMAN = MINUTES_ROLE === 'SK Chairman';
    var MINUTES_LIMIT = 20000;
    var TYPES = ['Regular Meeting', 'Special Meeting', 'Emergency Meeting', 'Committee Meeting', 'Planning Meeting', 'Budget Meeting', 'Other'];
    var MSTATUS = ['Scheduled', 'Ongoing', 'Completed', 'Postponed', 'Cancelled'];
    var ASTATUS = ['Present', 'Late', 'Excused', 'Absent'];
    var VENUES = ['Barangay Hall', 'SK Office', 'Barangay Session Hall', 'Barangay Multi-purpose Hall', 'Barangay Covered Court', 'Barangay Health Center', 'Day Care Center', 'School / Classroom', 'Online (Zoom / Google Meet)', 'Other'];
    var BARANGAY = 'Sikat', CITY = 'Alfonso', PROVINCE = 'Cavite';

    function $(id) { return document.getElementById(id); }
    function esc(s) {
        return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
        });
    }
    function fmtDate(iso) {
        var d = new Date(iso + 'T00:00:00');
        return isNaN(d) ? '\u2014' : d.toLocaleDateString('en-PH', { year: 'numeric', month: 'short', day: 'numeric' });
    }
    function fmtTime(t) {
        if (!t) return '';
        var p = t.split(':'), h = +p[0], ap = h >= 12 ? 'PM' : 'AM';
        return ((h % 12) || 12) + ':' + p[1] + ' ' + ap;
    }
    function modal(id) { return bootstrap.Modal.getOrCreateInstance($(id)); }
    function isOpen(id) { return $(id).classList.contains('show'); }
    function afterHide(id, fn) {
        if (!isOpen(id)) { fn(); return; }
        $(id).addEventListener('hidden.bs.modal', function once() { $(id).removeEventListener('hidden.bs.modal', once); fn(); });
        modal(id).hide();
    }
    function note(box, type, text) {
        $(box).innerHTML = '<div class="alert alert-' + type + ' small">' + esc(text) + '</div>';
        setTimeout(function () { $(box).innerHTML = ''; }, 4000);
    }
    function setErr(el, msg) {
        el.classList.add('is-invalid');
        var fb = el.parentElement.querySelector('.invalid-feedback');
        if (fb) { fb.textContent = msg; fb.style.display = 'block'; }
    }
    function clearErrs(scope) {
        scope.querySelectorAll('.is-invalid').forEach(function (e) { e.classList.remove('is-invalid'); });
        scope.querySelectorAll('.invalid-feedback').forEach(function (e) { e.textContent = ''; e.style.display = ''; });
    }
    function opts(sel, list, first) {
        sel.innerHTML = (first !== undefined ? '<option value="">' + esc(first) + '</option>' : '') + list.map(function (x) { return '<option>' + esc(x) + '</option>'; }).join('');
    }

    /* ---------- data ---------- */
    var meetings = [], attendance = {}, meetingSaveError = '';

    function normMeeting(m) {          // also upgrades meetings saved by the older version of this page
        m.startDate = m.startDate || m.date || '';
        m.startTime = m.startTime || m.time || '';
        m.endDate = m.endDate || m.startDate;
        m.endTime = m.endTime || '';
        m.location = m.location != null ? m.location : (m.venue || '');
        m.type = TYPES.indexOf(m.type) > -1 ? m.type : 'Regular Meeting';
        m.typeOther = m.type === 'Other' ? (m.typeOther || '') : '';
        m.agenda = m.agenda || '';
        m.remarks = m.remarks || '';
        m.attendees = Array.isArray(m.attendees) ? m.attendees : [];
        m.minutes = typeof m.minutes === 'string' ? m.minutes : '';
        m.minutesStatus = m.minutesStatus === 'Approved' ? 'Approved' : 'Draft';
        m.minutesUpdatedBy = typeof m.minutesUpdatedBy === 'string' ? m.minutesUpdatedBy : '';
        m.minutesUpdatedAt = typeof m.minutesUpdatedAt === 'string' ? m.minutesUpdatedAt : '';
        if (MSTATUS.indexOf(m.status) < 0) m.status = (m.startDate && m.startDate < D.todayISO()) ? 'Completed' : 'Scheduled';
        delete m.date; delete m.time; delete m.venue;
        return m;
    }
    function load() {
        D.load();
        var mm = D.read(MEET_KEY, []);
        meetings = (Array.isArray(mm) ? mm : []).filter(function (m) { return m && m.id; }).map(normMeeting);
        attendance = D.read(ATT_KEY, {});
        if (!attendance || typeof attendance !== 'object' || Array.isArray(attendance)) attendance = {};
    }
    function saveMeetings() {
        meetingSaveError = '';
        try {
            DB.setItem(MEET_KEY, JSON.stringify(meetings));
            return true;
        } catch (error) {
            meetingSaveError = error.message || 'Could not save meeting data.';
            return false;
        }
    }
    function typeText(m) { return m.type === 'Other' && m.typeOther ? m.typeOther : m.type; }
    function stamp(m) { return m.startDate + 'T' + (m.startTime || '00:00'); }
    function nameOf(id) { var k = D.kkById(id); return k ? D.kkName(k) : 'Removed member'; }
    function posOf(id) { var r = D.roleOf(id); return r ? r.position : '\u2014'; }
    function rangeText(m) {
        var s = fmtDate(m.startDate) + (m.startTime ? ', ' + fmtTime(m.startTime) : '');
        var sameDay = m.endDate === m.startDate;
        var e = m.endTime || m.endDate !== m.startDate ? (sameDay ? fmtTime(m.endTime) : fmtDate(m.endDate) + (m.endTime ? ', ' + fmtTime(m.endTime) : '')) : '';
        return s + (e ? ' \u2013 ' + e : '');
    }
    function activeOfficers() {
        var seen = Object.create(null);
        return D.officers().filter(function (officer) {
            var id = String(officer.id || '');
            if (!id || seen[id]) return false;
            seen[id] = true;
            return true;
        });
    }
    function statusBadge(s) {
        var cls = s === 'Scheduled' || s === 'Ongoing' ? 'bg-dark' : 'bg-secondary';
        return '<span class="badge ' + cls + '">' + esc(s) + '</span>';
    }
    // Include all active officials, in addition to any invited KK members.
    function attendeeIds(m) {
        var ids = activeOfficers().map(function (o) { return o.id; });
        m.attendees.forEach(function (id) {
            if (D.kkById(id) && ids.indexOf(id) === -1) ids.push(id);
        });
        return ids;
    }

    /* ---------- meeting list ---------- */
    function matches(m) {
        var q = D.norm($('meetSearch').value), t = $('meetTypeFilter').value, s = $('meetStatusFilter').value;
        if (t && m.type !== t) return false;
        if (s && m.status !== s) return false;
        if (!q) return true;
        var text = [m.title, m.type, m.typeOther, m.location, m.agenda, m.remarks, m.status, m.startDate, fmtDate(m.startDate)]
            .concat(m.attendees.map(nameOf)).join(' ').toLowerCase();
        return text.indexOf(q) > -1;
    }
    function filteredMeetings() {
        var today = D.todayISO();
        var up = meetings.filter(function (m) { return m.startDate >= today && matches(m); }).sort(function (a, b) { return stamp(a).localeCompare(stamp(b)); });
        var past = meetings.filter(function (m) { return m.startDate < today && matches(m); }).sort(function (a, b) { return stamp(b).localeCompare(stamp(a)); });
        return up.concat(past);
    }
    function renderMeetings() {
        var list = filteredMeetings();
        $('meetCount').textContent = meetings.length ? 'Showing ' + list.length + ' of ' + meetings.length + ' meeting' + (meetings.length !== 1 ? 's' : '') : '';
        if (!meetings.length) {
            $('meetingList').innerHTML = '<div class="text-center text-muted py-4"><i class="bi bi-calendar-x fs-1 d-block mb-2"></i>' +
                '<div class="small">No meetings yet. Use Add Meeting above to create the first record.</div></div>';
            return;
        }
        if (!list.length) { $('meetingList').innerHTML = '<div class="text-center text-muted py-4 small"><i class="bi bi-search fs-1 d-block mb-2"></i>No meetings match your search or filters.</div>'; return; }
        $('meetingList').innerHTML = list.map(function (m) {
            var d = new Date(m.startDate + 'T00:00:00');
            var rec = attendance[m.id] ? Object.keys(attendance[m.id]).length : 0;
            return '<div class="list-group-item meeting-record">' +
                '<div class="date-box"><div class="fw-bold" style="font-size:.7rem;">' + esc(d.toLocaleDateString('en-US', { month: 'short' }).toUpperCase()) + '</div><div class="fw-bold fs-5 lh-1">' + d.getDate() + '</div></div>' +
                '<div class="meeting-record-details"><div class="meeting-record-heading"><strong>' + esc(m.title) + '</strong>' + statusBadge(m.status) +
                '<span class="badge bg-light text-dark border">' + esc(typeText(m)) + '</span></div>' +
                '<div class="meeting-record-meta"><i class="bi bi-clock me-1"></i>' + esc(rangeText(m)) +
                (m.location ? ' <span class="mx-1">\u2022</span><i class="bi bi-geo-alt me-1"></i>' + esc(m.location) : '') + '</div>' +
                (m.agenda ? '<div class="meeting-record-agenda text-truncate">' + esc(m.agenda) + '</div>' : '') +
                '<div class="meeting-record-counts">Invited: ' + m.attendees.length + ' <span class="mx-1">\u2022</span> Attendance records: ' + rec + '</div></div>' +
                '<div class="meeting-record-actions" role="group" aria-label="Actions for ' + esc(m.title) + '">' +
                '<button type="button" class="btn btn-sm btn-outline-success" title="View meeting" aria-label="View meeting" data-view="' + esc(m.id) + '"><i class="bi bi-eye"></i></button>' +
                '<button type="button" class="btn btn-sm btn-outline-success" title="Edit meeting" aria-label="Edit meeting" data-edit="' + esc(m.id) + '"><i class="bi bi-pencil"></i></button>' +
                '<button type="button" class="btn btn-sm btn-outline-success" title="Print meeting" aria-label="Print meeting" data-print="' + esc(m.id) + '"><i class="bi bi-printer"></i></button>' +
                '<button type="button" class="btn btn-sm btn-outline-danger" title="Delete meeting" aria-label="Delete meeting" data-del="' + esc(m.id) + '"><i class="bi bi-trash"></i></button></div>';
        }).join('');
    }

    /* ---------- add / edit meeting ---------- */
    var editingMeeting = null, formAttendees = [];
    var initialMinutes = '';

    function editorMinutes() { return window.jQuery('#meetingMinutes').summernote('code') || ''; }
    function safeMinutes(html) { return window.SKMinutes.sanitize(html); }
    function minutesTextCount(html) {
        var box = document.createElement('div');
        box.innerHTML = safeMinutes(html);
        return Array.from(box.textContent || '').length;
    }
    function minutesChanged(html) {
        var current = safeMinutes(html), initial = safeMinutes(initialMinutes);
        if (minutesTextCount(current) === 0 && minutesTextCount(initial) === 0) return false;
        return current !== initial;
    }
    function updateMinutesControls(meeting) {
        if (!MINUTES_CAN_EDIT) return;
        meeting = meeting || meetings.filter(function (item) { return item.id === editingMeeting; })[0] || null;
        var status = meeting ? meeting.minutesStatus : 'Draft';
        var locked = status === 'Approved';
        var html = editorMinutes();
        var count = minutesTextCount(html);
        var changed = minutesChanged(html);
        $('minutesStatus').textContent = status;
        $('minutesStatus').className = 'badge ' + (locked ? 'text-bg-success' : 'text-bg-secondary');
        $('minutesCount').textContent = count.toLocaleString() + ' / 20,000 characters';
        $('minutesCount').classList.toggle('text-danger', count > MINUTES_LIMIT);
        $('minutesUpdated').textContent = meeting && meeting.minutesUpdatedAt
            ? 'Last updated ' + meeting.minutesUpdatedAt + (meeting.minutesUpdatedBy ? ' by ' + meeting.minutesUpdatedBy : '')
            : '';
        if (window.jQuery('#meetingMinutes').data('summernote')) {
            if (locked) window.jQuery('#meetingMinutes').summernote('disable');
            else window.jQuery('#meetingMinutes').summernote('enable');
        }
        $('minutesSaveBtn').disabled = !editingMeeting || locked || count > MINUTES_LIMIT || !changed;
        $('minutesApproveBtn').classList.toggle('d-none', !MINUTES_IS_CHAIRMAN || status !== 'Draft');
        $('minutesApproveBtn').disabled = !editingMeeting || locked || count === 0 || count > MINUTES_LIMIT || changed;
        $('minutesReopenBtn').classList.toggle('d-none', !MINUTES_IS_CHAIRMAN || status !== 'Approved');
        $('minutesReopenBtn').disabled = !editingMeeting;
    }
    function replaceMeetingMinutes(updated) {
        meetings = meetings.map(function (meeting) {
            return meeting.id === updated.id ? normMeeting(updated) : meeting;
        });
        var current = meetings.filter(function (meeting) { return meeting.id === editingMeeting; })[0];
        if (current) {
            window.jQuery('#meetingMinutes').summernote('code', current.minutes);
            initialMinutes = current.minutes;
            updateMinutesControls(current);
        }
        renderMeetings();
    }
    function runMinutesAction(action, meetingId, html) {
        var updated = D.meetingMinutes(action, meetingId, html);
        replaceMeetingMinutes(updated);
        return updated;
    }

    window.jQuery('#meetingMinutes').summernote({
        height: 250,
        dialogsInBody: true,
        tooltip: false,
        toolbar: [
            ['style', ['bold', 'italic', 'underline']],
            ['para', ['ul', 'ol']],
            ['insert', ['link']],
            ['misc', ['undo', 'redo']]
        ],
        callbacks: {
            onChange: function () { updateMinutesControls(); },
            onKeydown: function (event) {
                if (event.ctrlKey || event.metaKey || event.altKey || Array.from(event.key || '').length !== 1) return;
                var selection = window.getSelection();
                var selectedLength = selection ? Array.from(selection.toString()).length : 0;
                if (minutesTextCount(editorMinutes()) - selectedLength + 1 > MINUTES_LIMIT) {
                    event.preventDefault();
                }
            },
            onImageUpload: function () {},
            onInit: function () {
                var editor = window.jQuery('#meetingMinutes').next('.note-editor').find('.note-editable');
                editor[0].addEventListener('paste', function (event) {
                    var clipboard = event.clipboardData;
                    if (!clipboard) return;
                    var pastedHtml = clipboard.getData('text/html') || '';
                    var pastedText = clipboard.getData('text/plain') || '';
                    var hasImage = /<img\b/i.test(pastedHtml) ||
                        Array.prototype.some.call(clipboard.items || [], function (item) { return item.type.indexOf('image/') === 0; });
                    var selection = window.getSelection();
                    var selectedLength = selection ? Array.from(selection.toString()).length : 0;
                    var available = MINUTES_LIMIT - minutesTextCount(editorMinutes()) + selectedLength;
                    if (!hasImage && minutesTextCount(pastedHtml || pastedText) <= available) return;
                    event.preventDefault();
                    event.stopImmediatePropagation();
                    if (hasImage && Array.from(pastedText).length <= available) {
                        document.execCommand('insertText', false, pastedText);
                    }
                }, true);
                editor[0].addEventListener('drop', function (event) {
                    var transfer = event.dataTransfer;
                    var hasImage = transfer && (Array.prototype.some.call(transfer.files || [], function (file) {
                        return file.type.indexOf('image/') === 0;
                    }) || /<img\b/i.test(transfer.getData('text/html') || ''));
                    if (hasImage) {
                        event.preventDefault();
                        event.stopImmediatePropagation();
                    }
                }, true);
            }
        }
    });
    if (!MINUTES_CAN_EDIT) $('minutesPanel').classList.add('d-none');

    function renderAttendeeBox() {
        var offs = activeOfficers(), ids = offs.map(function (o) { return o.id; });
        // keep already-invited people visible even if they are no longer active
        var extra = formAttendees.filter(function (id) { return ids.indexOf(id) < 0 && D.kkById(id); });
        var rows = offs.map(function (o) { return { id: o.id, name: o.name, pos: o.position, note: '' }; })
            .concat(extra.map(function (id) { return { id: id, name: nameOf(id), pos: posOf(id), note: ' (not active)' }; }));
        $('attendeeBox').innerHTML = rows.length ? rows.map(function (r) {
            return '<div class="form-check py-1"><input class="form-check-input att-check" type="checkbox" id="ac_' + esc(r.id) + '" value="' + esc(r.id) + '"' + (formAttendees.indexOf(r.id) > -1 ? ' checked' : '') + '>' +
                '<label class="form-check-label small" for="ac_' + esc(r.id) + '"><strong>' + esc(r.name) + '</strong> \u2013 ' + esc(r.pos) + esc(r.note) + '</label></div>';
        }).join('') : '<div class="small text-muted p-2">No active SK members yet. Assign SK roles to KK members in the <a href="members.php?type=SK">Member List</a>.</div>';
    }
    function checkedAttendees() { return Array.prototype.map.call(document.querySelectorAll('#attendeeBox .att-check:checked'), function (c) { return c.value; }); }

    window.openMeetingForm = function (id) {
        D.load();
        var m = id ? meetings.filter(function (x) { return x.id === id; })[0] : null;
        editingMeeting = m ? m.id : null;
        var f = $('meetingForm'); f.reset(); clearErrs(f);
        opts($('meetingType'), TYPES); opts($('meetingStatus'), MSTATUS); opts($('meetingLocation'), VENUES, 'Select venue');
        $('meetingModalTitle').textContent = m ? 'Edit Meeting' : 'Add Meeting';
        $('meetingSubmitBtn').textContent = m ? 'Update Meeting' : 'Save Meeting';
        formAttendees = m ? m.attendees.slice() : [];
        initialMinutes = m ? safeMinutes(m.minutes) : '';
        window.jQuery('#meetingMinutes').summernote('code', initialMinutes);
        if (m) {
            $('meetingTitle').value = m.title; $('meetingType').value = m.type; $('startDate').value = m.startDate; $('startTime').value = m.startTime;
            $('endDate').value = m.endDate; $('endTime').value = m.endTime; $('meetingStatus').value = m.status;
            $('meetingTypeOther').value = m.typeOther || '';
            if (VENUES.indexOf(m.location) > -1 && m.location !== 'Other') $('meetingLocation').value = m.location;
            else if (m.location) { $('meetingLocation').value = 'Other'; $('meetingLocationOther').value = m.location; }
            $('meetingAgenda').value = m.agenda; $('meetingRemarks').value = m.remarks;
        }
        syncOther();
        renderAttendeeBox();
        updateMinutesControls(m);
        modal('meetingModal').show();
    };
    function syncOther() {
        $('typeOtherBox').classList.toggle('d-none', $('meetingType').value !== 'Other');
        $('locOtherBox').classList.toggle('d-none', $('meetingLocation').value !== 'Other');
    }
    $('meetingType').addEventListener('change', function () { syncOther(); if (this.value === 'Other') $('meetingTypeOther').focus(); });
    $('meetingLocation').addEventListener('change', function () { syncOther(); if (this.value === 'Other') $('meetingLocationOther').focus(); });
    $('startDate').addEventListener('change', function () { if (this.value && (!$('endDate').value || $('endDate').value < this.value)) $('endDate').value = this.value; });
    $('attAll').addEventListener('click', function () { document.querySelectorAll('#attendeeBox .att-check').forEach(function (c) { c.checked = true; }); });
    $('attNone').addEventListener('click', function () { document.querySelectorAll('#attendeeBox .att-check').forEach(function (c) { c.checked = false; }); });

    $('meetingForm').addEventListener('submit', function (e) {
        e.preventDefault();
        var form = this; clearErrs(form);
        var currentMinutes = MINUTES_CAN_EDIT ? editorMinutes() : '';
        var minutesCount = MINUTES_CAN_EDIT ? minutesTextCount(currentMinutes) : 0;
        if (minutesCount > MINUTES_LIMIT) {
            AppAlerts.warning('Minutes cannot exceed 20,000 characters.');
            return;
        }
        var minutesEdited = MINUTES_CAN_EDIT && minutesChanged(currentMinutes);
        var rec = {
            id: editingMeeting || D.uid('mt'), title: $('meetingTitle').value.trim(), type: $('meetingType').value,
            startDate: $('startDate').value, startTime: $('startTime').value, endDate: $('endDate').value, endTime: $('endTime').value,
            typeOther: $('meetingType').value === 'Other' ? $('meetingTypeOther').value.trim() : '',
            location: $('meetingLocation').value === 'Other' ? $('meetingLocationOther').value.trim() : $('meetingLocation').value, agenda: $('meetingAgenda').value.trim(), status: $('meetingStatus').value,
            attendees: checkedAttendees(), remarks: $('meetingRemarks').value.trim()
        };
        var ok = true;
        function bad(el, msg) { setErr(el, msg); ok = false; }
        if (rec.title.length < 3) bad($('meetingTitle'), 'Enter the meeting title.');
        if (!rec.type) bad($('meetingType'), 'Select a meeting type.');
        if (rec.type === 'Other' && rec.typeOther.length < 3) bad($('meetingTypeOther'), 'Enter what kind of meeting this is.');
        if (!rec.startDate) bad($('startDate'), 'Start date is required.');
        if (!rec.startTime) bad($('startTime'), 'Start time is required.');
        if (!rec.endDate) bad($('endDate'), 'End date is required.');
        if (!rec.endTime) bad($('endTime'), 'End time is required.');
        if (rec.startDate && rec.endDate && rec.endDate < rec.startDate) bad($('endDate'), 'End date cannot be before the start date.');
        else if (rec.startDate && rec.endDate === rec.startDate && rec.startTime && rec.endTime && rec.endTime <= rec.startTime) bad($('endTime'), 'End time must be after the start time.');
        if (!$('meetingLocation').value) bad($('meetingLocation'), 'Select the venue.');
        else if ($('meetingLocation').value === 'Other' && rec.location.length < 3) bad($('meetingLocationOther'), 'Enter the venue.');
        if (ok && meetings.some(function (m) { return m.id !== rec.id && m.startDate === rec.startDate && m.startTime === rec.startTime && m.title.toLowerCase() === rec.title.toLowerCase(); }))
            bad($('meetingTitle'), 'A meeting with the same title, date and time already exists.');
        if (!ok) { var first = form.querySelector('.is-invalid'); if (first) first.focus(); return; }

        var before = meetings.slice();
        if (editingMeeting) meetings = meetings.map(function (m) { return m.id === rec.id ? rec : m; });
        else meetings.push(rec);
        if (!saveMeetings()) {
            meetings = before;
            AppAlerts.error('Could not save the meeting: ' + meetingSaveError);
            return;
        }
        editingMeeting = rec.id;
        D.load();
        var savedMeetings = D.read(MEET_KEY, []);
        meetings = (Array.isArray(savedMeetings) ? savedMeetings : []).filter(function (m) { return m && m.id; }).map(normMeeting);
        if (minutesEdited) {
            try {
                var updated = runMinutesAction('meeting_minutes_save', rec.id, currentMinutes);
                initialMinutes = updated.minutes;
            } catch (error) {
                AppAlerts.error('The meeting was saved, but its minutes were not saved: ' + error.message);
                $('meetingModalTitle').textContent = 'Edit Meeting';
                $('meetingSubmitBtn').textContent = 'Update Meeting';
                renderAll(rec.id);
                updateMinutesControls();
                return;
            }
        }
        modal('meetingModal').hide();
        renderAll(rec.id);
    });

    $('minutesSaveBtn').addEventListener('click', function () {
        if (!editingMeeting) return;
        try {
            runMinutesAction('meeting_minutes_save', editingMeeting, editorMinutes());
        } catch (error) {
            AppAlerts.error(error.message);
        }
    });
    $('minutesApproveBtn').addEventListener('click', function () {
        if (!editingMeeting) return;
        try {
            runMinutesAction('meeting_minutes_approve', editingMeeting);
        } catch (error) {
            AppAlerts.error(error.message);
        }
    });
    $('minutesReopenBtn').addEventListener('click', function () {
        if (!editingMeeting) return;
        AppAlerts.confirm('Reopen the approved minutes for editing?', 'Reopen minutes?', 'Reopen')
            .then(function (confirmed) {
                if (!confirmed) return;
                try {
                    runMinutesAction('meeting_minutes_reopen', editingMeeting);
                } catch (error) {
                    AppAlerts.error(error.message);
                }
            });
    });

    function deleteMeeting(id) {
        var m = meetings.filter(function (x) { return x.id === id; })[0];
        if (!m) return;
        AppAlerts.confirm('Delete the meeting "' + m.title + '"? Its attendance record will also be deleted.', 'Delete meeting?', 'Delete')
            .then(function (confirmed) {
                if (!confirmed) return;
                var before = meetings.slice();
                meetings = meetings.filter(function (x) { return x.id !== id; });
                if (!saveMeetings()) {
                    meetings = before;
                    AppAlerts.error('Could not delete the meeting: ' + meetingSaveError);
                    renderAll();
                    return;
                }
                try {
                    DB.deleteMeetingAttendance(id, attendance[id] || {});
                    delete attendance[id];
                } catch (error) {
                    note('attendanceMessage', 'warning', error.message || 'The meeting was deleted, but its attendance record could not be removed.');
                }
                renderAll();
            });
    }

    /* ---------- view / print ---------- */
    function kv(l, v) { return '<div class="col-sm-6"><div class="kv-l">' + esc(l) + '</div><div class="kv-v">' + (v === '' || v == null ? '\u2014' : esc(v)) + '</div></div>'; }
    function meetingHtml(m, includeMinutes) {
        var rec = attendance[m.id] || {}, ids = attendeeIds(m);
        var rows = ids.length ? '<div class="table-responsive"><table class="table table-sm align-middle mb-0"><thead><tr><th>SK Member</th><th>Position</th><th>Attendance</th><th>Remarks</th></tr></thead><tbody>' +
            ids.map(function (id) {
                var a = rec[id] || {};
                var correctionCount = Array.isArray(a.history) ? Math.max(0, a.history.length - 1) : 0;
                return '<tr><td>' + esc(nameOf(id)) + '</td><td>' + esc(posOf(id)) + '</td><td>' +
                    esc(a.status || 'Not recorded') + (correctionCount ? '<div class="small text-muted">' + correctionCount + ' correction' + (correctionCount === 1 ? '' : 's') + '</div>' : '') +
                    '</td><td>' + esc(a.remarks || '') + '</td></tr>';
            }).join('') + '</tbody></table></div>' : '<div class="small text-muted">No attendees.</div>';
        return '<div class="d-flex flex-wrap gap-2 align-items-center mb-2"><h4 class="fw-bold mb-0">' + esc(m.title) + '</h4>' + statusBadge(m.status) + '</div>' +
            '<div class="row g-3">' + kv('Meeting type', typeText(m)) + kv('Status', m.status) + kv('Start', fmtDate(m.startDate) + (m.startTime ? ', ' + fmtTime(m.startTime) : '')) +
            kv('End', fmtDate(m.endDate) + (m.endTime ? ', ' + fmtTime(m.endTime) : '')) + kv('Location', m.location) + kv('Attendees invited', String(m.attendees.length)) + '</div>' +
            '<div class="group-title">Description / Agenda</div><div class="small">' + esc(m.agenda || 'No agenda added.').replace(/\n/g, '<br>') + '</div>' +
            '<div class="group-title">Attendees and Attendance</div>' + rows +
            '<div class="group-title">Remarks</div><div class="small">' + esc(m.remarks || 'No remarks.').replace(/\n/g, '<br>') + '</div>' +
            (includeMinutes && m.minutes ? '<div class="group-title">Minutes <span class="badge ' +
                (m.minutesStatus === 'Approved' ? 'bg-success' : 'bg-secondary') + '">' + esc(m.minutesStatus) +
                '</span></div><div class="minutes-content">' + safeMinutes(m.minutes) + '</div>' : '');
    }
    function openView(id) {
        var m = meetings.filter(function (x) { return x.id === id; })[0];
        if (!m) return;
        $('viewBody').innerHTML = meetingHtml(m, true);
        $('viewFooter').innerHTML =
            '<button type="button" class="btn btn-outline-success btn-sm" data-print="' + esc(id) + '"><i class="bi bi-printer me-1"></i>Print</button>' +
            '<button type="button" class="btn btn-outline-success btn-sm" data-edit="' + esc(id) + '"><i class="bi bi-pencil me-1"></i>Edit</button>' +
            '<button type="button" class="btn btn-dark btn-sm ms-auto" data-bs-dismiss="modal">Close</button>';
        modal('viewModal').show();
    }
    function printHeader(title) {
        return '<div style="text-align:center;margin-bottom:12px;"><div style="font-weight:800;font-size:15px;">Sangguniang Kabataan \u2013 Barangay ' + BARANGAY + '</div>' +
            '<div>' + CITY + ', ' + PROVINCE + '</div><div style="font-weight:700;margin-top:6px;">' + esc(title) + '</div>' +
            '<div style="font-size:11px;">Printed ' + fmtDate(D.todayISO()) + '</div></div>';
    }
    function doPrint(html) { $('printArea').innerHTML = html; setTimeout(function () { window.print(); }, 150); }
    function printMeeting(id) {
        var m = meetings.filter(function (x) { return x.id === id; })[0];
        if (!m) return;
        Swal.fire({
            title: 'Print meeting record',
            input: 'checkbox',
            inputPlaceholder: 'Include minutes',
            inputValue: 0,
            showCancelButton: true,
            confirmButtonText: 'Print',
            cancelButtonText: 'Cancel'
        }).then(function (result) {
            if (result.isConfirmed) doPrint(printHeader('SK Meeting Record') + meetingHtml(m, !!result.value));
        });
    }
    /* ---------- attendance ---------- */
    function renderAttendanceSelect(selectId) {
        var sel = $('attendanceMeeting'), keep = selectId || sel.value;
        var list = meetings.slice().sort(function (a, b) { return stamp(b).localeCompare(stamp(a)); });
        sel.innerHTML = list.length
            ? list.map(function (m) { return '<option value="' + esc(m.id) + '">' + esc(m.title) + ' \u2013 ' + esc(fmtDate(m.startDate)) + '</option>'; }).join('')
            : '<option value="">No meetings available</option>';
        if (keep && list.some(function (m) { return m.id === keep; })) sel.value = keep;
    }
    function filterAttendanceRows() {
        var query = D.norm($('attendanceSearch').value);
        var statusFilter = $('attendanceStatusFilter').value;
        var rows = Array.prototype.slice.call(document.querySelectorAll('#attendanceBody tr[data-id]'));
        var visible = 0;
        rows.forEach(function (row) {
            var status = row.querySelector('.att-status').value;
            var matchesText = !query || D.norm(row.cells[0].textContent + ' ' + row.cells[1].textContent).indexOf(query) > -1;
            var matchesStatus = !statusFilter || (statusFilter === 'Unmarked' ? !status : status === statusFilter);
            var show = matchesText && matchesStatus;
            row.classList.toggle('d-none', !show);
            if (show) visible++;
        });
        $('attendanceCount').textContent = rows.length
            ? 'Showing ' + visible + ' of ' + rows.length + ' officials'
            : '';
        $('attendanceFilterEmpty').classList.toggle('d-none', !rows.length || visible > 0);
    }
    function renderAttendanceRows() {
        var mid = $('attendanceMeeting').value, m = meetings.filter(function (x) { return x.id === mid; })[0];
        var body = $('attendanceBody'), saved = attendance[mid] || {};
        $('saveAttendanceBtn').disabled = !m;
        if (!m) {
            body.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-4">Add a meeting first to record attendance.</td></tr>';
            $('attendanceCount').textContent = '';
            $('attendanceFilterEmpty').classList.add('d-none');
            return;
        }
        var ids = attendeeIds(m);
        if (!ids.length) {
            body.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-4">No active SK members yet. Assign SK roles in the <a href="members.php?type=SK">Member List</a>.</td></tr>';
            $('saveAttendanceBtn').disabled = true;
            $('attendanceCount').textContent = '';
            $('attendanceFilterEmpty').classList.add('d-none');
            return;
        }
        body.innerHTML = ids.map(function (id) {
            var s = saved[id] || {};
            return '<tr data-id="' + esc(id) + '"><td class="fw-semibold">' + esc(nameOf(id)) + '</td><td>' + esc(posOf(id)) + '</td><td>' + esc(fmtDate(m.startDate)) + '</td>' +
                '<td><select class="form-select form-select-sm att-status"><option value=""' + (s.status ? ' disabled' : ' selected') + '>Select</option>' +
                ASTATUS.map(function (x) { return '<option' + (s.status === x ? ' selected' : '') + '>' + x + '</option>'; }).join('') + '</select></td>' +
                '<td><input type="text" class="form-control form-control-sm att-remarks" maxlength="120" value="' + esc(s.remarks || '') + '" placeholder="Optional"></td></tr>';
        }).join('');
        filterAttendanceRows();
    }
    window.saveAttendance = function () {
        var mid = $('attendanceMeeting').value;
        if (!mid) return;
        var rec = {}, saved = attendance[mid] || {};
        document.querySelectorAll('#attendanceBody tr[data-id]').forEach(function (tr) {
            var id = tr.dataset.id, previous = saved[id] || {};
            var st = tr.querySelector('.att-status').value;
            var remarks = tr.querySelector('.att-remarks').value.trim();
            if (st && (st !== previous.status || remarks !== (previous.remarks || ''))) {
                rec[id] = { status: st, remarks: remarks };
            }
        });
        var changed = Object.keys(rec).length;
        if (!changed) { note('attendanceMessage', 'info', 'There are no attendance changes to save.'); return; }
        try {
            attendance[mid] = DB.saveMeetingAttendance(mid, saved, rec);
        } catch (error) {
            note('attendanceMessage', 'danger', error.message || 'Could not save attendance.');
            return;
        }
        note('attendanceMessage', 'success', 'Attendance saved for ' + changed + ' member' + (changed !== 1 ? 's' : '') + '.');
        renderSummary(); renderMeetings();
    };

    /* ---------- summary ---------- */
    function meetingAttendanceCounts(meetingId) {
        var counts = { Present: 0, Late: 0, Excused: 0, Absent: 0 };
        var records = attendance[meetingId] || {};
        Object.keys(records).forEach(function (id) {
            var status = records[id] && records[id].status;
            if (Object.prototype.hasOwnProperty.call(counts, status)) counts[status]++;
        });
        return counts;
    }
    function renderDashboardOverview(rate) {
        var today = D.todayISO();
        var upcoming = meetings.filter(function (meeting) {
            return meeting.startDate >= today && meeting.status !== 'Cancelled' && meeting.status !== 'Completed';
        }).sort(function (a, b) { return stamp(a).localeCompare(stamp(b)); })[0];
        var dateText = new Date().toLocaleDateString(undefined, { month: 'short', day: 'numeric', year: 'numeric' });
        $('dashboardDate').textContent = dateText;
        if (upcoming) {
            var start = new Date(upcoming.startDate + 'T00:00:00');
            var meetingTime = fmtTime(upcoming.startTime) +
                (upcoming.endTime ? ' – ' + fmtTime(upcoming.endTime) : '');
            $('upcomingMeetingCard').innerHTML =
                '<div class="upcoming-meeting-summary"><div class="date-box">' +
                '<div class="fw-bold" style="font-size:.7rem;">' + esc(start.toLocaleDateString(undefined, { month: 'short' }).toUpperCase()) +
                '</div><div class="fw-bold fs-5 lh-1">' + start.getDate() + '</div></div>' +
                '<div class="upcoming-meeting-copy"><strong title="' + esc(upcoming.title) + '">' + esc(upcoming.title) +
                '</strong><span><i class="bi bi-clock me-1"></i>' + esc(meetingTime) +
                '</span><span><i class="bi bi-geo-alt me-1"></i>' + esc(upcoming.location || 'Location not set') + '</span></div></div>' +
                '<div class="upcoming-meeting-actions"><span class="badge text-bg-light border">' + esc(upcoming.status) + '</span>' +
                '<span class="upcoming-meeting-invites"><i class="bi bi-people me-1"></i>' + upcoming.attendees.length + ' invitee' +
                (upcoming.attendees.length === 1 ? '' : 's') + '</span>' +
                '<button type="button" class="btn btn-outline-success btn-sm ms-auto" data-view="' + esc(upcoming.id) +
                '">View Details <i class="bi bi-arrow-right ms-1"></i></button></div>';
        } else {
            $('upcomingMeetingCard').innerHTML = '<div class="small text-muted py-2">No upcoming meetings. Schedule one from Add Meeting.</div>';
        }

        var statusCounts = { Present: 0, Late: 0, Excused: 0, Absent: 0 };
        meetings.forEach(function (meeting) {
            var counts = meetingAttendanceCounts(meeting.id);
            Object.keys(statusCounts).forEach(function (status) {
                statusCounts[status] += counts[status];
            });
        });
        var ring = $('attendanceRing');
        ring.style.setProperty('--ring-value', rate + '%');
        ring.setAttribute('aria-label', 'Attendance rate ' + rate + ' percent');
        $('attendanceRingRate').textContent = rate + '%';
        var legendRows = [
            ['Present', statusCounts.Present, ''],
            ['Late', statusCounts.Late, 'late'],
            ['Excused', statusCounts.Excused, 'excused'],
            ['Absent', statusCounts.Absent, 'absent']
        ];
        $('attendanceLegend').innerHTML = legendRows.map(function (row) {
            return '<div class="attendance-legend-item"><span class="attendance-dot ' + row[2] + '"></span>' + row[0] +
                '<strong>' + row[1] + '</strong></div>';
        }).join('');

        var trendMeetings = meetings.filter(function (meeting) {
            var counts = meetingAttendanceCounts(meeting.id);
            return counts.Present + counts.Late + counts.Excused + counts.Absent > 0;
        }).sort(function (a, b) {
            return stamp(a).localeCompare(stamp(b));
        });
        if (trendMeetings.length) {
            var chartWidth = 480, chartHeight = 186, chartLeft = 36, chartRight = 10, chartTop = 13, chartBottom = 30;
            var plotWidth = chartWidth - chartLeft - chartRight, plotHeight = chartHeight - chartTop - chartBottom;
            var points = trendMeetings.map(function (meeting, index) {
                var counts = meetingAttendanceCounts(meeting.id);
                var meetingTotal = counts.Present + counts.Late + counts.Excused + counts.Absent;
                var meetingRate = meetingTotal ? Math.round((counts.Present + counts.Late) / meetingTotal * 100) : 0;
                var date = new Date(meeting.startDate + 'T00:00:00');
                return {
                    x: trendMeetings.length === 1 ? chartLeft + plotWidth / 2 : chartLeft + index / (trendMeetings.length - 1) * plotWidth,
                    y: chartTop + (100 - meetingRate) / 100 * plotHeight,
                    rate: meetingRate,
                    hasRecords: meetingTotal > 0,
                    title: meeting.title,
                    date: date.toLocaleDateString(undefined, { month: 'short', day: 'numeric' })
                };
            });
            var dateLabelIndexes = Object.create(null);
            var dateLabelCount = Math.min(points.length, 5);
            for (var labelIndex = 0; labelIndex < dateLabelCount; labelIndex++) {
                var pointIndex = Math.round(labelIndex * (points.length - 1) / Math.max(1, dateLabelCount - 1));
                dateLabelIndexes[pointIndex] = true;
            }
            var path = points.map(function (point, index) {
                return (index ? 'L' : 'M') + point.x.toFixed(1) + ' ' + point.y.toFixed(1);
            }).join(' ');
            var lastPoint = points[points.length - 1], firstPoint = points[0];
            var areaPath = path + ' L' + lastPoint.x.toFixed(1) + ' ' + (chartHeight - chartBottom) +
                ' L' + firstPoint.x.toFixed(1) + ' ' + (chartHeight - chartBottom) + ' Z';
            var grid = [100, 75, 50, 25, 0].map(function (value) {
                var y = chartTop + (100 - value) / 100 * plotHeight;
                return '<text class="attendance-trend-label" x="0" y="' + (y + 4).toFixed(1) + '">' + value + '</text>' +
                    '<line class="attendance-trend-grid" x1="' + chartLeft + '" y1="' + y.toFixed(1) +
                    '" x2="' + (chartWidth - chartRight) + '" y2="' + y.toFixed(1) + '"></line>';
            }).join('');
            $('attendanceTrend').innerHTML =
                '<svg class="attendance-trend-chart" viewBox="0 0 ' + chartWidth + ' ' + chartHeight + '" role="img" aria-label="Attendance rate across all meetings with saved attendance">' +
                '<defs><linearGradient id="attendanceTrendFill" x1="0" y1="0" x2="0" y2="1"><stop offset="0%" stop-color="#1768ef" stop-opacity=".24"></stop><stop offset="100%" stop-color="#1768ef" stop-opacity=".02"></stop></linearGradient></defs>' +
                grid + '<path class="attendance-trend-area" d="' + areaPath + '"></path><path class="attendance-trend-line" d="' + path + '"></path>' +
                points.map(function (point, index) {
                    return '<g><title>' + esc(point.title) + ': ' + (point.hasRecords ? point.rate + '%' : 'No attendance saved') + '</title>' +
                        '<circle class="attendance-trend-point" cx="' + point.x.toFixed(1) + '" cy="' + point.y.toFixed(1) + '" r="5"></circle>' +
                        (dateLabelIndexes[index] ? '<text class="attendance-trend-label" text-anchor="middle" x="' + point.x.toFixed(1) + '" y="' +
                            (chartHeight - 8) + '">' + esc(point.date) + '</text>' : '') + '</g>';
                }).join('') + '</svg>';
        } else {
            $('attendanceTrend').innerHTML = '<div class="small text-muted d-flex h-100 align-items-center justify-content-center">Attendance trends will appear after meetings are added.</div>';
        }

        var recent = meetings.filter(function (meeting) {
            return meeting.startDate <= today && meeting.status !== 'Cancelled' && meeting.status !== 'Postponed';
        }).sort(function (a, b) { return stamp(b).localeCompare(stamp(a)); }).slice(0, 4).map(function (meeting) {
            var counts = meetingAttendanceCounts(meeting.id);
            return {
                meeting: meeting,
                present: counts.Present + counts.Late,
                expected: attendeeIds(meeting).length
            };
        });
        $('recentAttendanceList').innerHTML = recent.length ? recent.map(function (record) {
            var meeting = record.meeting;
            var date = new Date(meeting.startDate + 'T00:00:00');
            var rateForMeeting = record.expected ? Math.round(record.present / record.expected * 100) : 0;
            var attendanceText = record.expected
                ? record.present + ' / ' + record.expected + ' present · ' + rateForMeeting + '%'
                : 'No active officials';
            return '<div class="recent-attendance-row"><span class="recent-date">' +
                esc(date.toLocaleDateString(undefined, { month: 'short', day: 'numeric', year: 'numeric' })) +
                '</span><span class="recent-title" title="' + esc(meeting.title) + '">' + esc(meeting.title) + '</span>' +
                '<span class="recent-attendance-status" title="Present or late out of eligible officials">' + attendanceText + '</span></div>';
        }).join('') : '<div class="small text-muted py-2">Recent meetings will appear here after they take place.</div>';
    }
    function renderSummary() {
        var active = activeOfficers().length;
        $('statActiveSk').textContent = active;
        $('statMeetings').textContent = meetings.length;
        $('statCompletedMeetings').textContent = meetings.filter(function (meeting) {
            return meeting.status === 'Completed';
        }).length;
        var tot = 0, ok = 0;
        Object.keys(attendance).forEach(function (mid) {
            if (!meetings.some(function (m) { return m.id === mid; })) return;
            Object.keys(attendance[mid]).forEach(function (pid) {
                tot++;
                if (attendance[mid][pid].status === 'Present' || attendance[mid][pid].status === 'Late') ok++;
            });
        });
        var rate = tot ? Math.round(ok / tot * 100) : 0;
        renderDashboardOverview(rate);
    }
    function renderAll(selectMeeting) {
        renderMeetings(); renderSummary(); renderAttendanceSelect(selectMeeting); renderAttendanceRows();
    }

    /* ---------- events ---------- */
    window.openAttendanceModal = function (id) {
        renderAttendanceSelect(id);
        renderAttendanceRows();
        modal('attendanceModal').show();
    };
    window.openMeetingsModal = function () {
        renderMeetings();
        modal('meetingsModal').show();
    };
    function hideOpenMeetingDialog(fn) {
        var source = isOpen('viewModal') ? 'viewModal' : (isOpen('meetingsModal') ? 'meetingsModal' : '');
        if (source) afterHide(source, fn);
        else fn();
    }
    document.addEventListener('click', function (e) {
        var b;
        if ((b = e.target.closest('[data-view]'))) { var viewId = b.dataset.view; hideOpenMeetingDialog(function () { openView(viewId); }); }
        else if ((b = e.target.closest('[data-edit]'))) { var id = b.dataset.edit; hideOpenMeetingDialog(function () { openMeetingForm(id); }); }
        else if ((b = e.target.closest('[data-del]'))) deleteMeeting(b.dataset.del);
        else if ((b = e.target.closest('[data-print]'))) printMeeting(b.dataset.print);
    });
    $('attendanceMeeting').addEventListener('change', renderAttendanceRows);
    $('attendanceSearch').addEventListener('input', filterAttendanceRows);
    $('attendanceStatusFilter').addEventListener('change', filterAttendanceRows);
    $('attendanceBody').addEventListener('change', function (event) {
        if (event.target.closest('.att-status')) filterAttendanceRows();
    });
    $('meetSearch').addEventListener('input', renderMeetings);
    $('meetTypeFilter').addEventListener('change', renderMeetings);
    $('meetStatusFilter').addEventListener('change', renderMeetings);
    opts($('meetTypeFilter'), TYPES, 'All Types');
    opts($('meetStatusFilter'), MSTATUS, 'All Status');
    $('attendanceStatusFilter').innerHTML = '<option value="">All statuses</option><option value="Unmarked">Not marked</option>' +
        ASTATUS.map(function (status) { return '<option value="' + esc(status) + '">' + esc(status) + '</option>'; }).join('');

    function refresh() { if (isOpen('meetingModal')) return; load(); renderAll(); }
    window.addEventListener('storage', function (e) { if (e.key === D.KEYS.KK || e.key === D.KEYS.ROLE || e.key === MEET_KEY || e.key === ATT_KEY || e.key === null) refresh(); });
    document.addEventListener('visibilitychange', function () { if (!document.hidden) refresh(); });
    window.addEventListener('pageshow', function (e) { if (e.persisted) refresh(); });

    if (!window.SK_CAN_MANAGE_ROLES) {
        $('saveAttendanceBtn').classList.add('d-none');
        $('attendanceMessage').innerHTML = '<div class="alert alert-info small">Only the SK Chairman or SK Secretary can record or correct attendance.</div>';
    }
    load();
    if (meetings.length) saveMeetings();     // store upgraded meeting fields once
    renderAll();
})();
</script>

</body>
</html>