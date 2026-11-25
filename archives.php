<?php
require_once __DIR__ . '/config.php';
require_viewer();
$canRestoreMembers = !is_public() && can_write_app_data();
?>
<!DOCTYPE html>
<html lang="en"<?= is_public() ? ' class="is-public"' : '' ?>>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Archives | KK Youth Profiling System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" integrity="sha384-tViUnnbYAV00FLIhhi3v/dWt3Jxw4gZQcNoSCxCIFNJVCx7/D55/wXsrNIRANwdD" crossorigin="anonymous">
    <link href="sidebar.css?v=32" rel="stylesheet">
    <style>
        :root { --line:var(--bs-border-color); --muted:var(--bs-secondary-color); }
        * { box-sizing:border-box; }
        body { margin:0; background:var(--bs-tertiary-bg); color:var(--bs-body-color); font-family:Arial,sans-serif; }
        .top-strip { height:7px; background:var(--bs-primary); }
        .header { min-height:70px; background:#fff; border-bottom:1px solid var(--line); display:flex; align-items:center; justify-content:space-between; padding:0 26px; gap:14px; }
        .header-title { font-size:20px; font-weight:800; }
        .user-area { display:flex; align-items:center; gap:10px; }
        .avatar { width:38px; height:38px; border-radius:50%; background:var(--bs-primary); display:flex; align-items:center; justify-content:center; font-weight:800; }
        .content { padding:26px; max-width:1500px; margin:0 auto; }
        .hero { background:#fff; border:1px solid var(--line); border-radius:16px; padding:25px; margin-bottom:20px; }
        .hero h1 { font-weight:900; font-size:28px; margin:0 0 6px; }
        .hero p { color:var(--muted); margin:0; }
        .summary-card, .records-card { background:#fff; border:1px solid var(--line); border-radius:14px; }
        .summary-card { width:100%; padding:18px; height:100%; text-align:left; color:inherit; cursor:pointer; transition:border-color .15s ease, box-shadow .15s ease, transform .15s ease; }
        .summary-card:hover { transform:translateY(-2px); border-color:var(--bs-primary); }
        .summary-card:focus-visible { outline:3px solid var(--bs-primary); outline-offset:2px; }
        .summary-card.selected { border-color:var(--bs-primary); box-shadow:0 0 0 2px var(--bs-primary); }
        .summary-card .count { font-size:27px; font-weight:900; }
        .summary-card .label { color:var(--muted); font-size:14px; }
        .records-card { overflow:hidden; }
        .records-heading { padding:17px 20px; border-bottom:1px solid var(--line); font-weight:800; }
        .records-body { padding:18px 20px; }
        .table { font-size:14px; }
        .table th { white-space:nowrap; background:var(--bs-primary-bg-subtle); color:var(--bs-secondary-color); font-size:12px; text-transform:uppercase; }
        .table td, .table th { padding:12px; vertical-align:middle; }
        .review-modal .modal-content { border:0; border-radius:16px; overflow:hidden; }
        .review-modal .modal-header { background:#1855a0; color:#fff; }
        .review-modal .modal-header .btn-close { filter:invert(1); opacity:.85; }
        .member-review-card { padding:18px; border:1px solid #d2e2fb; border-radius:16px; background:#eff6ff; color:#243653; }
        .member-review-head { display:flex; align-items:center; gap:14px; padding-bottom:14px; border-bottom:1px solid #d5e3f8; }
        .member-review-avatar { display:grid; width:62px; height:62px; flex:none; place-items:center; border:3px solid #fff; border-radius:50%; background:#dceaff; color:#174f9d; box-shadow:0 1px 5px #174f9d33; font-size:1.15rem; font-weight:850; }
        .member-review-name { color:#202a39; font-weight:800; }
        .member-review-type { display:inline-block; margin-top:4px; padding:2px 9px; border-radius:999px; background:#1765c0; color:#fff; font-size:.72rem; font-weight:750; }
        .member-review-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:14px 22px; padding-top:14px; }
        .member-review-label { margin-bottom:2px; color:#657994; font-size:.68rem; font-weight:800; letter-spacing:.025em; text-transform:uppercase; }
        .member-review-value { overflow-wrap:anywhere; color:#243653; font-size:.9rem; font-weight:600; }
        .footer-note { text-align:center; color:var(--muted); font-size:13px; padding:24px; }
        @media(max-width:767px) {
            .content { padding:16px; }
            .header { padding:0 16px; }
            .records-body { padding:14px; }
            .member-review-grid { grid-template-columns:1fr; gap:10px; }
        }
    </style>
<?php include __DIR__ . '/init.php'; ?>
<?php
$archiveRequestItems = json_decode($__data['kkEditRequests'] ?? '[]', true);
$archiveRequestItems = is_array($archiveRequestItems)
    ? array_filter($archiveRequestItems, function ($request) {
        return is_array($request) && ($request['status'] ?? '') === 'Edit Request';
    })
    : [];
$archivePendingItems = json_decode($__data['kkPendingRegistrations'] ?? '[]', true);
$archivePendingItems = is_array($archivePendingItems)
    ? array_filter($archivePendingItems, function ($request) {
        return is_array($request) && ($request['status'] ?? '') === 'Pending';
    })
    : [];
$archiveAttentionCount = count($archiveRequestItems) + count($archivePendingItems);
$archiveMembers = json_decode($__data['kkMembers'] ?? '[]', true);
$archiveMembers = is_array($archiveMembers) ? $archiveMembers : [];
$archiveRoles = json_decode($__data['skRoles'] ?? '[]', true);
$archiveRoles = is_array($archiveRoles) ? $archiveRoles : [];
$archiveAnnouncements = json_decode($__data['skAnnouncements'] ?? '[]', true);
$archiveAnnouncements = is_array($archiveAnnouncements) ? $archiveAnnouncements : [];
$archiveMembersById = [];
$archiveRows = [];

foreach ($archiveMembers as $member) {
    if (!is_array($member) || empty($member['id'])) continue;
    $archiveMembersById[(string)$member['id']] = $member;
    if (($member['status'] ?? '') !== 'Archived') continue;
    $name = trim(implode(' ', array_filter([
        $member['firstName'] ?? '', $member['middleName'] ?? '',
        $member['lastName'] ?? '', $member['suffix'] ?? '',
    ], function ($part) { return trim((string)$part) !== ''; })));
    $archiveRows[] = [
        'type' => 'member',
        'memberId' => (string)$member['id'],
        'typeLabel' => 'KK member',
        'name' => $name !== '' ? $name : 'Unnamed member',
        'identifier' => (string)($member['memberNo'] ?? ''),
        'details' => (string)($member['archiveReason'] ?? 'Archived KK member'),
        'date' => (string)($member['archivedAt'] ?? ''),
        'source' => 'registration.php#archiveBody',
        'sourceLabel' => 'Member records',
        'search' => implode(' ', [$name, $member['memberNo'] ?? '', $member['archiveReason'] ?? '']),
    ];
}

foreach ($archiveRoles as $role) {
    if (!is_array($role) || !in_array(($role['status'] ?? ''), ['Archived', 'Inactive'], true)) continue;
    $memberId = (string)($role['kkId'] ?? '');
    $member = $archiveMembersById[$memberId] ?? [];
    $name = trim(implode(' ', array_filter([
        $member['firstName'] ?? '', $member['middleName'] ?? '',
        $member['lastName'] ?? '', $member['suffix'] ?? '',
    ], function ($part) { return trim((string)$part) !== ''; })));
    $history = isset($role['history']) && is_array($role['history']) ? array_reverse($role['history']) : [];
    $archivedHistory = null;
    foreach ($history as $historyEntry) {
        if (is_array($historyEntry) && preg_match('/archiv|ended/i', (string)($historyEntry['action'] ?? ''))) {
            $archivedHistory = $historyEntry;
            break;
        }
    }
    $position = (string)($role['position'] ?? 'SK role');
    $roleStatus = (string)$role['status'];
    $roleTypeLabel = $roleStatus === 'Archived' ? 'Archived SK role' : 'Ended / inactive SK role';
    $archiveRows[] = [
        'type' => 'role',
        'typeLabel' => $roleTypeLabel,
        'name' => $name !== '' ? $name : 'Member record unavailable',
        'identifier' => $position,
        'details' => (string)($archivedHistory['note'] ?? ($roleStatus === 'Archived' ? 'Archived SK role' : 'Inactive SK role')),
        'date' => (string)($archivedHistory['at'] ?? ''),
        'source' => 'members.php',
        'sourceLabel' => 'Member list',
        'search' => implode(' ', [$name, $member['memberNo'] ?? '', $position, $roleTypeLabel, $archivedHistory['note'] ?? '']),
    ];
}

foreach ($archiveAnnouncements as $announcement) {
    if (!is_array($announcement) || empty($announcement['cancelled'])) continue;
    $title = trim((string)($announcement['title'] ?? ''));
    if ($title === '') $title = 'Untitled announcement';
    $archiveRows[] = [
        'type' => 'announcement',
        'typeLabel' => 'Cancelled event / announcement',
        'name' => $title,
        'announcementId' => (string)($announcement['id'] ?? ''),
        'identifier' => (string)($announcement['date'] ?? ''),
        'details' => trim((string)($announcement['category'] ?? 'Cancelled')) ?: 'Cancelled',
        'date' => (string)($announcement['updatedAt'] ?? ''),
        'source' => 'announcements.php',
        'sourceLabel' => 'Announcements',
        'search' => implode(' ', array_filter([
            $title,
            $announcement['date'] ?? '',
            $announcement['category'] ?? '',
            $announcement['description'] ?? '',
            $announcement['details'] ?? '',
            $announcement['venue'] ?? '',
            $announcement['organizer'] ?? '',
            $announcement['contact'] ?? '',
            $announcement['notes'] ?? '',
            is_array($announcement['centers'] ?? null) ? implode(' ', array_filter($announcement['centers'], 'is_scalar')) : '',
            'cancelled',
        ], 'is_scalar')),
    ];
}

usort($archiveRows, function ($a, $b) {
    return strcmp($b['date'], $a['date']) ?: strcasecmp($a['typeLabel'], $b['typeLabel']) ?: strcasecmp($a['name'], $b['name']);
});
$archiveCounts = ['member' => 0, 'role' => 0, 'announcement' => 0];
foreach ($archiveRows as $archiveRow) $archiveCounts[$archiveRow['type']]++;
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
            <div><div class="header-title">Archives</div><small>KK Youth Profiling System</small></div>
        </div>
        <div class="header-account-area">
            <a href="<?= is_public() ? 'announcements.php' : 'request.php' ?>" class="header-notification" aria-label="<?= is_public() ? 'View announcements' : (int)$archiveAttentionCount . ' requests need review' ?>">
                <i class="bi bi-bell" aria-hidden="true"></i>
                <?php if (!is_public() && $archiveAttentionCount > 0): ?><span class="header-notification-count"><?= (int)$archiveAttentionCount ?></span><?php endif; ?>
            </a>
            <div class="header-avatar"><?= e(uinit()) ?></div>
            <div class="d-none d-md-block header-identity">
                <div class="header-identity-name"><?= e(uname()) ?></div>
                <div class="header-identity-role"><?= e(strtoupper(urole())) ?></div>
            </div>
            <a href="<?= is_public() ? 'login.php' : 'logout.php' ?>" class="btn btn-sm header-logout">
                <i class="bi bi-box-arrow-right me-1"></i>
                <?= is_public() ? 'Official Login' : 'Logout' ?>
            </a>
        </div>
    </header>
    <div class="content">
        <section class="hero">
            <h1><i class="bi bi-archive me-2"></i>Archives</h1>
            <p>Review archived KK members and SK roles, plus cancelled events and announcements. Unarchive eligible KK members here, or use each record’s source page for other management.</p>
        </section>

        <div class="row g-3 mb-4">
            <div class="col-sm-6 col-lg-3"><button type="button" class="summary-card records-filter selected" data-filter="" aria-pressed="true"><div class="count"><?= count($archiveRows) ?></div><div class="label">Archived or cancelled records</div></button></div>
            <div class="col-sm-6 col-lg-3"><button type="button" class="summary-card records-filter" data-filter="member" aria-pressed="false"><div class="count"><?= $archiveCounts['member'] ?></div><div class="label">Archived KK members</div></button></div>
            <div class="col-sm-6 col-lg-3"><button type="button" class="summary-card records-filter" data-filter="role" aria-pressed="false"><div class="count"><?= $archiveCounts['role'] ?></div><div class="label">Ended / archived SK roles</div></button></div>
            <div class="col-sm-6 col-lg-3"><button type="button" class="summary-card records-filter" data-filter="announcement" aria-pressed="false"><div class="count"><?= $archiveCounts['announcement'] ?></div><div class="label">Cancelled events / announcements</div></button></div>
        </div>

        <section class="records-card">
            <div class="records-heading"><i class="bi bi-clock-history me-2"></i>Archived records</div>
            <div class="records-body">
                <div class="row g-2 mb-3">
                    <div class="col-md-7"><label class="visually-hidden" for="archiveSearch">Search archives</label><input id="archiveSearch" class="form-control" type="search" placeholder="Search name, ID, position, or reason"></div>
                    <div class="col-md-5"><label class="visually-hidden" for="archiveType">Filter archive type</label>
                        <select id="archiveType" class="form-select">
                            <option value="">All archived record types</option>
                            <option value="member">KK members</option>
                            <option value="role">SK roles</option>
                            <option value="announcement">Cancelled events / announcements</option>
                        </select>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead><tr><th>Type</th><th>Record</th><th>Archived / cancelled</th><th>Reason / details</th><th>Manage</th></tr></thead>
                        <tbody id="archiveRows">
                        <?php if (!$archiveRows): ?>
                            <tr id="archiveEmpty"><td colspan="5" class="text-center text-muted py-5">There are no archived or cancelled records.</td></tr>
                        <?php else: foreach ($archiveRows as $row): ?>
                            <tr data-type="<?= e($row['type']) ?>" data-search="<?= e($row['search']) ?>">
                                <td><span class="badge text-bg-secondary"><?= e($row['typeLabel']) ?></span></td>
                                <td><strong><?= e($row['name']) ?></strong><?php if ($row['identifier'] !== ''): ?><div class="small text-muted"><?= e($row['identifier']) ?></div><?php endif; ?></td>
                                <td><?= e($row['date'] !== '' ? substr($row['date'], 0, 10) : 'Date not recorded') ?></td>
                                <td><?= e($row['details']) ?></td>
                                <td class="text-nowrap">
                                    <?php if ($row['type'] === 'member' && $canRestoreMembers): ?>
                                        <button class="btn btn-sm btn-outline-success" type="button" data-restore-member="<?= e($row['memberId']) ?>"><i class="bi bi-arrow-counterclockwise me-1"></i>Unarchive</button>
                                    <?php endif; ?>
                                    <?php if ($row['type'] === 'announcement' && !empty($row['announcementId'])): ?>
                                        <button class="btn btn-sm btn-outline-primary" type="button" data-archive-announcement="<?= e($row['announcementId']) ?>">View details</button>
                                    <?php endif; ?>
                                    <a class="btn btn-sm btn-outline-primary" href="<?= e($row['source']) ?>"><?= e($row['sourceLabel']) ?> <i class="bi bi-arrow-up-right"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; endif; ?>
                            <tr id="archiveNoMatches" hidden><td colspan="5" class="text-center text-muted py-4">No matching records.</td></tr>
                        </tbody>
                    </table>
                </div>
                <p class="small text-muted mt-3 mb-0">Only record types with an existing archived or cancelled state are listed here. Records such as sign-ups, attendance, and reports are not archived by the application.</p>
            </div>
        </section>
        <div class="footer-note">KK Youth Profiling System &bull; SK Barangay Sikat, Alfonso, Cavite &bull; FY 2026&ndash;2027</div>
    </div>
</main>
<div class="modal fade" id="archiveAnnouncementModal" tabindex="-1" aria-labelledby="archiveAnnouncementTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title h5 fw-bold" id="archiveAnnouncementTitle">Cancelled announcement details</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="archiveAnnouncementDetails"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<?php if ($canRestoreMembers): ?>
<div class="modal fade" id="restoreMemberModal" tabindex="-1" aria-labelledby="restoreMemberTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content review-modal">
            <div class="modal-header">
                <h2 class="modal-title h5 fw-bold" id="restoreMemberTitle"><i class="bi bi-arrow-counterclockwise me-2"></i>Restore archived record</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="restoreMemberSummary" class="member-review-card mb-4"></div>
                <div class="mb-3">
                    <label class="form-label fw-semibold" for="restoreReason">Reason for Restoration <span class="text-danger">*</span></label>
                    <select class="form-select" id="restoreReason" required>
                        <option value="">Select a reason</option>
                        <option>Returned to Active Membership</option>
                        <option>Incorrectly Archived</option>
                        <option>Member Record Correction</option>
                        <option>Other</option>
                    </select>
                    <div class="invalid-feedback" id="restoreReasonError"></div>
                </div>
                <div class="mb-3 d-none" id="restoreOtherWrap">
                    <label class="form-label" for="restoreOther">Please specify <span class="text-danger">*</span></label>
                    <input class="form-control" id="restoreOther" type="text" maxlength="300" autocomplete="off">
                    <div class="form-text">Enter at least 10 characters for the restoration record.</div>
                    <div class="invalid-feedback" id="restoreOtherError"></div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold" for="restoreRemarks">Additional remarks <span class="text-muted fw-normal">(optional)</span></label>
                    <textarea class="form-control" id="restoreRemarks" rows="2" maxlength="150"></textarea>
                </div>
                <div class="alert alert-danger py-2 small d-none" id="restoreMemberError" role="alert"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="confirmRestoreMember"><i class="bi bi-arrow-counterclockwise me-1"></i>Unarchive member</button>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>
<script>
(function () {
    var search = document.getElementById('archiveSearch');
    var type = document.getElementById('archiveType');
    var filterCards = Array.prototype.slice.call(document.querySelectorAll('.records-filter'));
    var rows = Array.prototype.slice.call(document.querySelectorAll('#archiveRows tr[data-type]'));
    var noMatches = document.getElementById('archiveNoMatches');
    var restoreTarget = null;
    var canRestore = <?= $canRestoreMembers ? 'true' : 'false' ?>;
    var announcementRecords = [];
    try {
        var storedAnnouncements = JSON.parse(window.__DB.skAnnouncements || '[]');
        if (Array.isArray(storedAnnouncements)) announcementRecords = storedAnnouncements;
    } catch (error) {
        console.error('Could not read archived announcement details.', error);
    }

    function showAnnouncementDetails(id) {
        var announcement = announcementRecords.filter(function (record) {
            return record && String(record.id) === String(id) && record.cancelled;
        })[0];
        if (!announcement) return;

        var details = document.getElementById('archiveAnnouncementDetails');
        details.replaceChildren();
        var banner = announcement.banner || announcement.poster || '';
        if (/^data:image\/(?:png|jpe?g|webp);base64,[A-Za-z0-9+/=]+$/i.test(banner)) {
            var image = document.createElement('img');
            image.src = banner;
            image.alt = 'Banner for ' + String(announcement.title || 'cancelled announcement');
            image.className = 'img-fluid rounded mb-3';
            details.appendChild(image);
        }

        var heading = document.createElement('h3');
        heading.className = 'h5 fw-bold';
        heading.textContent = String(announcement.title || 'Untitled announcement');
        details.appendChild(heading);

        var fields = [
            ['Status', 'Cancelled'],
            ['Category', announcement.category],
            ['Centers of participation', Array.isArray(announcement.centers) ? announcement.centers.join(', ') : ''],
            ['Event date', announcement.date],
            ['Start time', announcement.startTime],
            ['End time', announcement.endTime],
            ['Venue', announcement.venue],
            ['Organizer', announcement.organizer],
            ['Contact', announcement.contact],
            ['Description', announcement.description],
            ['Details', announcement.details],
            ['Additional notes', announcement.notes],
            ['Publication status', announcement.publishState],
            ['Publication date', announcement.publishDate],
            ['Last updated', announcement.updatedAt],
        ];
        var list = document.createElement('dl');
        list.className = 'row mb-0';
        fields.forEach(function (field) {
            if (field[1] == null || field[1] === '') return;
            var label = document.createElement('dt');
            label.className = 'col-sm-4';
            label.textContent = field[0];
            var value = document.createElement('dd');
            value.className = 'col-sm-8';
            value.style.whiteSpace = 'pre-wrap';
            value.textContent = String(field[1]);
            list.appendChild(label);
            list.appendChild(value);
        });
        details.appendChild(list);
        bootstrap.Modal.getOrCreateInstance(document.getElementById('archiveAnnouncementModal')).show();
    }

    function clearRestoreErrors() {
        ['restoreReason', 'restoreOther'].forEach(function (id) {
            var input = document.getElementById(id);
            if (input) input.classList.remove('is-invalid');
        });
        ['restoreReasonError', 'restoreOtherError'].forEach(function (id) {
            var message = document.getElementById(id);
            if (message) message.textContent = '';
        });
        document.getElementById('restoreMemberError').classList.add('d-none');
        document.getElementById('restoreMemberError').textContent = '';
    }

    function renderRestoreMemberSummary(member) {
        var target = document.getElementById('restoreMemberSummary');
        var name = [member.firstName, member.middleName, member.lastName, member.suffix].filter(Boolean).join(' ') || 'Unnamed member';
        var initialsParts = name.trim().split(/\s+/);
        var initials = (initialsParts[0].charAt(0) + (initialsParts.length > 1 ? initialsParts[initialsParts.length - 1].charAt(0) : '')).toUpperCase();
        target.replaceChildren();

        var header = document.createElement('div');
        header.className = 'member-review-head';
        var avatar = document.createElement('div');
        avatar.className = 'member-review-avatar';
        avatar.textContent = initials;
        var identity = document.createElement('div');
        var nameLabel = document.createElement('div');
        nameLabel.className = 'member-review-name';
        nameLabel.textContent = name;
        var type = document.createElement('span');
        type.className = 'member-review-type';
        type.textContent = 'KK Member';
        identity.appendChild(nameLabel);
        identity.appendChild(type);
        header.appendChild(avatar);
        header.appendChild(identity);
        target.appendChild(header);

        var grid = document.createElement('div');
        grid.className = 'member-review-grid';
        [
            ['Full name', name],
            ['Member type', 'KK Member'],
            ['Original registration date', member.createdAt || 'Not recorded'],
            ['Archive date', member.archivedAt || 'Not recorded'],
            ['Archive reason', member.archiveReason || 'Not recorded'],
        ].forEach(function (field) {
            var item = document.createElement('div');
            var label = document.createElement('div');
            label.className = 'member-review-label';
            label.textContent = field[0];
            var value = document.createElement('div');
            value.className = 'member-review-value';
            value.textContent = String(field[1]);
            item.appendChild(label);
            item.appendChild(value);
            grid.appendChild(item);
        });
        target.appendChild(grid);
    }

    function openRestoreMember(id) {
        if (!canRestore) return;
        var records = [];
        try {
            records = JSON.parse(window.__DB.kkMembers || '[]');
        } catch (error) {
            console.error('Could not read archived KK member data.', error);
            window.alert('Could not read archived member data. Reload Archives and try again.');
            return;
        }
        restoreTarget = records.filter(function (member) {
            return member && String(member.id) === String(id) && member.status === 'Archived';
        })[0] || null;
        if (!restoreTarget) {
            window.alert('This archived member could not be found. Reload Archives and try again.');
            return;
        }
        renderRestoreMemberSummary(restoreTarget);
        document.getElementById('restoreReason').value = '';
        document.getElementById('restoreOther').value = '';
        document.getElementById('restoreRemarks').value = '';
        document.getElementById('restoreOtherWrap').classList.add('d-none');
        clearRestoreErrors();
        bootstrap.Modal.getOrCreateInstance(document.getElementById('restoreMemberModal')).show();
    }

    function confirmRestoreMember() {
        clearRestoreErrors();
        if (!restoreTarget) return;
        var reasonSelect = document.getElementById('restoreReason');
        var otherInput = document.getElementById('restoreOther');
        var remarks = document.getElementById('restoreRemarks').value.trim();
        if (!reasonSelect.value) {
            reasonSelect.classList.add('is-invalid');
            document.getElementById('restoreReasonError').textContent = 'Choose a restoration reason.';
            return;
        }
        var reason = reasonSelect.value;
        if (reason === 'Other') {
            var detail = otherInput.value.trim();
            if (detail.length < 10 || detail.length > 300) {
                otherInput.classList.add('is-invalid');
                document.getElementById('restoreOtherError').textContent = 'Specify the reason using 10 to 300 characters.';
                return;
            }
            reason = 'Other: ' + detail;
        }
        if (remarks.length > 150) {
            document.getElementById('restoreMemberError').textContent = 'Additional remarks must not exceed 150 characters.';
            document.getElementById('restoreMemberError').classList.remove('d-none');
            return;
        }
        if (remarks) reason += ' \u2013 Additional remarks: ' + remarks;
        try {
            DB.restoreMember(restoreTarget, String(restoreTarget.birthDate || ''), reason);
            window.location.reload();
        } catch (error) {
            var errorBox = document.getElementById('restoreMemberError');
            errorBox.textContent = error.message || 'Could not unarchive the KK member.';
            errorBox.classList.remove('d-none');
        }
    }

    document.addEventListener('click', function (event) {
        var button = event.target.closest('[data-archive-announcement]');
        if (button) showAnnouncementDetails(button.dataset.archiveAnnouncement);
        var restoreButton = event.target.closest('[data-restore-member]');
        if (restoreButton) openRestoreMember(restoreButton.dataset.restoreMember);
    });
    if (canRestore) {
        document.getElementById('restoreReason').addEventListener('change', function () {
            clearRestoreErrors();
            var isOther = this.value === 'Other';
            document.getElementById('restoreOtherWrap').classList.toggle('d-none', !isOther);
            if (isOther) document.getElementById('restoreOther').focus();
        });
        document.getElementById('restoreOther').addEventListener('input', function () {
            this.classList.remove('is-invalid');
            document.getElementById('restoreOtherError').textContent = '';
        });
        document.getElementById('confirmRestoreMember').addEventListener('click', confirmRestoreMember);
    }

    function filter() {
        var query = search.value.trim().toLowerCase();
        var selectedType = type.value;
        var visible = 0;
        rows.forEach(function (row) {
            var matches = (!selectedType || row.dataset.type === selectedType) &&
                (!query || (row.dataset.search + ' ' + row.innerText).toLowerCase().includes(query));
            row.hidden = !matches;
            if (matches) visible++;
        });
        noMatches.hidden = visible > 0 || rows.length === 0;
    }
    search.addEventListener('input', filter);
    type.addEventListener('change', function () {
        filterCards.forEach(function (card) {
            var selected = card.dataset.filter === type.value;
            card.classList.toggle('selected', selected);
            card.setAttribute('aria-pressed', selected ? 'true' : 'false');
        });
        filter();
    });
    filterCards.forEach(function (card) {
        card.addEventListener('click', function () {
            type.value = card.dataset.filter;
            search.value = '';
            filterCards.forEach(function (item) {
                var selected = item === card;
                item.classList.toggle('selected', selected);
                item.setAttribute('aria-pressed', selected ? 'true' : 'false');
            });
            filter();
        });
    });
})();
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
