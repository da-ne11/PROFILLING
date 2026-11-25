<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/role_store.php';
require_viewer();
$memberProfileUser = current_user();
$accountRoleSeats = ['SK Chairperson' => 0, 'SK Secretary' => 0];
if (can_manage_sk_roles()) {
    $accountRoleSeats = sk_role_account_seat_counts(db());
}
?>
<!DOCTYPE html>
<html lang="en"<?= is_public() ? ' class="is-public"' : '' ?>>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Member List | SK KK Profiling System</title>
 
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" integrity="sha384-XGjxtQfXaH2tnPFa9x+ruJTuLE3Aa6LhHSWRr1XeTyhezb4abCG4ccI5AkVDxqC+" crossorigin="anonymous">
    <link href="sidebar.css?v=32" rel="stylesheet">
    <link href="kk-id.css?v=3" rel="stylesheet">
 
<style>
 
:root{
    --kk-primary:var(--bs-primary);
    --kk-primary-dark:var(--bs-primary-text-emphasis);
    --kk-primary-soft:rgba(var(--bs-primary-rgb),.12);
    --black:var(--bs-body-color);
    --dark:var(--bs-primary-text-emphasis);
    --cream:var(--bs-tertiary-bg);
    --line:var(--bs-border-color);
    --muted:var(--bs-secondary-color);
    --white:#ffffff;
}
 
*{ box-sizing:border-box; }
 
body{
    margin:0;
    background:var(--bs-tertiary-bg);
    color:var(--black);
    font-family:Inter,system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;
}
 
a{ text-decoration:none; }
 
.top-strip{ height:7px; background:var(--bs-primary); }
 
header{
    background:#fff!important;
    border-bottom:1px solid var(--line)!important;
}
 
header h1{ font-weight:900; }
 
.btn-success{
    --bs-btn-bg:var(--bs-body-color);
    --bs-btn-border-color:var(--bs-body-color);
    --bs-btn-hover-bg:var(--kk-primary);
    --bs-btn-hover-border-color:var(--kk-primary);
    --bs-btn-hover-color:#fff;
    --bs-btn-active-bg:var(--bs-body-color);
    --bs-btn-active-border-color:var(--bs-body-color);
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
 
.member-hero{
    position:relative;
    overflow:hidden;
    background:var(--bs-primary);
    border-radius:26px;
    padding:28px;
    box-shadow:0 12px 30px rgba(20,20,20,.08);
}
.member-hero:after{
    content:"";
    position:absolute;
    width:220px;
    height:220px;
    border-radius:50%;
    right:-70px;
    top:-95px;
    background:var(--bs-primary-text-emphasis);
    opacity:.08;
}
.member-hero-content{ position:relative; z-index:2; }
.member-icon{
    width:58px;
    height:58px;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    border-radius:17px;
    background:var(--bs-primary-text-emphasis);
    color:var(--kk-primary);
    font-size:1.6rem;
}
.member-hero h2{ font-weight:950; letter-spacing:-.7px; }
.member-hero p{ color:var(--bs-body-color); max-width:680px; }
 
.stat-card{
    height:100%;
    border:1px solid var(--line);
    border-radius:20px;
    background:#fff;
    box-shadow:0 8px 20px rgba(20,20,20,.05);
    transition:.2s ease;
}
.stat-card:hover{ transform:translateY(-4px); box-shadow:0 14px 28px rgba(20,20,20,.09); }
.stat-icon{
    width:45px;
    height:45px;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    border-radius:14px;
    background:var(--bs-primary);
    color:var(--bs-body-color);
    font-size:1.2rem;
}
.stat-link{ cursor:pointer; }
.stat-link:focus-visible{ outline:3px solid var(--bs-primary-border-subtle); outline-offset:2px; }
.stat-link.selected{ border-color:var(--bs-primary); box-shadow:0 0 0 2px var(--bs-primary), 0 14px 28px rgba(20,20,20,.09); }
.stat-number{ font-size:1.8rem; font-weight:950; letter-spacing:-1px; }
 
/* overflow is visible so the Print / Manage dropdowns are not clipped */
.member-card{
    border:1px solid var(--line)!important;
    border-radius:22px!important;
    box-shadow:0 8px 22px rgba(20,20,20,.05)!important;
    background:#fff;
}
.member-card .card-header{
    background:#fff!important;
    border-bottom:1px solid var(--line);
    border-radius:22px 22px 0 0;
}
.member-card .card-footer{ border-radius:0 0 22px 22px; }
 
.search-box{ position:relative; }
.search-box i{
    position:absolute;
    left:14px;
    top:50%;
    transform:translateY(-50%);
    color:var(--bs-secondary-color);
}
.search-box input{ padding-left:42px; }
 
.form-control,
.form-select{
    border:1px solid var(--bs-border-color);
    border-radius:12px;
    min-height:43px;
    box-shadow:none!important;
}
textarea.form-control{ min-height:0; }
.form-control:focus,
.form-select:focus{
    border-color:var(--bs-body-color);
    box-shadow:0 0 0 3px rgba(var(--bs-primary-rgb),.28)!important;
}
 
.table{ --bs-table-hover-bg:rgba(var(--bs-primary-rgb),.08); }
.table thead th{
    background:rgba(var(--bs-primary-rgb),.08);
    color:var(--bs-secondary-color);
    font-size:.73rem;
    text-transform:uppercase;
    letter-spacing:.45px;
    white-space:nowrap;
}
.table td{ font-size:.86rem; }
 
.member-avatar{
    width:39px;
    height:39px;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    border-radius:12px;
    background:var(--bs-primary);
    color:var(--bs-body-color);
    font-weight:900;
    flex:none;
}
.thumb{ flex:none; object-fit:cover; border-radius:50%; background:var(--bs-primary); display:inline-flex; align-items:center; justify-content:center; font-weight:900; color:#fff; }
.member-name{ font-weight:800; }
.member-id{ font-size:.72rem; color:var(--bs-secondary-color); }
 
.status-active{ background:var(--bs-primary); color:var(--bs-body-color); }
.status-pending,
.status-inactive{ background:var(--bs-secondary-bg-subtle); color:var(--bs-secondary-text-emphasis); }
.status-archived{ background:var(--bs-secondary); color:#fff; }
 
.action-btn{
    width:34px;
    height:34px;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    border-radius:10px;
    padding:0;
}
 
.pagination .page-link{ color:var(--bs-body-color); border-color:var(--line); }
.pagination .page-item.active .page-link{
    background:var(--bs-primary-text-emphasis);
    border-color:var(--bs-body-color);
    color:#fff;
}
 
.empty-state{ padding:50px 20px; text-align:center; color:var(--bs-secondary-color); }
.empty-state i{ font-size:3rem; color:var(--bs-border-color); }
 
/* ---- profile cards ---- */
.person-card{
    border:1px solid var(--line);
    border-radius:20px;
    background:#fff;
    transition:.2s ease;
}
.person-card:hover{ box-shadow:0 12px 26px rgba(20,20,20,.09); transform:translateY(-3px); }
.person-card .desc{
    display:-webkit-box;
    -webkit-line-clamp:3;
    -webkit-box-orient:vertical;
    overflow:hidden;
    color:var(--bs-secondary-color);
    font-size:.84rem;
    min-height:3.9em;
}
.person-card .meta{ font-size:.82rem; color:var(--bs-secondary-color); }
.person-card .meta i{ width:1.3em; display:inline-block; }
.person-card.is-archived{ opacity:.78; }
.type-pill{
    font-size:.68rem;
    font-weight:800;
    letter-spacing:.4px;
    padding:.3em .65em;
    border-radius:999px;
    background:var(--bs-secondary-bg-subtle);
    color:var(--bs-secondary-text-emphasis);
}
 
/* ---- profile modal ---- */
.kv-l{ font-size:.74rem; color:var(--bs-secondary-color); }
.kv-v{ font-size:.9rem; font-weight:700; word-break:break-word; }
.review-modal .modal-content{border:0;border-radius:16px;overflow:hidden}
.review-modal .modal-header{background:#1855a0!important;color:#fff}
.review-modal .modal-header .btn-close{filter:invert(1);opacity:.85}
.member-review-card{padding:18px;border:1px solid #d2e2fb;border-radius:16px;background:#eff6ff;color:#243653}
.member-review-head{display:flex;align-items:center;gap:14px;padding-bottom:14px;border-bottom:1px solid #d5e3f8}
.member-review-head .thumb{width:62px;height:62px;flex:none;border:3px solid #fff;box-shadow:0 1px 5px #174f9d33}
.member-review-name{color:#202a39;font-weight:800}
.member-review-type{display:inline-block;margin-top:4px;padding:2px 9px;border-radius:999px;background:#1765c0;color:#fff;font-size:.72rem;font-weight:750}
.member-review-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px 22px;padding-top:14px}
.member-review-label{margin-bottom:2px;color:#657994;font-size:.68rem;font-weight:800;letter-spacing:.025em;text-transform:uppercase}
.member-review-value{overflow-wrap:anywhere;color:#243653;font-size:.9rem;font-weight:600}
.group-title{
    font-weight:800;
    font-size:.82rem;
    padding-bottom:6px;
    margin:20px 0 10px;
    border-bottom:2px solid var(--bs-primary);
}
.modal-content{ border-radius:22px; }
#profileFooter.kk-footer{
    display:flex;
    align-items:center;
    justify-content:flex-start;
    flex-wrap:wrap;
    gap:.75rem;
}
#profileFooter .kk-group{
    display:flex;
    align-items:center;
    flex-wrap:wrap;
    gap:.5rem;
}
#profileFooter .btn{
    border-radius:.65rem;
    font-weight:650;
    padding:.55rem .85rem;
}
#profileFooter .dropdown-menu{
    min-width:13rem;
    padding:.4rem;
    border-radius:.75rem;
    box-shadow:0 .5rem 1.25rem rgba(0,0,0,.14);
}
#profileFooter .dropdown-item{
    border-radius:.45rem;
    padding:.5rem .65rem;
}
#profileFooter .kk-close-btn{ margin-left:auto; }
.btn-close-white-fix{ filter:invert(1); }
#toastBox{ position:fixed; top:16px; right:16px; z-index:2000; width:min(380px,calc(100vw - 32px)); }
 
/* ---- printing ---- */
#printArea{ display:none; }
@media print{
    body > *:not(#printArea){ display:none!important; }
    #printArea{ display:block!important; padding:10px; color:#000; font-size:12px; }
    #printArea .group-title{ border-bottom:2px solid #000; }
    #printArea table{ width:100%; border-collapse:collapse; }
    #printArea th,#printArea td{ border:1px solid #999; padding:4px 6px; text-align:left; vertical-align:top; }
    #printArea .thumb{ border:1px solid #999; }
}
 
@media(max-width:575px){
    .member-hero{ padding:22px; }
    .member-hero h2{ font-size:1.55rem; }
    .stat-number{ font-size:1.5rem; }
    .member-review-grid{ grid-template-columns:1fr; gap:10px; }
}
 
/* sidebar.css makes modal titles white, so the header needs the primary colour */
.modal-header{ background:var(--bs-primary); border-bottom:0; border-radius:22px 22px 0 0; }
.modal-body .search-box i{ z-index:3; }
#pickMember{ border-radius:12px; }
#profileModal .modal-content{ border-radius:20px; overflow:hidden; }
 
/* ---- KK / SK ID card (card view) ---- */
.member-id-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(340px,1fr));gap:1.1rem}
.mini-card{display:flex;flex-direction:column;width:100%;min-height:215px;margin:0;border:1px solid #cfd8e6;border-radius:16px;background:#fff;overflow:hidden;box-shadow:0 6px 18px rgba(27,48,84,.08);transition:.2s ease}
.mini-card:hover{box-shadow:0 14px 30px rgba(20,56,120,.16);transform:translateY(-3px)}
.mini-card[data-act="view"],
.member-row[data-act="view"]{ cursor:pointer; }
.mini-card[data-act="view"]:focus-visible,
.member-row[data-act="view"]:focus-visible{ outline:3px solid var(--bs-primary); outline-offset:2px; }
.mini-card.is-archived{ opacity:.75; }
.mini-body{flex:1;display:grid;grid-template-columns:104px minmax(0,1fr);gap:1rem;align-items:center;padding:.9rem 1rem .6rem}
.mini-avatar{display:flex;align-items:center;justify-content:center}
.member-card .mini-avatar .thumb{display:flex!important;width:96px!important;height:96px!important;aspect-ratio:1;border-radius:50%!important;border:3px solid #fff;box-shadow:0 0 0 2px #1768ef;object-fit:cover;object-position:center;background:#eaf2ff;color:#1554b4;font-size:1.6rem!important}
.mini-main{min-width:0}
.mini-name{overflow:hidden;color:#14264c;font-size:1rem;font-weight:850;line-height:1.2;text-overflow:ellipsis;white-space:nowrap}
.mini-role{margin-top:.1rem;overflow:hidden;color:#1768ef;font-size:.74rem;font-weight:700;text-overflow:ellipsis;white-space:nowrap}
.mini-status{display:inline-flex;align-items:center;gap:.25rem;margin-top:.35rem;padding:.15rem .5rem;border:1px solid transparent;border-radius:999px;font-size:.66rem;font-weight:750}
.mini-status:before{width:6px;height:6px;border-radius:50%;background:currentColor;content:""}
.mini-status.is-active{border-color:#bce8cb;background:#e8f8ed;color:#16803c}
.mini-status.is-inactive{border-color:#dbe2ed;background:#f1f4f8;color:#66748a}
.mini-tags{display:flex;flex-wrap:wrap;gap:.3rem;margin-top:.45rem}
.mini-tag{padding:.15rem .5rem;border-radius:999px;background:#eaf2ff;color:#1554b4;font-size:.68rem;font-weight:700;white-space:nowrap}
.mini-info-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:.3rem .8rem;padding:.55rem 1rem;border-top:1px dashed #d5deec}
.mini-info-item{display:flex;align-items:center;gap:.35rem;min-width:0}
.mini-info-item>i{display:grid;width:20px;height:20px;flex:none;place-items:center;border-radius:50%;background:#e5edfc;color:#1761d9;font-size:.68rem}
.mini-info-item>span{min-width:0;overflow:hidden;color:#273a60;font-size:.74rem;text-overflow:ellipsis;white-space:nowrap}
.mini-footer{display:flex;align-items:center;justify-content:space-between;gap:.5rem;padding:.5rem 1rem;background:#f1f5fc;border-top:1px solid #e0e7f3}
.mini-idno{color:#14264c;font-size:.76rem;font-weight:800;letter-spacing:.06em}
.mini-view{display:inline-flex;align-items:center;gap:.3rem;padding:.3rem .65rem;border:1px solid #1761d9;border-radius:7px;background:#0d59d5;color:#fff;font-size:.72rem;font-weight:800;white-space:nowrap}
.mini-card:hover .mini-view{background:#0b4fbe}
@media(max-width:575.98px){
    .member-id-grid{grid-template-columns:minmax(0,1fr)}
    .mini-body{grid-template-columns:84px minmax(0,1fr)}
    .member-card .mini-avatar .thumb{width:78px!important;height:78px!important}
}
 
/* ---- assign SK role: scrollable form + position choices + always-visible Save ---- */
#roleModal .modal-dialog{ max-width:860px; }
#roleModal .modal-content{
    overflow:hidden;
    border:1px solid var(--bs-border-color);
    border-radius:20px;
    box-shadow:0 18px 48px rgba(24,44,74,.18);
}
#roleModal .modal-header{
    padding:1rem 1.25rem;
    background:var(--bs-primary-text-emphasis);
}
#roleModal .modal-header .modal-title{
    display:flex;
    align-items:center;
    gap:.65rem;
}
#roleModal .modal-header .modal-title:before{
    content:"";
    width:34px;
    height:34px;
    flex:none;
    border-radius:10px;
    background:rgba(255,255,255,.16);
    background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='white'%3E%3Cpath d='M8 1a3 3 0 0 0-3 3v1H4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V4a3 3 0 0 0-3-3m-1 4V4a1 1 0 1 1 2 0v1zM4 7h8v5H4z'/%3E%3C/svg%3E");
    background-repeat:no-repeat;
    background-position:center;
    background-size:17px;
}
#roleModal .modal-body{ overflow-y:auto; max-height:calc(100vh - 190px); }
#roleModal .modal-body{ padding:1.25rem; background:var(--bs-tertiary-bg); }
#roleModal #pickBox,
#roleModal #memberSummary{
    padding:1rem;
    border:1px solid var(--bs-border-color);
    border-radius:14px;
    background:var(--bs-body-bg);
}
#roleModal #memberSummary:not(.d-none){ margin-top:1rem; }
#roleModal .group-title{
    display:flex;
    align-items:center;
    gap:.6rem;
    margin:1.35rem 0 .75rem;
    padding:.65rem .8rem;
    border:0;
    border-left:3px solid var(--bs-primary);
    border-radius:7px;
    background:var(--bs-primary-bg-subtle);
    color:var(--bs-primary-text-emphasis);
    font-size:.8rem;
    letter-spacing:.025em;
    text-transform:uppercase;
}
#roleModal .group-title:before{
    content:"";
    width:7px;
    height:7px;
    border-radius:50%;
    background:var(--bs-primary);
}
#roleModal .form-label{ color:var(--bs-body-color); }
#roleModal .form-control,
#roleModal .form-select{
    min-height:44px;
    border-radius:10px;
    border-color:var(--bs-border-color);
    background-color:var(--bs-body-bg);
    color:var(--bs-body-color);
}
#roleModal textarea.form-control{ min-height:0; }
#roleModal .form-control:focus,
#roleModal .form-select:focus{
    border-color:var(--bs-primary);
    box-shadow:0 0 0 3px rgba(var(--bs-primary-rgb),.16);
}
#pickMember{ max-height:190px; overflow-y:auto; }
.pos-choices{ display:grid; grid-template-columns:repeat(auto-fill,minmax(150px,1fr)); gap:8px; }
.committee-options{ display:grid; grid-template-columns:repeat(auto-fit,minmax(min(100%,260px),1fr)); gap:.5rem; }
.committee-option{ display:flex; align-items:flex-start; gap:.55rem; min-height:42px; padding:.65rem .75rem; border:1px solid var(--bs-border-color); border-radius:10px; background:var(--bs-body-bg); color:var(--bs-body-color); cursor:pointer; }
.committee-option input{ margin-top:.22rem; flex:none; }
.committee-badges{ display:flex; flex-wrap:wrap; gap:.3rem; }
.committee-badge{ display:inline-block; padding:.2rem .5rem; border-radius:999px; background:var(--bs-primary-bg-subtle); color:var(--bs-primary-text-emphasis); font-size:.72rem; line-height:1.35; }
.pos-choice{ min-height:70px; text-align:left; border:1px solid var(--bs-border-color); border-radius:12px; background:var(--bs-body-bg); padding:10px 12px; cursor:pointer; transition:.15s ease; color:var(--bs-body-color); }
.pos-choice:hover:not(:disabled){ border-color:var(--bs-primary); background:var(--bs-primary-bg-subtle); transform:translateY(-1px); }
.pos-choice.active{ background:var(--bs-primary); border-color:var(--bs-primary); color:#fff; box-shadow:0 4px 10px rgba(var(--bs-primary-rgb),.2); }
.pos-choice:disabled{ opacity:.5; cursor:not-allowed; }
.pos-choice b{ display:block; font-size:.88rem; }
.pos-choice small{ display:block; margin-top:4px; font-size:.72rem; opacity:.78; }
#roleModal .modal-footer{
    position:sticky;
    bottom:0;
    padding:.85rem 1.25rem;
    background:var(--bs-body-bg);
    border-top:1px solid var(--bs-border-color);
}
#roleModal .modal-footer .btn{ min-width:105px; border-radius:9px; font-weight:650; }
#roleModal #roleSaveBtn{ box-shadow:0 3px 8px rgba(var(--bs-primary-rgb),.2); }
html[data-bs-theme="dark"] #roleModal .modal-body{ background:var(--bs-tertiary-bg); }
html[data-bs-theme="dark"] #roleModal #pickBox,
html[data-bs-theme="dark"] #roleModal #memberSummary,
html[data-bs-theme="dark"] #roleModal .pos-choice:not(.active){ background:var(--bs-body-bg); }
html[data-bs-theme="dark"] #roleModal .group-title{ color:var(--bs-primary-text-emphasis); }
@media(max-width:575.98px){
    #roleModal .modal-body{ padding:1rem; }
    #roleModal .modal-header,#roleModal .modal-footer{ padding-inline:1rem; }
    .pos-choices{ grid-template-columns:repeat(2,minmax(0,1fr)); }
    .pos-choice{ min-height:66px; padding:9px; }
}
 
.members-add-btn{border-radius:10px;font-weight:750}
.page-content>main.container-fluid{width:100%;max-width:none;margin:0;padding:1.15rem clamp(.85rem,2vw,1.75rem)!important}
body:not(.dashboard-page) .page-content>main.container-fluid{width:100%;max-width:none;margin:0;padding:1.15rem clamp(.85rem,2vw,1.75rem)!important}
.page-content>main>.member-hero,
.page-content>main>section.row.row-cols-1{display:none!important}
.member-card{overflow:visible;border:1px solid #e4e9f2!important;border-radius:16px!important;background:#fff!important;box-shadow:0 6px 20px rgba(27,48,84,.06)!important}
.member-directory-header{padding:1rem 1.1rem!important;background:#fff!important;border-bottom:1px solid #e9edf4!important;border-radius:16px 16px 0 0!important}
.member-directory-top{display:flex;align-items:flex-end;justify-content:space-between;gap:.85rem 1.25rem;flex-wrap:wrap}
.member-directory-title h2{color:#1b2c4d}
.member-category-tabs{display:flex;align-items:center;gap:.2rem;max-width:100%;overflow-x:auto}
.member-category-tab{display:inline-flex;align-items:center;gap:.45rem;white-space:nowrap;padding:.55rem .7rem;border:0;border-bottom:2px solid transparent;border-radius:8px 8px 0 0;background:transparent;color:#66748c;font-size:.81rem;font-weight:650}
.member-category-tab:hover{color:#185ed1;background:#f6f8fc}
.member-category-tab.active{border-bottom-color:#1768ef;color:#145ce1}
.member-category-count{display:inline-grid;min-width:1.45rem;height:1.35rem;place-items:center;padding:0 .35rem;border-radius:999px;background:#eef2f8;color:#52617a;font-size:.7rem;font-weight:750}
.member-category-tab.active .member-category-count{background:#e8f0ff;color:#145ce1}
.member-toolbar{display:flex;align-items:center;gap:.5rem;margin-top:.85rem}
.member-search{flex:1 1 260px;min-width:190px;max-width:440px}
.member-search .form-control{min-height:38px;height:38px;padding-left:2.15rem;border-color:#dfe5ef;border-radius:9px;font-size:.82rem}
.member-search i{left:.75rem;font-size:.82rem}
.member-toolbar>.dropdown{flex:none}
.member-toolbar>.dropdown>.btn,.member-view-toggle .btn{min-height:38px;border-radius:9px;font-size:.78rem}
.member-toolbar .btn-outline-success{--bs-btn-color:#1761d9;--bs-btn-border-color:#d3dff1;--bs-btn-hover-bg:#eaf1ff;--bs-btn-hover-border-color:#bfd1ef;--bs-btn-hover-color:#124eb2;--bs-btn-active-bg:#eaf1ff;--bs-btn-active-border-color:#bfd1ef;--bs-btn-active-color:#124eb2}
.member-view-toggle .btn{width:38px}
.member-view-toggle .btn.active{background:#eaf1ff;border-color:#cddcf8;color:#145ce1}
.member-filter-count{display:inline-grid;min-width:1.25rem;height:1.25rem;place-items:center;margin-left:.2rem;padding:0 .3rem;border-radius:999px;background:#e8f0ff;color:#145ce1;font-size:.68rem;font-weight:750}
.member-filter-menu{width:min(360px,calc(100vw - 24px));max-height:min(70vh,520px);overflow-y:auto;border:1px solid #e3e8f0;border-radius:12px;box-shadow:0 12px 30px rgba(26,45,76,.14)}
#filterPanel.dropdown-menu{display:none!important}
#filterPanel.dropdown-menu.show{display:block!important}
#filterPanel .form-select{min-height:36px;border-radius:8px;font-size:.78rem}
#filterPanel .form-label{color:#5f6e86}
.member-toolbar .dropdown-menu{border-color:#e3e8f0;border-radius:11px;box-shadow:0 10px 24px rgba(26,45,76,.12)}
.member-toolbar .dropdown-item{border-radius:7px;font-size:.82rem}
.member-card>.card-body{padding:.35rem 1rem 0!important}
.member-card .table-responsive{overflow-x:auto}
.member-card .table{width:100%;min-width:1080px;table-layout:auto;margin-bottom:0}
.member-card .table thead th{padding:.65rem .7rem;border-bottom:1px solid #e7ebf2;background:#f5f7fb;color:#65728a;font-size:.67rem;font-weight:750;letter-spacing:.035em;text-transform:uppercase;white-space:nowrap}
.member-card .table tbody td{padding:.58rem .7rem;border-bottom:1px solid #edf0f5;color:#38465d;font-size:.76rem;vertical-align:middle}
.member-card .table tbody tr:last-child td{border-bottom:0}
.member-card .table tbody tr.member-row{cursor:pointer}
.member-card .table tbody tr.member-row:hover{background:#f7f9fd}
.member-card .table tbody tr.member-row:focus-visible{outline:2px solid #1768ef;outline-offset:-2px}
.member-card .table td:first-child{min-width:195px!important}
.member-card .thumb{width:32px!important;height:32px!important;border-radius:50%;font-size:.75rem!important}
.member-card .member-name{color:#1e2d49;font-size:.78rem;font-weight:750}
.member-card .member-id{font-size:.66rem;line-height:1.25}
.member-card .member-address{max-width:205px;color:#526078;font-size:.73rem}
.member-card .badge.status-active,
.member-card .badge.status-inactive,
.member-card .badge.status-archived{padding:.28rem .52rem;border:1px solid transparent;border-radius:999px;font-size:.66rem;font-weight:700;white-space:nowrap}
.member-card .badge.status-active{border-color:#ccebd9;background:#eaf7ef;color:#157347}
.member-card .badge.status-inactive{border-color:#f1dfb2;background:#fff7e4;color:#8a6500}
.member-card .badge.status-archived{border-color:#dfe4ec;background:#f0f2f6;color:#677386}
.member-card .card-footer{padding:.7rem 1rem!important;background:#fff;border-top:1px solid #e9edf4!important}
.member-card .pagination .page-link{min-width:32px;border-radius:7px;font-size:.75rem}
.member-card .pagination .page-item.active .page-link{background:#1768ef;border-color:#1768ef;color:#fff}
 
@media(max-width:991.98px){
    .member-directory-top{align-items:flex-start}
    .member-toolbar{flex-wrap:wrap}
    .member-search{flex:1 1 100%;max-width:none;order:-1}
}
@media(max-width:575.98px){
    .page-content>main.container-fluid{padding-inline:.7rem!important}
    .member-directory-header{padding:.85rem!important}
    .member-category-tabs{width:100%;justify-content:space-between}
    .member-category-tab{gap:.3rem;padding:.5rem .4rem;font-size:.72rem}
    .member-toolbar{gap:.4rem}
    .member-toolbar>.dropdown>.btn{padding:.4rem .55rem;font-size:.73rem}
    .member-view-toggle .btn{width:34px;min-height:36px;padding:.3rem}
    .member-card>.card-body{padding-inline:.65rem!important}
    .member-card .card-footer{padding-inline:.75rem!important}
    .members-own-profile{padding:.45rem .6rem}
    .members-own-profile-label{display:none}
    .members-add-btn{padding:.45rem .65rem;font-size:.78rem}
    .members-add-btn~.header-notification{display:none}
}
html.dark-mode .member-card,
html.dark-mode .member-directory-header,
html.dark-mode .member-card .card-footer{background:#222d40!important;border-color:#35435d!important}
html.dark-mode .member-directory-title h2,
html.dark-mode .member-card .member-name{color:#e4ebf8}
html.dark-mode .member-category-tab{color:#a7b5cf}
html.dark-mode .member-category-tab.active{color:#8fb4ff}
html.dark-mode .member-category-count{background:#35435d;color:#d4def0}
html.dark-mode .member-category-tab.active .member-category-count{background:#293b58;color:#a9c5ff}
html.dark-mode .member-card .table thead th{background:#29364b;color:#b5c1d5;border-color:#35435d}
html.dark-mode .member-card .table tbody td{color:#d7deeb;border-color:#35435d}
html.dark-mode .member-card .table tbody tr.member-row:hover{background:#29364b}
html.dark-mode .member-filter-menu,
html.dark-mode .member-toolbar .dropdown-menu{background:#222d40;border-color:#35435d}
html.dark-mode .member-filter-menu .form-label,
html.dark-mode .member-toolbar .dropdown-item{color:#d7deeb}
html.dark-mode .member-card .badge.status-active{background:#183c30;color:#92e0b2;border-color:#285c45}
html.dark-mode .member-card .badge.status-inactive{background:#443a22;color:#f2d48a;border-color:#61512a}
html.dark-mode .member-card .badge.status-archived{background:#364152;color:#c5cfdd;border-color:#4b586a}
html.dark-mode .mini-card{background:#222d40;border-color:#35435d}
html.dark-mode .mini-main{background:#29364b}
html.dark-mode .mini-footer{background:#29364b;border-color:#35435d}
html.dark-mode .mini-idno{color:#e4ebf8}
html.dark-mode .mini-name{color:#e4ebf8}
html.dark-mode .mini-role,
html.dark-mode .mini-desc{color:#a7b5cf}
html.dark-mode .mini-avatar .thumb{border-color:#35435d;background:#293b58;color:#a9c5ff}
html.dark-mode .mini-status.is-active{background:#183c30;color:#92e0b2;border-color:#285c45}
html.dark-mode .mini-status.is-inactive{background:#364152;color:#c5cfdd;border-color:#4b586a}
html.dark-mode .mini-tag{background:#29364b;border-color:#35435d;color:#d7deeb}
html.dark-mode .mini-info-grid,
html.dark-mode .mini-desc{background:#29364b}
html.dark-mode .mini-info-item>i{background:#293b58;color:#a9c5ff}
html.dark-mode .mini-info-item>span{color:#d7deeb}
html.dark-mode .mini-committee{background:#293b58;color:#a9c5ff}
html.dark-mode .mini-view{background:#0d59d5;border-color:#1761d9;color:#fff}
html.dark-mode .mini-card:hover .mini-view{background:#0b4fbe;border-color:#0d54d0;color:#fff}
</style>
 
<?php include __DIR__ . '/init.php'; ?>
<script>
window.SK_ACCOUNT_ROLE_SEATS = <?= json_encode(
    $accountRoleSeats,
    JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE
) ?>;
window.SK_CAN_SCAN_KK_ID = <?= can_write_app_data() ? 'true' : 'false' ?>;
window.SK_CURRENT_MEMBER_ID = <?= json_encode(
    (string)($memberProfileUser['legacy_id'] ?? ''),
    JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
) ?>;
</script>
</head>
 
<body>
 
<div class="top-strip"></div>
 
<script src="theme.js"></script>
<script src="sidebar.js?v=16"></script>
 
<div class="page-content">
 
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
            <h1 class="h5 fw-bold mb-0">Member List</h1>
            <div class="text-muted small d-none d-sm-block">
                All KK youth members, including SK officials
            </div>
        </div>
 
        <div class="ms-auto header-account-area">
            <a href="registration.php" class="btn btn-primary px-4 members-add-btn">
                <i class="bi bi-plus-lg me-1"></i>Add member
            </a>
 
            <?php if (!empty($memberProfileUser['legacy_id'])): ?>
                <button type="button" class="btn btn-outline-primary members-own-profile" id="membersOwnProfile" data-member-id="<?= e((string)$memberProfileUser['legacy_id']) ?>" aria-label="View my member profile" title="View my member profile">
                    <i class="bi bi-person-vcard me-md-1"></i><span class="members-own-profile-label">My profile</span>
                </button>
            <?php endif; ?>
 
            <a href="announcements.php"
               class="header-notification"
               aria-label="Notifications">
                <i class="bi bi-bell"></i>
                <span class="header-notification-count">3</span>
            </a>
 
            <span class="header-avatar">
                <?= e(uinit()) ?>
            </span>
 
            <div class="d-none d-md-block header-identity">
                <div class="header-identity-name"><?= e(uname()) ?></div>
                <div class="header-identity-role"><?= e(strtoupper(urole())) ?></div>
            </div>
 
            <a href="<?= is_public() ? 'login.php' : 'logout.php' ?>" class="btn btn-sm header-logout">
                <i class="bi bi-box-arrow-right me-1"></i>
                <?= is_public() ? 'Official Login' : 'Logout' ?>
            </a>
 
        </div>
 
    </div>
 
</header>
 
<main class="container-fluid p-3 p-lg-4">
 
    <!-- ================= HERO + QUICK ACTIONS ================= -->
    <section class="member-hero mb-4">
        <div class="member-hero-content">
 
            <div class="member-icon mb-3">
                <i class="bi bi-people-fill"></i>
            </div>
 
            <h2 class="mb-2">Member List</h2>
 
            <p class="mb-3">
                One central list of all registered KK youth of Barangay Sikat.
                SK officials are KK members too &ndash; select a member and assign an SK position.
            </p>
 
            <div class="d-flex flex-wrap gap-2">
                <button type="button" class="btn btn-dark rounded-pill px-4" id="heroViewSk">
                    <i class="bi bi-person-vcard me-2"></i>View SK Members
                </button>
                <button type="button" class="btn btn-dark rounded-pill px-4" id="heroViewKk">
                    <i class="bi bi-people me-2"></i>View Regular KK Members
                </button>
                <button type="button" class="btn btn-dark rounded-pill px-4 admin-only role-manager-only" id="heroAssign">
                    <i class="bi bi-person-badge me-2"></i>Assign SK Member
                </button>
                <a href="registration.php" class="btn btn-dark rounded-pill px-4">
                    <i class="bi bi-person-plus-fill me-2"></i>Register KK Youth
                </a>
            </div>
 
        </div>
    </section>
 
    <div id="unlinkedBox"></div>
 
    <!-- ================= SUMMARY CARDS ================= -->
    <section class="row row-cols-1 row-cols-sm-2 row-cols-xl-4 g-3 mb-4">
 
        <div class="col">
            <div class="card stat-card stat-link" data-stat="ALL" role="button" tabindex="0" title="Click to show these members"><div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <span class="fw-semibold text-muted small">All KK Members</span>
                    <span class="stat-icon"><i class="bi bi-people-fill"></i></span>
                </div>
                <h3 class="stat-number mb-1" id="statTotal">0</h3>
                <p class="small text-muted mb-0" id="statTotalSub">Registered KK youth</p>
            </div></div>
        </div>
 
        <div class="col">
            <div class="card stat-card stat-link" data-stat="SK" role="button" tabindex="0" title="Click to show these members"><div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <span class="fw-semibold text-muted small">SK Members</span>
                    <span class="stat-icon"><i class="bi bi-person-vcard-fill"></i></span>
                </div>
                <h3 class="stat-number mb-1" id="statSk">0</h3>
                <p class="small text-muted mb-0" id="statSkSub">KK members with an SK position</p>
            </div></div>
        </div>
 
        <div class="col">
            <div class="card stat-card stat-link" data-stat="KK" role="button" tabindex="0" title="Click to show these members"><div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <span class="fw-semibold text-muted small">Regular KK Members</span>
                    <span class="stat-icon"><i class="bi bi-person-lines-fill"></i></span>
                </div>
                <h3 class="stat-number mb-1" id="statKk">0</h3>
                <p class="small text-muted mb-0" id="statKkSub">No SK position</p>
            </div></div>
        </div>
 
        <div class="col">
            <div class="card stat-card stat-link" data-stat="ACTIVE_SK" role="button" tabindex="0" title="Click to show these members"><div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <span class="fw-semibold text-muted small">Active SK Members</span>
                    <span class="stat-icon"><i class="bi bi-person-check-fill"></i></span>
                </div>
                <h3 class="stat-number mb-1" id="statActiveSk">0</h3>
                <p class="small text-muted mb-0">Currently serving</p>
            </div></div>
        </div>
 
    </section>
 
    <!-- ================= MEMBER MANAGEMENT ================= -->
    <div class="card member-card">
 
        <div class="card-header member-directory-header">
            <div class="member-directory-top">
                <div class="member-directory-title">
                    <h2 class="h6 fw-bold mb-1">Member Directory</h2>
                    <div class="small text-muted" id="directorySub">Search, filter and manage member profiles</div>
                </div>
                <div class="member-category-tabs" role="group" aria-label="Member categories">
                    <button type="button" class="member-category-tab active" data-member-type="ALL" aria-pressed="true">
                        All Members <span class="member-category-count" id="tabAllCount">0</span>
                    </button>
                    <button type="button" class="member-category-tab" data-member-type="KK" aria-pressed="false">
                        KK Members <span class="member-category-count" id="tabKkCount">0</span>
                    </button>
                    <button type="button" class="member-category-tab" data-member-type="SK" aria-pressed="false">
                        SK Officials <span class="member-category-count" id="tabSkCount">0</span>
                    </button>
                </div>
            </div>
 
            <div class="member-toolbar">
                <select class="d-none" id="typeFilter" aria-hidden="true" tabindex="-1">
                    <option value="ALL">All KK Members</option>
                    <option value="SK">SK Members</option>
                    <option value="KK">Regular KK Members</option>
                </select>
 
                <div class="search-box member-search">
                    <i class="bi bi-search"></i>
                    <input type="search" class="form-control form-control-sm" id="searchInput" aria-label="Search members" placeholder="Search name, position, contact, email, Purok or ID...">
                </div>
 
                <div class="dropdown member-filter-dropdown" data-bs-auto-close="outside">
                    <button type="button" class="btn btn-outline-success btn-sm" data-bs-toggle="dropdown" aria-expanded="false" aria-controls="filterPanel">
                        <i class="bi bi-funnel me-1"></i>Filter
                        <span class="member-filter-count d-none" id="filterCount">0</span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end p-3 member-filter-menu" id="filterPanel">
                        <div class="d-flex justify-content-between align-items-center gap-2 mb-2">
                            <strong class="small">Filter members</strong>
                            <button type="button" class="btn btn-link btn-sm p-0" id="resetBtn">Clear filters</button>
                        </div>
                        <div class="row g-2">
                            <div class="col-12">
                                <label class="form-label small fw-semibold mb-1" for="positionFilter">SK Position</label>
                                <select class="form-select form-select-sm" id="positionFilter"></select>
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-semibold mb-1" for="statusFilter">Status</label>
                                <select class="form-select form-select-sm" id="statusFilter">
                                    <option value="">All Status</option>
                                    <option>Active</option>
                                    <option>Inactive</option>
                                    <option>Archived</option>
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-semibold mb-1" for="purokFilter">Purok</label>
                                <select class="form-select form-select-sm" id="purokFilter"></select>
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-semibold mb-1" for="sortSelect">Sort by</label>
                                <select class="form-select form-select-sm" id="sortSelect">
                                    <option value="name-asc">Name (A to Z)</option>
                                    <option value="name-desc">Name (Z to A)</option>
                                    <option value="position">SK position</option>
                                    <option value="newest">Newest first</option>
                                    <option value="oldest">Oldest first</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
 
                <div class="dropdown admin-only">
                    <button class="btn btn-outline-success btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-printer me-1"></i>Print
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><button class="dropdown-item" type="button" id="printListBtn"><i class="bi bi-list-ul me-2"></i>Print member list</button></li>
                    </ul>
                </div>
 
                <div class="dropdown">
                    <button class="btn btn-outline-success btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-gear me-1"></i><?= is_public() ? 'Options' : 'Manage' ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li class="admin-only role-manager-only"><button class="dropdown-item" type="button" id="assignBtn"><i class="bi bi-person-badge me-2"></i>Assign SK member</button></li>
                        <li class="admin-only role-manager-only"><button class="dropdown-item" type="button" id="manageCommitteesBtn"><i class="bi bi-diagram-3 me-2"></i>Manage committees</button></li>
                        <li class="admin-only"><button class="dropdown-item" type="button" id="scanKkIdBtn"><i class="bi bi-qr-code-scan me-2"></i>Scan KK ID</button></li>
                        <li class="admin-only"><button class="dropdown-item" type="button" id="exportBtn"><i class="bi bi-filetype-csv me-2"></i>Export list (CSV)</button></li>
                        <li class="admin-only role-manager-only"><button class="dropdown-item" type="button" id="termCheckBtn"><i class="bi bi-calendar-check me-2"></i>Check ended SK terms</button></li>
                    </ul>
                </div>
 
                <div class="btn-group btn-group-sm member-view-toggle" role="group" aria-label="View mode">
                    <button type="button" class="btn btn-outline-success active" id="viewCards" title="Card view" aria-label="Card view"><i class="bi bi-grid"></i></button>
                    <button type="button" class="btn btn-outline-success" id="viewTable" title="Table view" aria-label="Table view"><i class="bi bi-table"></i></button>
                </div>
            </div>
        </div>
 
        <div class="card-body p-3 p-lg-4" id="results" aria-live="polite"></div>
 
        <div class="card-footer bg-white border-top p-3">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                <div class="small text-muted" id="resultCount">Showing 0 members</div>
                <nav aria-label="Member list pagination">
                    <ul class="pagination pagination-sm mb-0" id="pager"></ul>
                </nav>
            </div>
        </div>
 
    </div>
 
    <div class="text-center text-muted small py-4">
        KK Youth Profiling System &bull; SK Barangay Sikat, Alfonso, Cavite &bull; FY 2026&ndash;2027
    </div>
 
</main>
 
</div>
 
<!-- ================= PROFILE MODAL ================= -->
<div class="modal fade" id="profileModal" tabindex="-1" aria-labelledby="profileTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="profileTitle">Member Profile</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="profileBody"></div>
            <div class="modal-footer flex-wrap gap-2" id="profileFooter"></div>
        </div>
    </div>
</div>

<div class="modal fade" id="memberArchiveModal" tabindex="-1" aria-labelledby="memberArchiveTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content review-modal">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="memberArchiveTitle"><i class="bi bi-archive me-2"></i>Archive member record</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="memberArchiveSummary" class="member-review-card mb-4"></div>
                <div class="mb-3">
                    <label class="form-label" for="memberArchiveReason">Reason <span class="text-danger">*</span></label>
                    <select class="form-select" id="memberArchiveReason" required>
                        <option value="">Select a reason</option>
                        <option>Moved out of the barangay</option>
                        <option>No longer eligible</option>
                        <option>Requested by the youth</option>
                        <option>Duplicate record</option>
                        <option>Incorrect / invalid record</option>
                        <option>Other</option>
                    </select>
                    <div class="invalid-feedback" id="memberArchiveReasonError"></div>
                </div>
                <div class="mb-3 d-none" id="memberArchiveOtherWrap">
                    <label class="form-label" for="memberArchiveOther">Please specify <span class="text-danger">*</span></label>
                    <input class="form-control" id="memberArchiveOther" type="text" maxlength="200" autocomplete="off">
                    <div class="invalid-feedback" id="memberArchiveOtherError"></div>
                </div>
                <div>
                    <label class="form-label" for="memberArchiveRemarks">Additional remarks <span class="text-muted fw-normal">(optional)</span></label>
                    <textarea class="form-control" id="memberArchiveRemarks" rows="2" maxlength="300"></textarea>
                </div>
                <div class="form-text mt-2">Archived records are retained and can be restored from Archives.</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-dark" id="confirmMemberArchive"><i class="bi bi-archive me-1"></i>Archive member</button>
            </div>
        </div>
    </div>
</div>
 
<!-- ================= ASSIGN / EDIT SK ROLE MODAL ================= -->
<div class="modal fade" id="roleModal" tabindex="-1" aria-labelledby="roleModalTitle" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable modal-fullscreen-md-down">
        <div class="modal-content">
            <form id="roleForm" novalidate>
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="roleModalTitle">Assign SK Role</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
 
                    <!-- choose an existing KK member -->
                    <div id="pickBox">
                        <label class="form-label fw-semibold" for="pickSearch">Select KK Member <span class="text-danger">*</span></label>
                        <div class="search-box mb-2">
                            <i class="bi bi-search"></i>
                            <input type="text" class="form-control" id="pickSearch" placeholder="Search a registered KK youth by name, ID or Purok...">
                        </div>
                        <select class="form-select" id="pickMember" size="6"></select>
                        <div class="invalid-feedback"></div>
                        <div class="form-text">Only registered, active KK youth without an SK position are listed. Not in the list? Register them first in KK Youth Registration.</div>
                    </div>
 
                    <!-- member summary (read-only: comes from the KK record) -->
                    <div id="memberSummary" class="d-none"></div>
 
                    <div class="group-title">SK Position and Status</div>
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">SK Position <span class="text-danger">*</span></label>
                            <div class="pos-choices" id="posChoices" role="radiogroup" aria-label="SK position"></div>
                            <select class="d-none" id="rolePosition"></select>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-12 d-none" id="kagawadCommitteeField">
                            <label class="form-label fw-semibold">Committee Assignments <span class="text-danger">*</span></label>
                            <div class="committee-options" id="roleCommitteeChoices"></div>
                            <div class="invalid-feedback"></div>
                            <label class="committee-option mt-2">
                                <input type="checkbox" class="form-check-input" id="committeeOtherToggle">
                                <span>Others</span>
                            </label>
                            <div class="mt-2 d-none" id="committeeOtherField">
                                <label class="form-label small fw-semibold" for="committeeOther">Other committee</label>
                                <input type="text" class="form-control" id="committeeOther" maxlength="100" placeholder="Specify another committee">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" for="roleStatus">SK Status</label>
                            <select class="form-select" id="roleStatus">
                                <option>Active</option>
                                <option>Inactive</option>
                                <option>Archived</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" for="roleBasis">Basis <span class="text-danger">*</span></label>
                            <select class="form-select" id="roleBasis">
                                <option value="">Select basis</option>
                                <option value="elected">Elected</option>
                                <option value="appointed">Appointed</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
 
                    <div class="group-title">Election and Term</div>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold" for="roleYear">Election Year <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="roleYear" min="2000" max="2100" placeholder="e.g. 2026">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold" for="roleElected">Date Elected <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="roleElected">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-4"></div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" for="roleStart">Term Start <span class="text-muted">(optional)</span></label>
                            <input type="date" class="form-control" id="roleStart">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" for="roleEnd">Term End <span class="text-muted">(optional)</span></label>
                            <input type="date" class="form-control" id="roleEnd">
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
 
                    <div class="group-title">Description</div>
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold" for="roleDesc">Short Description</label>
                            <textarea class="form-control" id="roleDesc" rows="2" maxlength="500" placeholder="Short description of this SK official"></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold" for="roleResp">Responsibilities</label>
                            <textarea class="form-control" id="roleResp" rows="3" maxlength="600" placeholder="Filled in automatically from the position. You can edit it."></textarea>
                        </div>
                    </div>
 
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success" id="roleSaveBtn">
                        <i class="bi bi-save me-2"></i><span id="roleSaveLabel">Assign SK Role</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
 
<!-- ================= COMMITTEE MANAGEMENT ================= -->
<div class="modal fade" id="committeeModal" tabindex="-1" aria-labelledby="committeeModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="committeeModalTitle">Manage Committees</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="small text-muted">Assign an SK official as chairperson of each committee. One official may chair more than one committee.</p>
                <div id="committeeList" class="list-group mb-3"></div>
                <button type="button" class="btn btn-primary mb-3" id="saveCommitteeChairsBtn">Save Chairpersons</button>
                <button type="button" class="btn btn-outline-primary mb-3" id="randomizeCommitteeChairsBtn">Randomly Assign All</button>
                <p class="small text-muted mb-0">Random assignments are saved and stay the same until changed.</p>
                <hr>
                <p class="small text-muted">Add or rename committees. Renaming updates committee memberships and chairperson assignments.</p>
                <form id="committeeForm">
                    <input type="hidden" id="committeeOriginal">
                    <label class="form-label fw-semibold" for="committeeName">Committee name</label>
                    <input type="text" class="form-control" id="committeeName" maxlength="100" required>
                    <div class="invalid-feedback"></div>
                    <div class="d-flex gap-2 mt-3">
                        <button type="submit" class="btn btn-success" id="committeeSaveBtn">Add Committee</button>
                        <button type="button" class="btn btn-outline-secondary d-none" id="committeeCancelEditBtn">Cancel edit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
 
<!-- ================= LINK EXISTING MEMBER ACCOUNT MODAL ================= -->
<div class="modal fade" id="linkAccountModal" tabindex="-1" aria-labelledby="linkAccountTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="linkAccountTitle">Link an Existing Account</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="small text-muted">Linking connects one existing login to this KK member. It does not create an account or change its credentials.</p>
                <label class="form-label" for="linkAccountSelect">Unlinked account</label>
                <select class="form-select" id="linkAccountSelect"></select>
                <div class="small text-danger mt-2" id="linkAccountError"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmLinkAccount">Link account</button>
            </div>
        </div>
    </div>
</div>
 
<!-- ================= CREATE MEMBER ACCOUNT MODAL ================= -->
<div class="modal fade" id="createAccountModal" tabindex="-1" aria-labelledby="createAccountTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="createAccountTitle">Create Separate Member Login</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="small text-muted">This is separate from assigning an SK role. Each KK member may have only one login.</p>
                <label class="form-label" for="newAccountUsername">Username</label>
                <input class="form-control mb-3" id="newAccountUsername" maxlength="50" autocomplete="off">
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" id="guardianConsent">
                    <label class="form-check-label" for="guardianConsent">Guardian consent has been obtained (when required)</label>
                </div>
                <label class="form-label" for="consentVersion">Consent form version (if consent is recorded)</label>
                <input class="form-control" id="consentVersion" maxlength="50" placeholder="Enter the actual form version">
                <div class="small text-danger mt-2" id="createAccountError"></div>
                <div class="alert alert-success d-none mt-3 mb-0" id="temporaryPasswordBox"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="confirmCreateAccount">Create account</button>
            </div>
        </div>
    </div>
</div>
 
<!-- ================= KK ID SCANNER ================= -->
<div class="modal fade" id="kkIdScannerModal" tabindex="-1" aria-labelledby="kkIdScannerTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="kkIdScannerTitle">Scan KK ID</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="small text-muted">Only authorized SK staff can verify an ID. The QR contains an opaque verification code, not member details.</p>
                <div class="row g-3 align-items-start">
                    <div class="col-md-6">
                        <video id="kkIdCamera" class="w-100 rounded border bg-dark" autoplay muted playsinline aria-label="Camera view for scanning a KK ID QR"></video>
                        <div class="small text-muted mt-2" id="kkIdCameraStatus">Camera scanning is optional. You can enter the QR value manually.</div>
                        <button type="button" class="btn btn-outline-primary btn-sm mt-2" id="startKkIdCamera">
                            <i class="bi bi-camera me-1"></i>Start camera scanner
                        </button>
                    </div>
                    <div class="col-md-6">
                        <form id="kkIdScanForm">
                            <label class="form-label fw-semibold" for="kkIdScanValue">QR value</label>
                            <input type="text" class="form-control" id="kkIdScanValue" maxlength="69" autocomplete="off" placeholder="KKID: followed by the QR value" required>
                            <button type="submit" class="btn btn-primary mt-2" id="verifyKkIdButton">Verify ID</button>
                        </form>
                        <div class="mt-3" id="kkIdScanResult" aria-live="polite"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
 
<!-- ================= CONFIRMATION MODAL ================= -->
<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="cfTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="cfTitle">Are you sure?</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body"><p class="mb-0" id="cfText"></p></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-dark" id="cfOk">Confirm</button>
            </div>
        </div>
    </div>
</div>
 
<div id="toastBox" aria-live="assertive"></div>
<div id="printArea"></div>
 
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/qrcode-generator@1.4.4/qrcode.min.js" integrity="sha384-lQXOAyZwHXE55JFyrOMB7nY2Wv+m5ZWNtJcHrd1rceRQXAYNLak8ukN5TjBTcIwz" crossorigin="anonymous"></script>
<script src="kk-id.js?v=1"></script>
<script src="sk-data.js?v=2"></script>
<script>
(function () {
    'use strict';
 
    var D = window.SKData;
    var ACCOUNT_ROLE_SEATS = window.SK_ACCOUNT_ROLE_SEATS || {};
    if (!window.SK_CAN_MANAGE_ROLES) {
        document.querySelectorAll('.role-manager-only').forEach(function (element) {
            element.classList.add('d-none');
        });
    }
    var MIN_AGE = 15, MAX_AGE = 30, PAGE_SIZE = 10;
    var BARANGAY = 'Sikat', CITY = 'Alfonso', PROVINCE = 'Cavite';
    var POSITIONS = D.POSITIONS.slice();
    if (D.roles.some(function (role) { return role.position === 'SK Vice Chairperson'; })) {
        POSITIONS.push('SK Vice Chairperson');
    }
    var ROLES = D.ROLES;
    var DEFAULT_COMMITTEES = [
        'Committee on Good Governance and Active Citizenship',
        'Committee on Environment & Disaster Risk Resiliency',
        'Committee on Anti-Drug Abuse and Social Protection',
        'Committee on Education and Culture',
        'Committee on Health and Sanitation',
        'Committee on Youth and Sports Development',
        'Committee on Budget and Appropriation'
    ];
    var COMMITTEES = DEFAULT_COMMITTEES.slice();
    function loadCommittees() {
        try {
            var stored = JSON.parse(DB.getItem('skCommittees') || 'null');
            if (Array.isArray(stored) && stored.every(function (name) { return typeof name === 'string'; })) {
                COMMITTEES = Array.from(new Set(DEFAULT_COMMITTEES.concat(stored)));
                return;
            }
        } catch (error) {
            notify('danger', 'Could not read the committee list.');
        }
        COMMITTEES = DEFAULT_COMMITTEES.slice();
    }
    var PUROKS = ['Purok 1', 'Purok 2', 'Purok 3', 'Purok 4', 'Purok 5'];
    var kkName = D.kkName, calcAge = D.calcAge, norm = D.norm, todayISO = D.todayISO;
 
    /* ---------- helpers ---------- */
    function $(id) { return document.getElementById(id); }
    function memberRoleOf(kkId) {
        var role = D.roleOf(kkId);
        return role && POSITIONS.indexOf(role.position) !== -1 ? role : null;
    }
    function occupiedSeats(position, exceptRoleId) {
        var today = todayISO();
        var roleSeats = D.roles.filter(function (role) {
            var rolePosition = role.position === 'SK Chairman' ? 'SK Chairperson' : role.position;
            return role.id !== exceptRoleId && rolePosition === position && role.status === 'Active'
                && (!role.termStart || role.termStart <= today) && (!role.termEnd || role.termEnd >= today);
        }).length;
        return roleSeats + (Number(ACCOUNT_ROLE_SEATS[position]) || 0);
    }
    function positionSeatError(position, exceptRoleId) {
        var cap = D.SEATS[position];
        if (!cap || occupiedSeats(position, exceptRoleId) < cap) return '';
        return cap === 1
            ? 'There is already an active ' + position + ' role or account. Reassign that seat first.'
            : 'All ' + cap + ' ' + position + ' seats are already filled by active members.';
    }
    function esc(s) {
        return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
        });
    }
    function fmtDate(iso) {
        if (!iso) return '\u2014';
        var d = new Date(String(iso).length === 10 ? iso + 'T00:00:00' : iso);
        return isNaN(d) ? '\u2014' : d.toLocaleDateString('en-PH', { year: 'numeric', month: 'short', day: 'numeric' });
    }
    function yearOf(iso) { return iso ? String(iso).slice(0, 4) : ''; }
    function modal(id) { return bootstrap.Modal.getOrCreateInstance($(id)); }
    function isOpen(id) { return $(id).classList.contains('show'); }
    function afterHide(id, fn) {
        if (!isOpen(id)) { fn(); return; }
        $(id).addEventListener('hidden.bs.modal', function once() { $(id).removeEventListener('hidden.bs.modal', once); fn(); });
        modal(id).hide();
    }
    function notify(type, text) {
        var div = document.createElement('div');
        div.className = 'alert alert-' + type + ' shadow small mb-2';
        div.textContent = text;
        $('toastBox').appendChild(div);
        setTimeout(function () { div.remove(); }, 5000);
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
    function fillSelect(sel, list, first) {
        sel.innerHTML = (first !== undefined ? '<option value="">' + esc(first) + '</option>' : '') +
            list.map(function (t) { return '<option>' + esc(t) + '</option>'; }).join('');
    }
    function reload() { D.load(); }
 
    /* ---------- member records (one per KK member, SK info joined in) ---------- */
    function addrOf(m) { return [m.houseNo, m.street, m.purok, 'Brgy. ' + (m.barangay || BARANGAY), m.city || CITY, m.province || PROVINCE].filter(Boolean).join(', '); }
    function eligibility(m) {
        var a = m.birthDate ? calcAge(m.birthDate) : NaN;
        if (isNaN(a)) return 'Unknown';
        return a < MIN_AGE ? 'Under age' : (a > MAX_AGE ? 'Over age' : 'Eligible');
    }
    function buildRecords() {
        return D.kk.map(function (m) {
            var r = memberRoleOf(m.id), age = m.birthDate ? calcAge(m.birthDate) : '';
            var kkStatus = m.status === 'Archived' ? 'Archived' : 'Active';
            return {
                key: m.id, kk: m, role: r || null, isSK: !!r, name: kkName(m), photo: m.photo || '', age: age,
                contact: m.mobile || '', email: m.email || '', purok: m.purok || '', address: addrOf(m), code: m.memberNo || '',
                kkStatus: kkStatus, position: r ? r.position : '', skStatus: r ? r.status : '',
                status: r ? r.status : kkStatus, rank: r ? Math.max(0, POSITIONS.indexOf(r.position)) + 1 : 9,
                created: m.createdAt || '',
                text: [kkName(m), m.mobile, m.email, m.purok, m.memberNo, m.street, m.houseNo, m.gender, r ? r.position : '', r ? r.electionYear : ''].join(' ').toLowerCase()
            };
        });
    }
    function getRec(key) { return buildRecords().filter(function (r) { return r.key === key; })[0]; }
 
    function termLabel(r) { return r && r.termStart ? yearOf(r.termStart) + '\u2013' + yearOf(r.termEnd) : ''; }
    function termLine(r) {
        if (!r.role) return '\u2014';
        var t = termLabel(r.role);
        return (t ? 'Term ' + t : 'No term') + (r.role.electionYear ? ' \u2022 Elected ' + r.role.electionYear : '');
    }
    function shortDesc(r) {
        if (r.isSK) return r.role.description || r.role.responsibilities || ROLES[r.position] || '';
        return [r.age !== '' ? r.age + ' years old' : '', r.kk.gender, r.kk.classification].filter(Boolean).join(' \u2022 ');
    }
    function committeesOfRole(role) {
        var values = Array.isArray(role && role.committees) ? role.committees
            : (Array.isArray(role && role.committee) ? role.committee : (role && role.committee ? [role.committee] : []));
        return values.filter(function (value, index, list) {
            return typeof value === 'string' && value.trim() && list.indexOf(value) === index;
        });
    }
    function committeeLabel(value) {
        return value.indexOf('Other: ') === 0 ? value.slice(7) : value;
    }
    function committeeBadges(role) {
        var values = committeesOfRole(role);
        return values.length ? '<div class="committee-badges">' + values.map(function (value) {
            return '<span class="committee-badge">' + esc(committeeLabel(value)) + '</span>';
        }).join('') + '</div>' : '';
    }
 
    /* ---------- filters + list ---------- */
    var view = 'cards', page = 1, loading = true;
 
    function filtered() {
        var q = norm($('searchInput').value), t = $('typeFilter').value, pos = $('positionFilter').value,
            st = $('statusFilter').value, pu = $('purokFilter').value, so = $('sortSelect').value;
        var list = buildRecords().filter(function (r) {
            if (t === 'SK' && !r.isSK) return false;
            if (t === 'KK' && r.isSK) return false;
            if (pos && r.position !== pos) return false;
            if (st && r.status !== st) return false;
            if (pu && r.purok !== pu) return false;
            if (q && r.text.indexOf(q) < 0) return false;
            return true;
        });
        list.sort(function (a, b) {
            var currentMemberId = String(window.SK_CURRENT_MEMBER_ID || '');
            if (currentMemberId) {
                var aIsCurrentUser = String(a.key) === currentMemberId;
                var bIsCurrentUser = String(b.key) === currentMemberId;
                if (aIsCurrentUser !== bIsCurrentUser) return aIsCurrentUser ? -1 : 1;
            }
            if (so === 'name-desc') return norm(b.name).localeCompare(norm(a.name));
            if (so === 'position') return a.rank - b.rank || norm(a.name).localeCompare(norm(b.name));
            if (so === 'newest') return String(b.created).localeCompare(String(a.created));
            if (so === 'oldest') return String(a.created).localeCompare(String(b.created));
            return norm(a.name).localeCompare(norm(b.name));
        });
        return list;
    }
 
    function refreshFilterOptions() {
        var pSel = $('positionFilter'), uSel = $('purokFilter'), pv = pSel.value, uv = uSel.value;
        fillSelect(pSel, POSITIONS, 'All SK Positions');
        if (POSITIONS.indexOf(pv) > -1) pSel.value = pv;
        var puroks = PUROKS.slice();
        D.kk.forEach(function (m) { if (m.purok && puroks.indexOf(m.purok) < 0) puroks.push(m.purok); });
        fillSelect(uSel, puroks, 'All Purok');
        if (puroks.indexOf(uv) > -1) uSel.value = uv;
    }
 
    function renderStats() {
        var total = D.kk.length;
        var archivedKk = D.kk.filter(function (m) { return m.status === 'Archived'; }).length;
        var skMemberIds = Object.create(null), activeSkMemberIds = Object.create(null);
        var today = todayISO();
        D.roles.forEach(function (role) {
            var memberId = String(role.kkId || '');
            var member = D.kkById(memberId);
            if (!member) return;
            skMemberIds[memberId] = true;
            if (role.status === 'Active'
                && (member.status || 'Active') === 'Active'
                && (!role.termStart || role.termStart <= today)
                && (!role.termEnd || role.termEnd >= today)) {
                activeSkMemberIds[memberId] = true;
            }
        });
        var sk = Object.keys(skMemberIds).length;
        $('statTotal').textContent = total;
        $('statTotalSub').textContent = (total - archivedKk) + ' active \u2022 ' + archivedKk + ' archived';
        $('statSk').textContent = sk;
        $('statSkSub').textContent = D.roles.filter(function (r) { return r.status === 'Archived'; }).length + ' term ended \u2022 ' + D.roles.filter(function (r) { return r.status === 'Inactive'; }).length + ' inactive';
        $('statKk').textContent = total - sk;
        $('statKkSub').textContent = 'No SK position';
        $('statActiveSk').textContent = Object.keys(activeSkMemberIds).length;
        $('tabAllCount').textContent = total;
        $('tabKkCount').textContent = total - sk;
        $('tabSkCount').textContent = sk;
    }
 
    function avatarHtml(r, size) {
        var st = 'width:' + size + 'px;height:' + size + 'px;font-size:' + Math.round(size / 2.6) + 'px;';
        if (r.photo) return '<img src="' + esc(r.photo) + '" alt="" class="thumb" style="' + st + '">';
        var parts = String(r.name || '?').trim().split(/\s+/);
        var ini = (parts[0].charAt(0) + (parts.length > 1 ? parts[parts.length - 1].charAt(0) : '')).toUpperCase();
        return '<span class="thumb" style="' + st + '">' + esc(ini) + '</span>';
    }
    function statusBadge(s, label) {
        var cls = s === 'Active' ? 'status-active' : (s === 'Archived' ? 'status-archived' : 'status-inactive');
        return '<span class="badge ' + cls + '">' + esc(label ? label + ': ' + s : s) + '</span>';
    }
    function actBtn(key, act, icon, title, cls) {
        return '<button type="button" class="btn btn-sm action-btn ' + cls + '" data-act="' + act + '" data-key="' + esc(key) + '" title="' + esc(title) + '" aria-label="' + esc(title) + '"><i class="bi ' + icon + '"></i></button>';
    }
    function pills(r) {
        return '<span class="type-pill">KK MEMBER</span>' + (r.isSK ? '<span class="type-pill">SK MEMBER</span>' : '');
    }
 
    function cardHtml(r) {
        var term = r.isSK ? termLine(r) : 'Registered ' + fmtDate(r.created);
        var classification = r.kk.classification === 'Other'
            ? (r.kk.classificationOther || 'Other')
            : r.kk.classification;
        var status = r.isSK ? r.skStatus : r.kkStatus;
        var isActive = status === 'Active';
        var roleText = r.isSK ? r.position : 'KK Member';
        return '<article class="card mini-card' + (r.kkStatus === 'Archived' ? ' is-archived' : '') + '" data-act="view" data-key="' + esc(r.key) + '" role="button" tabindex="0" aria-label="Open profile for ' + esc(r.name) + '">' +
            '<div class="mini-body">' +
                '<span class="mini-avatar">' + avatarHtml(r, 96) + '</span>' +
                '<div class="mini-main">' +
                    '<div class="mini-name" title="' + esc(r.name) + '">' + esc(r.name) + '</div>' +
                    '<div class="mini-role" title="' + esc(roleText) + '">' + esc(roleText) + '</div>' +
                    '<span class="mini-status ' + (isActive ? 'is-active' : 'is-inactive') + '">' + (isActive ? 'Active' : 'Inactive') + '</span>' +
                    '<div class="mini-tags">' +
                        (r.age !== '' ? '<span class="mini-tag">' + esc(r.age) + ' yrs</span>' : '') +
                        (classification ? '<span class="mini-tag">' + esc(classification) + '</span>' : '') +
                    '</div>' +
                '</div>' +
            '</div>' +
            '<div class="mini-info-grid">' +
                '<div class="mini-info-item"><i class="bi bi-telephone"></i><span title="' + esc(r.contact || '\u2014') + '">' + esc(r.contact || '\u2014') + '</span></div>' +
                '<div class="mini-info-item"><i class="bi bi-envelope"></i><span title="' + esc(r.email || '\u2014') + '">' + esc(r.email || '\u2014') + '</span></div>' +
                '<div class="mini-info-item"><i class="bi bi-geo-alt"></i><span title="' + esc(r.purok || '\u2014') + '">' + esc(r.purok || '\u2014') + '</span></div>' +
                '<div class="mini-info-item"><i class="bi bi-calendar3"></i><span title="' + esc(term) + '">' + esc(term) + '</span></div>' +
            '</div>' +
            '<div class="mini-footer">' +
                '<span class="mini-idno">' + esc(r.code || 'NO ID') + '</span>' +
                '<span class="mini-view"><i class="bi bi-person-vcard"></i>View profile</span>' +
            '</div></article>';
    }
 
    function tableHtml(list) {
        return '<div class="table-responsive"><table class="table table-hover align-middle mb-0"><thead><tr>' +
            '<th>Member</th><th>Age</th><th>Contact</th><th>Address / Purok</th><th>KK Status</th><th>SK Position</th><th>SK Status</th><th>Election / Term</th></tr></thead><tbody>' +
            list.map(function (r) {
                return '<tr class="member-row" data-act="view" data-key="' + esc(r.key) + '" tabindex="0" role="button" aria-label="Open profile for ' + esc(r.name) + '"><td style="min-width:210px;"><div class="d-flex align-items-center gap-2">' + avatarHtml(r, 39) +
                    '<div><div class="member-name">' + esc(r.name) + '</div><div class="member-id">' + esc(r.code) + ' ' + (r.isSK ? 'KK + SK' : 'KK') + '</div>' +
                    (shortDesc(r) ? '<div class="member-id text-truncate" style="max-width:220px;">' + esc(shortDesc(r)) + '</div>' : '') + '</div></div></td>' +
                    '<td>' + esc(r.age) + '</td>' +
                    '<td><div>' + esc(r.contact || '\u2014') + '</div><div class="member-id">' + esc(r.email || '') + '</div></td>' +
                    '<td><div class="member-address text-truncate" title="' + esc(r.address || r.purok || '\u2014') + '">' + esc(r.address || r.purok || '\u2014') + '</div></td>' +
                    '<td>' + statusBadge(r.kkStatus) + '</td>' +
                    '<td>' + esc(r.position || '\u2014') + '</td>' +
                    '<td>' + (r.isSK ? statusBadge(r.skStatus) : '\u2014') + '</td>' +
                    '<td class="text-nowrap">' + esc(termLine(r)) + '</td></tr>';
            }).join('') + '</tbody></table></div>';
    }
 
    function emptyHtml(total) {
        if (!total) {
            return '<div class="empty-state"><i class="bi bi-people"></i><h5 class="fw-bold mt-3">No KK members yet</h5>' +
                '<p class="small mb-3">Register KK youth first. Once registered they appear here and an SK position can be assigned to them.</p>' +
                '<a href="registration.php" class="btn btn-success btn-sm"><i class="bi bi-person-plus-fill me-1"></i>Register KK Youth</a></div>';
        }
        return '<div class="empty-state"><i class="bi bi-person-x"></i><h5 class="fw-bold mt-3">No members found</h5><p class="small mb-0">Try changing your search or filters.</p></div>';
    }
 
    function updateMemberToolbar() {
        var type = $('typeFilter').value;
        document.querySelectorAll('[data-member-type]').forEach(function (tab) {
            var active = tab.dataset.memberType === type;
            tab.classList.toggle('active', active);
            tab.setAttribute('aria-pressed', active ? 'true' : 'false');
        });
        var activeFilters = ['positionFilter', 'statusFilter', 'purokFilter'].filter(function (id) {
            return !!$(id).value;
        }).length;
        $('filterCount').textContent = activeFilters;
        $('filterCount').classList.toggle('d-none', activeFilters === 0);
    }
 
    function renderList() {
        var box = $('results');
        updateMemberToolbar();
        if (loading) {
            box.innerHTML = '<div class="text-center text-muted py-5"><div class="spinner-border mb-3" role="status"></div><div class="small">Loading members...</div></div>';
            $('pager').innerHTML = ''; $('resultCount').textContent = 'Loading...';
            return;
        }
        var list = filtered(), pages = Math.max(1, Math.ceil(list.length / PAGE_SIZE));
        if (page > pages) page = pages;
        var slice = list.slice((page - 1) * PAGE_SIZE, page * PAGE_SIZE);
 
        if (!slice.length) box.innerHTML = emptyHtml(D.kk.length);
        else if (view === 'cards') box.innerHTML = '<div class="member-id-grid">' + slice.map(cardHtml).join('') + '</div>';
        else box.innerHTML = tableHtml(slice);
 
        $('resultCount').textContent = list.length
            ? 'Showing ' + ((page - 1) * PAGE_SIZE + 1) + '\u2013' + Math.min(page * PAGE_SIZE, list.length) + ' of ' + list.length + ' member' + (list.length !== 1 ? 's' : '')
            : 'Showing 0 members';
        $('directorySub').textContent = { ALL: 'All KK members', SK: 'SK members (KK members with an SK position)', KK: 'Regular KK members (no SK position)' }[$('typeFilter').value] + ' \u2022 search, filter and manage profiles';
 
        var p = '', from = Math.max(1, page - 2), to = Math.min(pages, page + 2);
        p += '<li class="page-item' + (page === 1 ? ' disabled' : '') + '"><button class="page-link" data-page="' + (page - 1) + '">Previous</button></li>';
        for (var i = from; i <= to; i++) p += '<li class="page-item' + (i === page ? ' active' : '') + '"><button class="page-link" data-page="' + i + '">' + i + '</button></li>';
        p += '<li class="page-item' + (page === pages ? ' disabled' : '') + '"><button class="page-link" data-page="' + (page + 1) + '">Next</button></li>';
        $('pager').innerHTML = pages > 1 ? p : '';
        $('viewCards').classList.toggle('active', view === 'cards');
        $('viewTable').classList.toggle('active', view === 'table');
        markStats();
    }
 
    /* ---------- clickable summary cards: click one to show those members ---------- */
    function markStats() {
        var t = $('typeFilter').value, s = $('statusFilter').value;
        var plain = !$('positionFilter').value && !$('purokFilter').value && !$('searchInput').value.trim();
        document.querySelectorAll('.stat-link').forEach(function (c) {
            var k = c.dataset.stat;
            var on = plain && ((k === 'ALL' && t === 'ALL' && !s) || (k === 'SK' && t === 'SK' && !s) ||
                               (k === 'KK' && t === 'KK' && !s) || (k === 'ACTIVE_SK' && t === 'SK' && s === 'Active'));
            c.classList.toggle('selected', on);
            c.setAttribute('aria-pressed', on ? 'true' : 'false');
        });
    }
    function showStat(k) {
        $('searchInput').value = ''; $('positionFilter').value = ''; $('purokFilter').value = '';
        $('typeFilter').value = k === 'ALL' ? 'ALL' : (k === 'KK' ? 'KK' : 'SK');
        $('statusFilter').value = k === 'ACTIVE_SK' ? 'Active' : '';
        page = 1; renderList();
        document.querySelector('.member-card').scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
    document.querySelectorAll('.stat-link').forEach(function (c) {
        c.addEventListener('click', function () { showStat(c.dataset.stat); });
        c.addEventListener('keydown', function (e) { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); showStat(c.dataset.stat); } });
    });
 
    function renderUnlinked() {
        var u = D.read(D.KEYS.UNLINKED, []);
        if (!Array.isArray(u) || !u.length) { $('unlinkedBox').innerHTML = ''; return; }
        $('unlinkedBox').innerHTML = '<div class="alert alert-secondary d-flex flex-column flex-md-row gap-2 justify-content-between align-items-md-center mb-4">' +
            '<div class="small"><strong>' + u.length + ' SK official' + (u.length > 1 ? 's' : '') + ' from the old SK list could not be matched to a registered KK member:</strong> ' +
            esc(u.map(function (x) { return x.name + ' (' + x.position + ')'; }).join('; ')) +
            '. Register them in KK Youth Registration, then assign their SK position here.</div>' +
            '<div class="d-flex gap-2 flex-none"><a href="registration.php" class="btn btn-sm btn-outline-success">Register KK Youth</a>' +
            '<button type="button" class="btn btn-sm btn-dark" id="dismissUnlinked">Dismiss</button></div></div>';
    }
 
    function renderAll() { refreshFilterOptions(); renderStats(); renderUnlinked(); renderList(); }
 
    /* ---------- profile (view / print) ---------- */
    function raw(html) { return { html: html }; }
    function kv(l, v) {
        var out = (v == null || v === '' || (Array.isArray(v) && !v.length)) ? '\u2014' : (v.html !== undefined ? v.html : esc(Array.isArray(v) ? v.join(', ') : v));
        return '<div class="col-sm-6 col-lg-4"><div class="kv-l">' + esc(l) + '</div><div class="kv-v">' + out + '</div></div>';
    }
    function sec(title, rows) { return '<div class="group-title">' + esc(title) + '</div><div class="row g-3">' + rows + '</div>'; }
    function histList(h) {
        h = (h || []).slice().reverse();
        if (!h.length) return '<div class="small text-muted">No history.</div>';
        return '<ul class="ps-3 mb-0">' + h.map(function (x) {
            return '<li class="small"><strong>' + esc(x.action) + '</strong> \u2013 ' + fmtDate(x.at) + ' <span class="text-muted">(' + esc(x.by) + ')</span>' +
                (x.note ? '<br><span class="text-muted">' + esc(x.note) + '</span>' : '') + '</li>';
        }).join('') + '</ul>';
    }
 
    function profileHtml(r) {
        var m = r.kk, ro = r.role;
        var head = '<div class="d-flex flex-column flex-sm-row align-items-sm-center gap-3 mb-2">' + avatarHtml(r, 120) +
            '<div><h4 class="fw-bold mb-1">' + esc(r.name) + '</h4>' +
            '<div class="text-muted mb-2">' + esc(r.isSK ? r.position + ' \u2022 KK Member' : 'KK Member (Youth)') + '</div>' +
            '<div class="d-flex flex-wrap gap-2 align-items-center">' + statusBadge(r.kkStatus, 'KK') + (r.isSK ? statusBadge(r.skStatus, 'SK') : '') + pills(r) +
            (r.code ? '<span class="member-id">' + esc(r.code) + '</span>' : '') + '</div></div></div>';
 
        var skPart = '';
        if (r.isSK) {
            skPart =
                (ro.status === 'Archived' ? '<div class="alert alert-secondary small mt-3 mb-0">This SK term has ended, so the SK role is archived. Use <strong>Edit SK Role</strong> to record a new term.</div>' : '') +
                sec('SK Information', kv('SK position', ro.position) +
                    (ro.position === 'SK Kagawad' ? kv('Committee assignments', raw(committeeBadges(ro) || '\u2014')) : '') +
                    kv('SK status', ro.status) + kv('Election year', ro.electionYear) + kv('Date elected', fmtDate(ro.dateElected)) +
                    kv('Term start', fmtDate(ro.termStart)) + kv('Term end', fmtDate(ro.termEnd)) + kv('Term', termLabel(ro))) +
                '<div class="group-title">Short Description</div><div class="small">' + esc(ro.description || 'No description added.') + '</div>' +
                '<div class="group-title">Responsibilities</div><div class="small">' + esc(ro.responsibilities || ROLES[ro.position] || 'No responsibilities recorded.') + '</div>';
        }
        var a = r.age;
        return head +
            (m.status === 'Archived' ? '<div class="alert alert-secondary small mt-3 mb-0">' + esc(m.archiveReason || 'This KK record is archived.') + '</div>' : '') +
            skPart +
            sec('Personal Information', kv('Age', a) + kv('Sex', m.gender === 'Prefer to self-describe' ? m.genderOther : m.gender) + (window.SK_PUBLIC ? '' : kv('Date of birth', fmtDate(m.birthDate))) + kv('Civil status', m.civilStatus) + kv('Religion', m.religion === 'Other' ? m.religionOther : m.religion)) +
            sec('Contact Information', kv('Mobile number', m.mobile) + kv('Email', m.email) + kv('Facebook', m.facebook)) +
            sec('Address / Purok', kv('Purok', m.purok) + kv('Full address', addrOf(m))) +
            sec('KK Membership', kv('KK member no.', m.memberNo) + kv('KK status', r.kkStatus) + kv('Registration date', fmtDate(m.createdAt)) + kv('Eligibility', eligibility(m) + ' (ages ' + MIN_AGE + '\u2013' + MAX_AGE + ')') + kv('Registered SK voter', m.skVoter)) +
            sec('Youth Profiling', kv('Classification', m.classification === 'Other' ? m.classificationOther : m.classification) + kv('Educational attainment', m.educationLevel) + kv('Education status', m.educationStatus) +
                kv('School / institution', m.school === 'Other' ? m.schoolOther : m.school) + kv('Employment status', m.employment) + kv('Occupation / job', m.occupation) + kv('Program interests', m.interests)) +
            (r.isSK ? '<div class="group-title">SK Role History</div>' + histList(ro.history) : '');
    }
 
    function loadIdPrintHistory(memberId) {
        var target = $('idPrintHistory');
        if (!target) return;
        KKId.api('kk_id_prints', { memberId: String(memberId) }).then(function (result) {
            target.replaceChildren();
            if (!result.prints.length) {
                target.textContent = 'No ID prints recorded.';
                return;
            }
            var table = document.createElement('table');
            table.className = 'table table-sm mb-0';
            var body = document.createElement('tbody');
            result.prints.forEach(function (record) {
                var row = document.createElement('tr');
                [record.printed_at, record.print_method === 'manual' ? 'Manual' : 'Automatic', record.printed_by].forEach(function (value) {
                    var cell = document.createElement('td');
                    cell.textContent = String(value || '\u2014');
                    row.appendChild(cell);
                });
                body.appendChild(row);
            });
            table.appendChild(body);
            target.appendChild(table);
        }).catch(function (error) {
            target.textContent = error.message || 'Could not load ID print history.';
        });
    }
 
    function openProfile(key) {
        var r = getRec(key);
        if (!r) return;
        $('profileTitle').textContent = r.isSK ? 'SK Member Profile' : 'KK Member Profile';
        $('profileBody').innerHTML = profileHtml(r);
        if (!window.SK_PUBLIC && window.SK_CAN_SCAN_KK_ID) {
            $('profileBody').insertAdjacentHTML('beforeend',
                '<div class="group-title">KK ID Print History</div><div class="small text-muted" id="idPrintHistory">Loading print history...</div>');
            loadIdPrintHistory(r.key);
        }
        var canPrintId = !window.SK_PUBLIC && window.SK_CAN_SCAN_KK_ID && r.kkStatus === 'Active';
        var f = '<div class="kk-group kk-print-group">';
        if (canPrintId) {
            f += '<button type="button" class="btn btn-primary" id="btnPrintId" data-act="print-id" data-key="' + esc(key) + '"><i class="bi bi-person-vcard me-1"></i>Print KK ID</button>';
        }
        f += '<button type="button" class="btn btn-outline-secondary" id="btnPrintProfile" data-act="print" data-key="' + esc(key) + '"><i class="bi bi-printer me-1"></i>Print Profile</button>';
        if (canPrintId) {
            f += '<button type="button" class="btn btn-outline-secondary" id="btnLogPrint" data-act="log-manual-id" data-key="' + esc(key) + '"><i class="bi bi-journal-check me-1"></i>Log Manual Print</button>';
        }
        f += '</div><div class="kk-group kk-manage-group">';
        if (window.SK_CAN_MANAGE_ROLES && r.kkStatus !== 'Archived') {
            f += '<button type="button" class="btn btn-outline-secondary" id="btnEditReg" data-act="kkedit" data-key="' + esc(key) + '"><i class="bi bi-pencil-square me-1"></i>Edit Member</button>';
        }
        if (window.SK_CAN_MANAGE_ROLES && (r.kkStatus !== 'Archived' || r.isSK)) {
            f += '<button type="button" class="btn btn-outline-secondary" id="btnAssignRole" data-act="role" data-key="' + esc(key) + '"><i class="bi bi-shield-check me-1"></i>' + (r.isSK ? 'Edit SK Role' : 'Assign SK Role') + '</button>';
        }
        if (window.SK_CAN_MANAGE_ROLES && r.isSK) {
            f += '<button type="button" class="btn btn-outline-danger" data-act="remove" data-key="' + esc(key) + '"><i class="bi bi-person-x me-1"></i>End SK Role</button>';
            f += '<button type="button" class="btn btn-outline-secondary" data-act="archive-role" data-key="' + esc(key) + '"><i class="bi bi-archive me-1"></i>Archive SK Role</button>';
        }
        if (window.SK_CAN_MANAGE_ROLES && r.kkStatus !== 'Archived') {
            f += '<button type="button" class="btn btn-outline-danger" data-act="archive-member" data-key="' + esc(key) + '"><i class="bi bi-archive me-1"></i>Archive KK Member</button>';
        }
        if (window.SK_CAN_MANAGE_ROLES) {
            f += '<div class="dropdown">' +
                '<button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="bi bi-key me-1"></i>Login Account</button>' +
                '<ul class="dropdown-menu dropdown-menu-end">' +
                '<li><button class="dropdown-item" type="button" id="btnCreateLogin" data-act="create-account" data-key="' + esc(key) + '"><i class="bi bi-person-plus me-2"></i>Create Login</button></li>' +
                '<li><button class="dropdown-item" type="button" id="btnLinkAccount" data-act="link-account" data-key="' + esc(key) + '"><i class="bi bi-link-45deg me-2"></i>Link Existing Account</button></li>';
            if (r.kkStatus === 'Active') {
                f += '<li><button class="dropdown-item" type="button" id="btnShowUsername" data-act="show-account-username" data-key="' + esc(key) + '"><i class="bi bi-eye me-2"></i>Show Username</button></li>' +
                    '<li><hr class="dropdown-divider"></li>' +
                    '<li><button class="dropdown-item text-danger" type="button" id="btnResetPassword" data-act="reset-account" data-key="' + esc(key) + '"><i class="bi bi-arrow-repeat me-2"></i>Reset Password</button></li>';
            }
            f += '</ul></div>';
        }
        f += '</div><button type="button" class="btn btn-outline-secondary kk-close-btn" data-bs-dismiss="modal">Close</button>';
        $('profileFooter').innerHTML = f;
        $('profileFooter').classList.add('kk-footer');
        modal('profileModal').show();
    }

    var memberArchiveTarget = null;
    function clearMemberArchiveErrors() {
        ['memberArchiveReason', 'memberArchiveOther'].forEach(function (id) {
            $(id).classList.remove('is-invalid');
        });
        $('memberArchiveReasonError').textContent = '';
        $('memberArchiveOtherError').textContent = '';
    }
    function memberArchiveSummaryField(label, value) {
        return '<div><div class="member-review-label">' + esc(label) + '</div><div class="member-review-value">' + esc(value || '\u2014') + '</div></div>';
    }
    function renderMemberArchiveSummary(record) {
        $('memberArchiveSummary').innerHTML =
            '<div class="member-review-head">' + avatarHtml(record, 62) +
            '<div><div class="member-review-name">' + esc(record.name) + '</div><span class="member-review-type">KK Member</span></div></div>' +
            '<div class="member-review-grid">' +
            memberArchiveSummaryField('Full name', record.name) +
            memberArchiveSummaryField('Member type', 'KK Member') +
            memberArchiveSummaryField('Original registration date', fmtDate(record.kk.createdAt)) +
            memberArchiveSummaryField('Barangay', record.kk.barangay || BARANGAY) +
            memberArchiveSummaryField('KK member number', record.code) +
            '</div>';
    }
    function openMemberArchive(key) {
        var record = getRec(key);
        if (!record || record.kkStatus === 'Archived' || !window.SK_CAN_MANAGE_ROLES) return;
        memberArchiveTarget = String(record.key);
        renderMemberArchiveSummary(record);
        $('memberArchiveReason').value = '';
        $('memberArchiveOther').value = '';
        $('memberArchiveRemarks').value = '';
        $('memberArchiveOtherWrap').classList.add('d-none');
        clearMemberArchiveErrors();
        modal('memberArchiveModal').show();
    }
    function saveMemberArchive() {
        var reasonSelect = $('memberArchiveReason');
        var other = $('memberArchiveOther').value.trim();
        var remarks = $('memberArchiveRemarks').value.trim();
        clearMemberArchiveErrors();
        if (!reasonSelect.value) {
            reasonSelect.classList.add('is-invalid');
            $('memberArchiveReasonError').textContent = 'Choose an archive reason.';
            return;
        }
        if (reasonSelect.value === 'Other' && (other.length < 2 || other.length > 200)) {
            $('memberArchiveOther').classList.add('is-invalid');
            $('memberArchiveOtherError').textContent = 'Specify the reason using 2 to 200 characters.';
            return;
        }
        if (remarks.length > 300) {
            notify('warning', 'Additional remarks must not exceed 300 characters.');
            return;
        }
        try {
            var members = JSON.parse(DB.getItem('kkMembers') || '[]');
            var requests = JSON.parse(DB.getItem('kkEditRequests') || '[]');
            if (!Array.isArray(members) || !Array.isArray(requests)) throw new Error('Member or request data is invalid. Reload the page and try again.');
            var member = members.filter(function (item) { return item && String(item.id) === memberArchiveTarget; })[0];
            if (!member) throw new Error('The KK member record was not found. Reload and try again.');
            if (member.status === 'Archived') throw new Error('This KK member is already archived.');

            var reason = reasonSelect.value === 'Other' ? 'Other: ' + other : reasonSelect.value;
            if (remarks) reason += ' \u2013 ' + remarks;
            var now = D.nowISO();
            var officer = window.SK_OFFICER || 'SK Officer';
            member.status = 'Archived';
            member.archiveReason = reason;
            member.archivedAt = now;
            member.archivedBy = officer;
            member.archiveType = 'manual';
            if (!Array.isArray(member.history)) member.history = [];
            member.history.push({ at: now, action: 'Archived', by: officer, note: reason });
            requests.forEach(function (request) {
                if (request && String(request.memberId) === memberArchiveTarget && request.status === 'Edit Request') {
                    request.status = 'Rejected';
                    request.reviewedAt = now;
                    request.reviewedBy = 'System';
                    request.remarks = 'Profile was archived.';
                }
            });

            var archivedName = D.kkName(member) || 'The member';
            DB.saveMemberData(members, requests);
            D.load();
            renderAll();
            memberArchiveTarget = null;
            modal('memberArchiveModal').hide();
            notify('success', archivedName + ' was archived.');
        } catch (error) {
            notify('danger', error.message || 'Could not archive the KK member.');
        }
    }
    $('memberArchiveReason').addEventListener('change', function () {
        clearMemberArchiveErrors();
        var isOther = this.value === 'Other';
        $('memberArchiveOtherWrap').classList.toggle('d-none', !isOther);
        if (isOther) $('memberArchiveOther').focus();
    });
    $('memberArchiveOther').addEventListener('input', function () {
        this.classList.remove('is-invalid');
        $('memberArchiveOtherError').textContent = '';
    });
    $('confirmMemberArchive').addEventListener('click', saveMemberArchive);
 
    var linkAccountTarget = null;
    function openAccountLink(legacyId) {
        var error = $('linkAccountError');
        error.textContent = '';
        $('linkAccountSelect').innerHTML = '<option value="">Loading accounts...</option>';
        try {
            var result = DB.listUnlinkedAccounts();
            if (result.linkedLegacyIds.map(String).indexOf(String(legacyId)) !== -1) {
                notify('warning', 'This member already has an account linked.');
                return;
            }
            var accounts = result.accounts.filter(function (account) { return Number(account.is_active) === 1; });
            if (!accounts.length) {
                notify('info', 'There are no active unlinked accounts to link.');
                return;
            }
            linkAccountTarget = legacyId;
            $('linkAccountSelect').innerHTML = '<option value="">Select an account</option>' + accounts.map(function (account) {
                return '<option value="' + esc(account.id) + '">' + esc(account.full_name) + ' (' + esc(account.username) + ' · ' + esc(account.role) + ')</option>';
            }).join('');
            modal('linkAccountModal').show();
        } catch (e) {
            notify('danger', e.message || 'Could not load unlinked accounts.');
        }
    }
 
    $('confirmLinkAccount').addEventListener('click', function () {
        var accountId = $('linkAccountSelect').value;
        if (!accountId || !linkAccountTarget) {
            $('linkAccountError').textContent = 'Select an unlinked account.';
            return;
        }
        try {
            DB.linkMemberAccount(accountId, linkAccountTarget);
            hideModal('linkAccountModal');
            notify('success', 'The existing account is now linked. Its username and password were not changed.');
            linkAccountTarget = null;
        } catch (e) {
            $('linkAccountError').textContent = e.message || 'Could not link the account.';
        }
    });
 
    var createAccountTarget = null;
    function openCreateAccount(legacyId) {
        $('createAccountError').textContent = '';
        $('temporaryPasswordBox').classList.add('d-none');
        $('temporaryPasswordBox').textContent = '';
        $('newAccountUsername').value = '';
        $('guardianConsent').checked = false;
        $('consentVersion').value = 'v1';
        try {
            var result = DB.listUnlinkedAccounts();
            if (result.linkedLegacyIds.map(String).indexOf(String(legacyId)) !== -1) {
                notify('warning', 'This member already has an account. Link or promote that account instead; creating another is not allowed.');
                return;
            }
            createAccountTarget = legacyId;
            modal('createAccountModal').show();
        } catch (e) {
            notify('danger', e.message || 'Could not check the member account.');
        }
    }
 
    function resetMemberAccount(legacyId) {
        var member = getRec(legacyId);
        if (!member || !window.SK_CAN_MANAGE_ROLES) return;
        if (!window.confirm('Reset the login password for ' + member.name + '? A new temporary password will be shown once, and the member must change it at next sign-in.')) return;
        try {
            var result = DB.resetMemberAccountByLegacyId(legacyId);
            window.alert('Temporary password (uppercase surname; show only once): ' + result.temporaryPassword +
                '\n\nShare it securely. The member must change it at next sign-in.');
        } catch (error) {
            notify('danger', error.message || 'Could not reset the member password.');
        }
    }
 
    function showMemberAccountUsername(legacyId) {
        var member = getRec(legacyId);
        if (!member || !window.SK_CAN_MANAGE_ROLES) return;
        if (!window.confirm('Only continue after verifying ' + member.name + '\'s identity through a trusted channel. Reveal their login username?')) return;
        try {
            var result = DB.getMemberUsernameByLegacyId(legacyId);
            window.alert('Login username: ' + result.username + '\n\nShare it privately with the verified member.');
        } catch (error) {
            notify('danger', error.message || 'Could not retrieve the member username.');
        }
    }
 
    $('confirmCreateAccount').addEventListener('click', function () {
        var username = $('newAccountUsername').value.trim();
        if (!createAccountTarget || !username) {
            $('createAccountError').textContent = 'Enter a username.';
            return;
        }
        try {
            var result = DB.createMemberAccount(
                createAccountTarget,
                username,
                $('guardianConsent').checked,
                $('consentVersion').value.trim()
            );
            $('temporaryPasswordBox').textContent = 'Temporary password (uppercase surname; show only once): ' + result.temporaryPassword +
                '. The member must change it at first sign-in.';
            $('temporaryPasswordBox').classList.remove('d-none');
            $('confirmCreateAccount').disabled = true;
            createAccountTarget = null;
        } catch (e) {
            $('createAccountError').textContent = e.message || 'Could not create the member account.';
        }
    });
    $('createAccountModal').addEventListener('hidden.bs.modal', function () {
        $('confirmCreateAccount').disabled = false;
        createAccountTarget = null;
    });
 
    function printHeader(title) {
        return '<div style="text-align:center;margin-bottom:12px;"><div style="font-weight:800;font-size:15px;">Sangguniang Kabataan \u2013 Barangay ' + BARANGAY + '</div>' +
            '<div>' + CITY + ', ' + PROVINCE + '</div><div style="font-weight:700;margin-top:6px;">' + esc(title) + '</div>' +
            '<div style="font-size:11px;">Printed ' + fmtDate(D.nowISO()) + '</div></div>';
    }
    function doPrint(html) { $('printArea').innerHTML = html; setTimeout(function () { window.print(); }, 150); }
    function printProfile(key) { if (window.SK_PUBLIC) return; var r = getRec(key); if (r) doPrint(printHeader(r.isSK ? 'SK Member Profile' : 'KK Member Profile') + profileHtml(r)); }
    function printList() {
        if (window.SK_PUBLIC) return;
        var list = filtered();
        if (!list.length) { notify('warning', 'There are no members to print.'); return; }
        var f = [];
        if ($('typeFilter').value !== 'ALL') f.push($('typeFilter').selectedOptions[0].text);
        ['positionFilter', 'statusFilter', 'purokFilter'].forEach(function (id) { if ($(id).value) f.push($(id).value); });
        if ($('searchInput').value.trim()) f.push('Search: ' + $('searchInput').value.trim());
        doPrint(printHeader('Member List') + '<div style="font-size:11px;margin-bottom:6px;">' + esc(f.join(' | ') || 'All KK members') + ' \u2013 ' + list.length + ' record(s)</div>' +
            '<table><thead><tr><th>#</th><th>Name</th><th>Age</th><th>Contact</th><th>Purok</th><th>KK Status</th><th>SK Position</th><th>Committee Assignments</th><th>SK Status</th><th>Election / Term</th></tr></thead><tbody>' +
            list.map(function (r, i) {
                return '<tr><td>' + (i + 1) + '</td><td>' + esc(r.name) + '</td><td>' + esc(r.age) + '</td><td>' + esc(r.contact) + '</td><td>' + esc(r.purok) + '</td><td>' + esc(r.kkStatus) + '</td><td>' +
                    esc(r.position) + '</td><td>' + esc(r.position === 'SK Kagawad' ? committeesOfRole(r.role).map(committeeLabel).join('; ') : '') + '</td><td>' +
                    esc(r.skStatus) + '</td><td>' + esc(r.isSK ? termLine(r) : '') + '</td></tr>';
            }).join('') + '</tbody></table>');
    }
    function exportCsv() {
        var list = filtered();
        if (!list.length) { notify('warning', 'There are no members to export.'); return; }
        var head = ['KK No.', 'Full Name', 'Age', 'Sex', 'Contact', 'Email', 'Purok', 'Address', 'KK Status', 'SK Position', 'Committee Assignments', 'SK Status', 'Election Year', 'Date Elected', 'Term Start', 'Term End'];
        var rows = list.map(function (r) {
            var o = r.role || {};
            return [r.code, r.name, r.age, r.kk.gender, r.contact, r.email, r.purok, r.address, r.kkStatus, r.position,
                r.position === 'SK Kagawad' ? committeesOfRole(r.role).map(committeeLabel).join('; ') : '',
                r.skStatus, o.electionYear, o.dateElected, o.termStart, o.termEnd];
        });
        var csv = [head].concat(rows).map(function (row) {
            return row.map(function (c) { c = c == null ? '' : String(c); return /[",\n]/.test(c) ? '"' + c.replace(/"/g, '""') + '"' : c; }).join(',');
        }).join('\r\n');
        var a = document.createElement('a');
        a.href = URL.createObjectURL(new Blob(['\ufeff' + csv], { type: 'text/csv;charset=utf-8;' }));
        a.download = 'member-list-' + todayISO() + '.csv';
        document.body.appendChild(a); a.click(); a.remove();
        setTimeout(function () { URL.revokeObjectURL(a.href); }, 1000);
        notify('success', 'Exported ' + list.length + ' member(s).');
    }
 
    /* ---------- confirm dialog ---------- */
    var cfCallback = null;
    function ask(o, cb) {
        $('cfTitle').textContent = o.title; $('cfText').textContent = o.text;
        $('cfOk').className = 'btn ' + (o.cls || 'btn-dark'); $('cfOk').textContent = o.ok || 'Confirm';
        cfCallback = cb; modal('confirmModal').show();
    }
    $('cfOk').addEventListener('click', function () {
        var cb = cfCallback; cfCallback = null;
        afterHide('confirmModal', function () { if (cb) cb(); });
    });
 
    /* ---------- assign / edit SK role ---------- */
    var roleKkId = null, roleEditing = false, respTouched = false, yearTouched = false;
 
    function pickable() {
        return D.kk.filter(function (m) { return m.status !== 'Archived' && !memberRoleOf(m.id); })
            .sort(function (a, b) { return norm(kkName(a)).localeCompare(norm(kkName(b))); });
    }
    function renderPick() {
        var q = norm($('pickSearch').value), sel = $('pickMember'), keep = sel.value;
        var list = pickable().filter(function (m) { return !q || norm([kkName(m), m.memberNo, m.purok].join(' ')).indexOf(q) > -1; });
        sel.innerHTML = list.length
            ? list.map(function (m) {
                var a = m.birthDate ? calcAge(m.birthDate) : '';
                return '<option value="' + esc(m.id) + '">' + esc(kkName(m)) + (a !== '' ? ' \u2022 ' + a + ' yrs' : '') + (m.purok ? ' \u2022 ' + m.purok : '') + (m.memberNo ? ' \u2022 ' + m.memberNo : '') + '</option>';
            }).join('')
            : '<option value="" disabled>' + (D.kk.length ? 'No matching KK member available' : 'No KK members registered yet') + '</option>';
        if (keep && list.some(function (m) { return m.id === keep; })) sel.value = keep;
    }
    function summaryHtml(m) {
        var r = { name: kkName(m), photo: m.photo || '' };
        var a = m.birthDate ? calcAge(m.birthDate) : '';
        return '<div class="d-flex align-items-center gap-3 p-3 border rounded-4 mb-1">' + avatarHtml(r, 56) +
            '<div class="small"><div class="member-name">' + esc(r.name) + '</div>' +
            '<div class="text-muted">' + esc([m.memberNo, a !== '' ? a + ' years old' : '', m.purok, m.mobile].filter(Boolean).join(' \u2022 ')) + '</div>' +
            '<div class="text-muted">Photo, contact and address come from the KK record.</div></div></div>';
    }
 
    function openRoleForm(kkId) {
        var f = $('roleForm');
        f.reset(); clearErrs(f);
        loadCommittees();
        fillSelect($('rolePosition'), POSITIONS, 'Select SK position');
        var role = kkId ? memberRoleOf(kkId) : null, m = kkId ? D.kkById(kkId) : null;
        roleKkId = kkId || null; roleEditing = !!role; respTouched = false; yearTouched = false;
        $('roleModalTitle').textContent = role ? 'Edit SK Role and Term' : 'Assign SK Role';
        $('roleSaveLabel').textContent = role ? 'Update SK Role' : 'Assign SK Role';
        $('pickBox').classList.toggle('d-none', !!m);
        $('memberSummary').classList.toggle('d-none', !m);
        $('memberSummary').innerHTML = m ? summaryHtml(m) : '';
        $('pickSearch').value = '';
        if (!m) { renderPick(); }
        if (role) {
            $('rolePosition').value = role.position; $('roleStatus').value = role.status === 'Archived' ? 'Inactive' : (role.status || 'Active');
            $('roleStatus').disabled = false;
            $('roleBasis').value = role.basis || '';
            var selectedCommittees = committeesOfRole(role);
            renderCommitteeChoices(selectedCommittees);
            var customCommittee = selectedCommittees.filter(function (value) { return value.indexOf('Other: ') === 0; })[0] || '';
            $('committeeOtherToggle').checked = !!customCommittee;
            $('committeeOther').value = customCommittee ? customCommittee.slice(7) : '';
            $('roleYear').value = role.electionYear || ''; $('roleElected').value = role.dateElected || '';
            $('roleStart').value = role.termStart || ''; $('roleEnd').value = role.termEnd || '';
            $('roleDesc').value = role.description || ''; $('roleResp').value = role.responsibilities || '';
            respTouched = !!role.responsibilities; yearTouched = !!role.electionYear;
        } else {
            $('roleStatus').disabled = false;
            $('roleBasis').value = '';
            renderCommitteeChoices([]);
            $('committeeOtherToggle').checked = false;
            $('committeeOther').value = '';
            $('roleYear').value = new Date().getFullYear();
        }
        updateCommitteeField();
        renderPosChoices();
        modal('roleModal').show();
    }
 
    function renderCommitteeChoices(selected) {
        $('roleCommitteeChoices').innerHTML = COMMITTEES.map(function (committee, index) {
            return '<label class="committee-option">' +
                '<input type="checkbox" class="form-check-input committee-choice" value="' + esc(committee) + '"' +
                (selected.indexOf(committee) !== -1 ? ' checked' : '') +
                ' id="committeeChoice' + index + '">' +
                '<span>' + esc(committee) + '</span></label>';
        }).join('');
    }
 
    function updateCommitteeField() {
        var isKagawad = $('rolePosition').value === 'SK Kagawad';
        $('kagawadCommitteeField').classList.toggle('d-none', !isKagawad);
        $('roleCommitteeChoices').querySelectorAll('input').forEach(function (input) { input.disabled = !isKagawad; });
        $('committeeOtherToggle').disabled = !isKagawad;
        $('committeeOther').disabled = !isKagawad;
        $('committeeOtherField').classList.toggle('d-none', !$('committeeOtherToggle').checked);
        if (!isKagawad) {
            $('roleCommitteeChoices').classList.remove('is-invalid');
            var feedback = $('kagawadCommitteeField').querySelector('.invalid-feedback');
            if (feedback) feedback.textContent = '';
        }
    }
    $('roleCommitteeChoices').addEventListener('change', function () {
        this.classList.remove('is-invalid');
        var feedback = $('kagawadCommitteeField').querySelector('.invalid-feedback');
        if (feedback) feedback.textContent = '';
    });
    $('committeeOtherToggle').addEventListener('change', updateCommitteeField);
    $('committeeOther').addEventListener('input', function () {
        this.classList.remove('is-invalid');
        var feedback = this.parentElement.querySelector('.invalid-feedback');
        if (feedback) feedback.textContent = '';
    });
 
    /* position choices (synced with the hidden select used by the form) */
    function renderPosChoices() {
        var cur = $('rolePosition').value, exceptId = null;
        if (roleKkId) { var ex = memberRoleOf(roleKkId); if (ex) exceptId = ex.id; }
        $('posChoices').innerHTML = POSITIONS.map(function (p) {
            var cap = D.SEATS[p] || 1;
            var used = occupiedSeats(p, exceptId);
            var left = Math.max(0, cap - used), full = (left === 0 && p !== cur) || (p === 'SK Vice Chairperson' && p !== cur);
            return '<button type="button" class="pos-choice' + (p === cur ? ' active' : '') + '" data-pos="' + esc(p) + '" role="radio" aria-checked="' + (p === cur) + '"' + (full ? ' disabled' : '') + '>' +
                '<b>' + esc(p.replace('SK ', '')) + '</b><small>' + (full ? 'Seat filled' : left + ' of ' + cap + ' seat' + (cap > 1 ? 's' : '') + ' open') + '</small></button>';
        }).join('');
    }
    $('posChoices').addEventListener('click', function (e) {
        var b = e.target.closest('.pos-choice');
        if (!b || b.disabled) return;
        $('rolePosition').value = b.dataset.pos;
        $('rolePosition').dispatchEvent(new Event('change'));
        $('rolePosition').classList.remove('is-invalid');
        var fb = $('rolePosition').parentElement.querySelector('.invalid-feedback'); if (fb) { fb.textContent = ''; fb.style.display = ''; }
        renderPosChoices();
    });
 
    $('pickSearch').addEventListener('input', renderPick);
    $('rolePosition').addEventListener('change', function () {
        updateCommitteeField();
        if (!respTouched || !$('roleResp').value.trim()) { $('roleResp').value = ROLES[this.value] || ''; respTouched = false; }
    });
    $('roleResp').addEventListener('input', function () { respTouched = true; });
    $('roleYear').addEventListener('input', function () { yearTouched = true; });
    $('roleElected').addEventListener('change', function () { if (this.value && !yearTouched) $('roleYear').value = yearOf(this.value); });
 
    $('roleForm').addEventListener('submit', function (e) {
        e.preventDefault();
        var form = this; clearErrs(form); reload();
        var ok = true;
        function bad(el, msg) { setErr(el, msg); ok = false; }
 
        var kkId = roleKkId || $('pickMember').value;
        if (!kkId) bad($('pickMember'), 'Select a KK member.');
        var m = kkId ? D.kkById(kkId) : null;
        if (kkId && !m) { notify('danger', 'This KK member no longer exists.'); modal('roleModal').hide(); renderAll(); return; }
        var existing = kkId ? memberRoleOf(kkId) : null;
        if (existing && !roleEditing) { notify('danger', kkName(m) + ' already has an SK position. Edit that role instead.'); modal('roleModal').hide(); renderAll(); return; }
        if (m && m.status === 'Archived' && !existing) bad($('pickMember'), 'This KK record is archived and cannot be assigned an SK role.');
 
        var pos = $('rolePosition').value, st = $('roleStatus').value, basis = $('roleBasis').value,
            committees = pos === 'SK Kagawad'
                ? Array.prototype.map.call($('roleCommitteeChoices').querySelectorAll('.committee-choice:checked'), function (input) { return input.value; })
                : [],
            customCommittee = $('committeeOtherToggle').checked ? $('committeeOther').value.trim() : '',
            yr = $('roleYear').value.trim(), el = $('roleElected').value, ts = $('roleStart').value, te = $('roleEnd').value;
        if (!pos) bad($('rolePosition'), 'Select an SK position.');
        if (pos === 'SK Kagawad' && $('committeeOtherToggle').checked && !customCommittee) {
            bad($('committeeOther'), 'Specify the other committee.');
        }
        if (customCommittee && !/^[^<>]{1,100}$/.test(customCommittee)) {
            bad($('committeeOther'), 'Enter a valid committee name (up to 100 characters).');
        }
        if (customCommittee) committees.push('Other: ' + customCommittee);
        if (pos === 'SK Kagawad' && !committees.length) {
            bad($('roleCommitteeChoices'), 'Select at least one committee.');
        }
        if (!existing && st !== 'Active') bad($('roleStatus'), 'A new SK role must start as Active.');
        if (!basis) bad($('roleBasis'), 'Select elected or appointed.');
        if (!yr) bad($('roleYear'), 'Election year is required.');
        else if (!/^\d{4}$/.test(yr) || +yr < 2000 || +yr > 2100) bad($('roleYear'), 'Enter a valid 4-digit year.');
        if (!el) bad($('roleElected'), 'Date elected is required.');
        if (el && ts && ts < el) bad($('roleStart'), 'Term cannot start before the date elected.');
        if (ts && te && te <= ts) bad($('roleEnd'), 'Term end must be after the term start.');
        if (yr && el && /^\d{4}$/.test(yr) && yearOf(el) !== yr) bad($('roleYear'), 'Election year should match the year of the date elected (' + yearOf(el) + ').');
        var ended = te && te < todayISO();
        var existingOccupiesSeat = existing && existing.position === pos && existing.status === 'Active'
            && (!existing.termStart || existing.termStart <= todayISO())
            && (!existing.termEnd || existing.termEnd >= todayISO());
        if (ok && st === 'Active' && !ended && !existingOccupiesSeat) {
            var err = positionSeatError(pos, existing ? existing.id : null);
            if (err) bad($('rolePosition'), err);
        }
        if (!ok) { var first = form.querySelector('.is-invalid'); if (first) first.focus(); return; }
 
        var expected = existing ? JSON.parse(JSON.stringify(existing)) : null;
        var desc = $('roleDesc').value.trim(), resp = $('roleResp').value.trim() || ROLES[pos] || '';
        var r;
        if (existing) {
            r = existing;
            var note = [];
            if (r.position !== pos) note.push('position changed from ' + r.position + ' to ' + pos);
            var oldCommittees = committeesOfRole(r);
            if (JSON.stringify(oldCommittees.slice().sort()) !== JSON.stringify(committees.slice().sort()))
                note.push('committee assignments updated');
            if (r.termStart !== ts || r.termEnd !== te) note.push('term set to ' + fmtDate(ts) + ' \u2013 ' + fmtDate(te));
            r.position = pos; r.committees = committees; r.committee = committees.length ? committees[0] : null;
            r.status = st; r.basis = basis; r.electionYear = yr; r.dateElected = el; r.termStart = ts; r.termEnd = te;
            r.description = desc; r.responsibilities = resp;
            D.addHist(r, 'Updated', note.join('; ') || 'SK role information updated.');
        } else {
            r = {
                id: D.uid('r'), kkId: kkId, position: pos, committees: committees,
                committee: committees.length ? committees[0] : null, status: 'Active', basis: basis, electionYear: yr, dateElected: el, termStart: ts, termEnd: te,
                description: desc, responsibilities: resp, history: [], createdAt: D.nowISO()
            };
            D.addHist(r, 'SK role assigned', pos + ', term ' + fmtDate(ts) + ' \u2013 ' + fmtDate(te) + '.');
            D.roles.push(r);
        }
        try {
            if (existing) DB.updateRole(r, expected);
            else DB.assignRole(r);
        } catch (err) {
            reload();
            notify('danger', err.message || 'Could not save the SK role.');
            return;
        }
        modal('roleModal').hide();
        var nm = kkName(m);
        notify('success', existing ? nm + '\'s SK role was updated.' : nm + ' is now an SK member (' + pos + ').' + (r.status === 'Archived' ? ' The term has already ended, so the role is archived.' : ''));
        if (!existing) { $('typeFilter').value = 'ALL'; $('positionFilter').value = ''; $('statusFilter').value = ''; $('purokFilter').value = ''; $('searchInput').value = ''; page = 1; }
        renderAll();
    });
 
    function removeRole(key) {
        var r = getRec(key);
        if (!r || !r.isSK) return;
        var expected = JSON.parse(JSON.stringify(r.role));
        ask({ title: 'End SK Role?', text: r.name + ' will stay in the KK Member List, but this SK role will end and the person will become a regular KK member unless another active role applies. Continue?', ok: 'End SK Role', cls: 'btn-danger' }, function () {
            try {
                DB.endRole(r.role, expected, false);
                reload();
                notify('success', r.name + '\'s SK role was ended.');
                renderAll();
            } catch (err) {
                reload();
                notify('danger', err.message || 'Could not end the SK role.');
            }
        });
    }
 
    function archiveRole(key) {
        var r = getRec(key);
        if (!r || !r.isSK) return;
        var expected = JSON.parse(JSON.stringify(r.role));
        ask({ title: 'Archive SK Role?', text: r.name + '\'s role history will be kept, and this role will no longer grant SK access. Continue?', ok: 'Archive SK Role', cls: 'btn-secondary' }, function () {
            try {
                DB.endRole(r.role, expected, true);
                reload();
                notify('success', r.name + '\'s SK role was archived.');
                renderAll();
            } catch (err) {
                reload();
                notify('danger', err.message || 'Could not archive the SK role.');
            }
        });
    }
 
    function memberIdData(record) {
        return {
            id: record.key,
            name: record.name,
            memberNo: record.code,
            purok: record.purok,
            photo: record.photo,
            status: record.kkStatus
        };
    }
 
    function printMemberId(key) {
        var record = getRec(key);
        if (!record || record.kkStatus !== 'Active') {
            notify('warning', 'Only active KK members can print an ID.');
            return;
        }
        KKId.printCard(memberIdData(record), 'automatic').then(function () {
            notify('success', 'ID print request recorded.');
            loadIdPrintHistory(record.key);
        }).catch(function (error) {
            notify('danger', error.message || 'Could not print the KK ID.');
        });
    }
 
    function logManualIdPrint(key) {
        var record = getRec(key);
        if (!record || record.kkStatus !== 'Active') {
            notify('warning', 'Only active KK members can have an ID print recorded.');
            return;
        }
        KKId.recordPrint(record.key, 'manual').then(function () {
            notify('success', 'Manual ID print recorded.');
            loadIdPrintHistory(record.key);
        }).catch(function (error) {
            notify('danger', error.message || 'Could not record the manual ID print.');
        });
    }
 
    function showScannedMember(member) {
        var box = $('kkIdScanResult');
        box.replaceChildren();
        var result = document.createElement('div');
        result.className = 'alert alert-success mb-0';
        var title = document.createElement('strong');
        title.textContent = 'Verified KK ID';
        var details = document.createElement('div');
        details.className = 'small mt-1';
        details.textContent = member.name + ' · ' + (member.memberNo || 'No member number') + ' · ' + member.status;
        result.appendChild(title);
        result.appendChild(details);
        var profileButton = document.createElement('button');
        profileButton.type = 'button';
        profileButton.className = 'btn btn-sm btn-outline-primary mt-2';
        profileButton.textContent = 'Open member profile';
        profileButton.addEventListener('click', function () {
            afterHide('kkIdScannerModal', function () { openProfile(member.id); });
        });
        result.appendChild(profileButton);
        box.appendChild(result);
        $('kkIdCameraStatus').textContent = 'KK ID verified.';
    }
 
    function verifyScannedMember(value) {
        $('verifyKkIdButton').disabled = true;
        $('kkIdScanResult').textContent = 'Verifying QR code...';
        KKId.verifyScan(value).then(showScannedMember).catch(function (error) {
            $('kkIdScanResult').textContent = error.message || 'Could not verify this KK ID.';
        }).finally(function () {
            $('verifyKkIdButton').disabled = false;
        });
    }
 
    /* ---------- events ---------- */
    document.addEventListener('click', function (e) {
        var b = e.target.closest('[data-act]');
        if (b) {
            var key = b.dataset.key, act = b.dataset.act;
            if (act === 'view') openProfile(key);
            else if (act === 'print') printProfile(key);
            else if (act === 'kkedit') window.location.href = 'registration.php?edit=' + encodeURIComponent(key);
            else if (act === 'archive-member') afterHide('profileModal', function () { openMemberArchive(key); });
            else if (act === 'role') afterHide('profileModal', function () { reload(); openRoleForm(key); });
            else if (act === 'remove') afterHide('profileModal', function () { removeRole(key); });
            else if (act === 'archive-role') afterHide('profileModal', function () { archiveRole(key); });
            else if (act === 'link-account') afterHide('profileModal', function () { openAccountLink(key); });
            else if (act === 'create-account') afterHide('profileModal', function () { openCreateAccount(key); });
            else if (act === 'reset-account') afterHide('profileModal', function () { resetMemberAccount(key); });
            else if (act === 'show-account-username') afterHide('profileModal', function () { showMemberAccountUsername(key); });
            else if (act === 'print-id') printMemberId(key);
            else if (act === 'log-manual-id') logManualIdPrint(key);
            return;
        }
        var ownProfileButton = e.target.closest('#membersOwnProfile');
        if (ownProfileButton) {
            var ownProfile = buildRecords().filter(function (record) {
                return String(record.key) === String(ownProfileButton.dataset.memberId);
            })[0];
            if (ownProfile) openProfile(ownProfile.key);
            else notify('warning', 'Your KK profile is not available in the Member List.');
            return;
        }
        if (e.target.closest('#scanKkIdBtn')) {
            $('kkIdScanValue').value = '';
            $('kkIdScanResult').textContent = '';
            $('kkIdCameraStatus').textContent = 'Camera scanning is optional. You can enter the QR value manually.';
            modal('kkIdScannerModal').show();
            return;
        }
        if (e.target.closest('#dismissUnlinked')) { D.write(D.KEYS.UNLINKED, []); renderUnlinked(); return; }
        var pg = e.target.closest('[data-page]');
        if (pg) { page = +pg.dataset.page; renderList(); $('results').scrollIntoView({ behavior: 'smooth', block: 'start' }); }
    });
    document.addEventListener('keydown', function (e) {
        if ((e.key === 'Enter' || e.key === ' ') && e.target.closest('[data-act="view"]')) {
            e.preventDefault();
            openProfile(e.target.closest('[data-act="view"]').dataset.key);
        }
    });
 
    $('kkIdScanForm').addEventListener('submit', function (event) {
        event.preventDefault();
        verifyScannedMember($('kkIdScanValue').value);
    });
    $('startKkIdCamera').addEventListener('click', function () {
        $('kkIdCameraStatus').textContent = 'Requesting camera access...';
        KKId.startScanner($('kkIdCamera'), function (token) {
            $('kkIdScanValue').value = 'KKID:' + token;
            verifyScannedMember($('kkIdScanValue').value);
        }, $('kkIdCameraStatus')).catch(function (error) {
            $('kkIdCameraStatus').textContent = error.message || 'Could not start the camera scanner.';
        });
    });
    $('kkIdScannerModal').addEventListener('hidden.bs.modal', function () {
        KKId.stopScanner($('kkIdCamera'));
    });
 
    function resetPage() { page = 1; renderList(); }
    $('searchInput').addEventListener('input', resetPage);
    ['positionFilter', 'statusFilter', 'purokFilter', 'sortSelect', 'typeFilter'].forEach(function (id) { $(id).addEventListener('change', resetPage); });
    document.querySelectorAll('[data-member-type]').forEach(function (tab) {
        tab.addEventListener('click', function () {
            $('typeFilter').value = tab.dataset.memberType;
            resetPage();
        });
    });
 
    function jumpTo(type) {
        $('typeFilter').value = type; page = 1; renderList();
        document.querySelector('.member-card').scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
    $('heroViewSk').addEventListener('click', function () { jumpTo('SK'); });
    $('heroViewKk').addEventListener('click', function () { jumpTo('KK'); });
    $('viewCards').addEventListener('click', function () { view = 'cards'; renderList(); });
    $('viewTable').addEventListener('click', function () { view = 'table'; renderList(); });
    $('printListBtn').addEventListener('click', printList);
    function assignSk() { reload(); openRoleForm(null); }
    $('heroAssign').addEventListener('click', assignSk);
    $('assignBtn').addEventListener('click', assignSk);
    $('exportBtn').addEventListener('click', exportCsv);
    $('resetBtn').addEventListener('click', function () {
        $('searchInput').value = ''; $('typeFilter').value = 'ALL'; $('positionFilter').value = ''; $('statusFilter').value = ''; $('purokFilter').value = ''; $('sortSelect').value = 'name-asc';
        resetPage();
    });
    $('termCheckBtn').addEventListener('click', function () {
        try {
            var result = DB.archiveExpiredRoles();
            reload();
            notify(result.archived ? 'info' : 'success', result.archived ? result.archived + ' expired SK role' + (result.archived > 1 ? 's were' : ' was') + ' archived.' : 'No SK terms have ended.');
            renderAll();
        } catch (err) {
            notify('danger', err.message || 'Could not archive expired SK roles.');
        }
    });
 
    function renderCommitteeList() {
        var savedChairs = {};
        try {
            savedChairs = JSON.parse(DB.getItem('skCommitteeChairs') || '{}');
            if (!savedChairs || typeof savedChairs !== 'object' || Array.isArray(savedChairs)) savedChairs = {};
        } catch (error) {
            notify('danger', 'Could not read committee chair assignments.');
        }
        var officials = D.roles.filter(function (role) {
            var today = todayISO();
            var member = D.kkById(role.kkId);
            return role.status === 'Active' && member && (member.status || 'Active') === 'Active'
                && (!role.termStart || role.termStart <= today)
                && (!role.termEnd || role.termEnd >= today);
        });
        $('committeeList').innerHTML = COMMITTEES.length
            ? COMMITTEES.map(function (name) {
                var options = '<option value="">Select an SK official</option>' + officials.map(function (role) {
                    var member = D.kkById(role.kkId);
                    var officialName = D.kkName(member) + ' — ' + role.position;
                    return '<option value="' + esc(role.id) + '"' + (String(savedChairs[name] || '') === String(role.id) ? ' selected' : '') + '>' + esc(officialName) + '</option>';
                }).join('');
                return '<div class="list-group-item d-flex justify-content-between align-items-center gap-2">' +
                    '<div class="flex-grow-1"><div class="fw-semibold">' + esc(name) + '</div>' +
                    '<label class="form-label small mt-2 mb-1" for="committee-chair-' + esc(name) + '">Chairperson</label>' +
                    '<select class="form-select form-select-sm" id="committee-chair-' + esc(name) + '" data-committee-chair="' + esc(name) + '">' + options + '</select></div>' +
                    (DEFAULT_COMMITTEES.indexOf(name) === -1
                        ? '<button type="button" class="btn btn-sm btn-outline-primary align-self-start" data-committee-edit="' + esc(name) + '">Rename</button>'
                        : '') + '</div>';
            }).join('')
            : '<div class="text-muted small">No committees are configured.</div>';
    }
    function resetCommitteeForm() {
        $('committeeForm').reset();
        $('committeeOriginal').value = '';
        $('committeeSaveBtn').textContent = 'Add Committee';
        $('committeeCancelEditBtn').classList.add('d-none');
        clearErrs($('committeeForm'));
    }
    function randomCommitteeChairAssignments(selects) {
        var assignments = {};
        selects.forEach(function (select) {
            var options = Array.prototype.slice.call(select.options).filter(function (option) {
                return option.value !== '';
            });
            if (!options.length) throw new Error('No active SK officials are available to assign as committee chairpersons.');
            var selected = options[Math.floor(Math.random() * options.length)];
            assignments[select.dataset.committeeChair] = selected.value;
        });
        if (!selects.length) throw new Error('No committees are available for chairperson assignment.');
        return assignments;
    }
    $('manageCommitteesBtn').addEventListener('click', function () {
        loadCommittees();
        renderCommitteeList();
        resetCommitteeForm();
        modal('committeeModal').show();
    });
    $('committeeList').addEventListener('click', function (event) {
        var button = event.target.closest('[data-committee-edit]');
        if (!button) return;
        $('committeeOriginal').value = button.dataset.committeeEdit;
        $('committeeName').value = button.dataset.committeeEdit;
        $('committeeSaveBtn').textContent = 'Save Changes';
        $('committeeCancelEditBtn').classList.remove('d-none');
        $('committeeName').focus();
    });
    $('committeeCancelEditBtn').addEventListener('click', resetCommitteeForm);
    $('saveCommitteeChairsBtn').addEventListener('click', function () {
        var assignments = {};
        $('committeeList').querySelectorAll('[data-committee-chair]').forEach(function (select) {
            assignments[select.dataset.committeeChair] = select.value || null;
        });
        try {
            DB.saveCommitteeChairs(assignments, COMMITTEES);
            notify('success', 'Committee chairpersons saved.');
        } catch (error) {
            notify('danger', error.message || 'Could not save committee chairpersons.');
        }
    });
    $('randomizeCommitteeChairsBtn').addEventListener('click', function () {
        var selects = Array.prototype.slice.call($('committeeList').querySelectorAll('[data-committee-chair]'));
        try {
            var assignments = randomCommitteeChairAssignments(selects);
            DB.saveCommitteeChairs(assignments, COMMITTEES);
            selects.forEach(function (select) {
                select.value = assignments[select.dataset.committeeChair];
            });
            notify('success', 'Random chairpersons assigned and saved.');
        } catch (error) {
            notify('danger', error.message || 'Could not randomly assign committee chairpersons.');
        }
    });
    $('committeeForm').addEventListener('submit', function (event) {
        event.preventDefault();
        var name = $('committeeName').value.trim(), original = $('committeeOriginal').value || null;
        clearErrs(this);
        if (name.length < 2) {
            setErr($('committeeName'), 'Enter a committee name of at least 2 characters.');
            return;
        }
        try {
            var result = DB.saveCommittee(name, original, COMMITTEES);
            COMMITTEES = result.committees;
            D.load();
            renderCommitteeList();
            renderAll();
            resetCommitteeForm();
            notify('success', original ? 'Committee updated.' : 'Committee added.');
        } catch (error) {
            notify('danger', error.message || 'Could not save the committee.');
        }
    });
 
    function refresh() { reload(); renderAll(); }
    window.addEventListener('storage', function (e) {
        if (e.key === D.KEYS.KK || e.key === D.KEYS.ROLE || e.key === null) { if (!isOpen('roleModal')) refresh(); }
    });
    document.addEventListener('visibilitychange', function () { if (!document.hidden && !isOpen('roleModal')) refresh(); });
    window.addEventListener('pageshow', function (e) { if (e.persisted) refresh(); });
 
    /* ---------- start ---------- */
    var qType = new URLSearchParams(location.search).get('type');
    if (qType && /^(SK|KK|ALL)$/i.test(qType)) $('typeFilter').value = qType.toUpperCase();
    renderList();
    setTimeout(function () { reload(); loading = false; renderAll(); }, 250);
})();
</script>
 
</body>
</html>
 