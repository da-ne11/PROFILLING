(function () {
    'use strict';

    var $ = function (id) { return document.getElementById(id); };
    var OFFICER = window.SK_OFFICER || 'SK Officer';
    var CAN_REVIEW_MEMBER_REQUESTS = window.SK_CAN_REVIEW_MEMBER_REQUESTS === true;
    var MAX_AGE = 30;
    var FIELD_LIST = [
        ['firstName', 'First Name'], ['middleName', 'Middle Name'], ['lastName', 'Last Name'], ['suffix', 'Suffix'],
        ['birthDate', 'Date of Birth'], ['gender', 'Gender'], ['genderOther', 'Gender (self-described)'],
        ['civilStatus', 'Civil Status'], ['religion', 'Religion'], ['religionOther', 'Religion (specified)'],
        ['houseNo', 'House Number'], ['street', 'Street'], ['purok', 'Purok / Sitio'],
        ['mobile', 'Mobile Number'], ['email', 'Email Address'], ['facebook', 'Facebook Account'],
        ['classification', 'Youth Classification'], ['classificationOther', 'Classification (specified)'],
        ['educationLevel', 'Educational Attainment'], ['educationOther', 'Educational Attainment (specified)'],
        ['educationStatus', 'Education Status'], ['educationStatusOther', 'Education Status (specified)'],
        ['school', 'School / Institution'], ['schoolOther', 'School / Institution (specified)'],
        ['employment', 'Employment Status'], ['employmentOther', 'Employment Status (specified)'],
        ['salaryIncome', 'Monthly Salary / Income (₱)'], ['occupation', 'Occupation / Job'],
        ['occupationOther', 'Occupation (specified)'], ['training', 'Training / Skills Program'],
        ['trainingOther', 'Training (specified)'], ['pwd', 'PWD'], ['disabilityType', 'Disability Type'],
        ['disabilityOther', 'Disability (specified)'], ['skVoter', 'Registered SK Voter'],
        ['voterNo', 'Precinct Voter Number'], ['interests', 'Program Interests'],
        ['interestOther', 'Program Interest (specified)'], ['notes', 'Additional Notes'], ['photo', 'Profile Picture']
    ];
    var FIELD_LABELS = {};
    FIELD_LIST.forEach(function (field) { FIELD_LABELS[field[0]] = field[1]; });
    var members = [], editRequests = [];
    var requestStatusFilter = 'pending', requestDatePreset = 'all', currentEditRequestId = null;
    var selectedRequestIds = new Set();
    var editModal = bootstrap.Modal.getOrCreateInstance($('reviewModal'));

    function esc(value) {
        return String(value == null ? '' : value).replace(/[&<>"']/g, function (character) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[character];
        });
    }
    function fullName(record) {
        return [record.firstName, record.middleName, record.lastName, record.suffix].filter(Boolean).join(' ') || 'Unnamed applicant';
    }
    function nowISO() { return new Date().toISOString(); }
    function fmtDate(value) {
        if (!value) return '—';
        var date = new Date(String(value).length === 10 ? value + 'T00:00:00' : value);
        return isNaN(date) ? '—' : date.toLocaleDateString('en-PH', { year: 'numeric', month: 'short', day: 'numeric' });
    }
    function calcAge(value) {
        var birth = new Date(value + 'T00:00:00');
        if (isNaN(birth)) return NaN;
        var today = new Date();
        var age = today.getFullYear() - birth.getFullYear();
        if (today.getMonth() < birth.getMonth() ||
            (today.getMonth() === birth.getMonth() && today.getDate() < birth.getDate())) age--;
        return age;
    }
    function readList(key) {
        var raw = DB.getItem(key);
        if (raw == null || raw === '') return [];
        var value = JSON.parse(raw);
        if (!Array.isArray(value)) throw new Error('The ' + key + ' data is not a list.');
        return value;
    }
    function loadData() {
        members = readList('kkMembers');
        editRequests = readList('kkEditRequests');
    }
    function notify(type, message) {
        var alert = document.createElement('div');
        alert.className = 'alert alert-' + type + ' shadow small mb-2';
        alert.textContent = message;
        $('toastBox').appendChild(alert);
        window.setTimeout(function () { alert.remove(); }, 5000);
    }
    function setList(key, list) {
        DB.setItem(key, JSON.stringify(list));
    }
    function findMember(id) {
        return members.filter(function (member) { return String(member.id) === String(id); })[0] || null;
    }
    function requestState(item) {
        if (item.status === 'Edit Request' || item.status === 'Pending') return 'pending';
        return String(item.status || '').toLowerCase();
    }
    function requestDate(item) {
        return item.requestedAt ? String(item.requestedAt).slice(0, 10) : '';
    }
    function matchesStatus(item) {
        var status = requestState(item);
        return requestStatusFilter === 'all' || (requestStatusFilter === 'done'
            ? status === 'approved' || status === 'rejected'
            : status === requestStatusFilter);
    }
    function matchesDate(item) {
        var from = $('requestDateFrom').value, to = $('requestDateTo').value;
        if (from && to && from > to) return false;
        var date = requestDate(item);
        return !!date && (!from || date >= from) && (!to || date <= to);
    }
    function dateText(value) {
        return value ? fmtDate(value) : '—';
    }
    function statusBadge(status) {
        var css = status === 'Approved' ? 'success' : status === 'Rejected' ? 'danger' : 'warning text-dark';
        return '<span class="badge bg-' + css + '">' + esc(status || 'Pending') + '</span>';
    }
    function button(action, id, label, css) {
        return '<button type="button" class="btn btn-sm ' + css + '" data-act="' + action + '" data-id="' + esc(id) + '">' + label + '</button>';
    }
    function checkButton(item) {
        var member = findMember(item.memberId);
        var pending = requestState(item) === 'pending';
        var eligible = !!member && member.status !== 'Archived';
        var selected = selectedRequestIds.has(String(item.id));
        return '<button type="button" class="btn rq-check-btn' + (selected ? ' is-selected' : '') + '" data-select-request="' + esc(item.id) + '" aria-label="' + (selected ? 'Deselect' : 'Select') + ' ' + esc(item.memberName || 'request') + '" ' + (!pending || !eligible || !CAN_REVIEW_MEMBER_REQUESTS ? 'disabled' : '') + '><i class="bi ' + (selected ? 'bi-check-lg' : 'bi-check') + '" aria-hidden="true"></i></button>';
    }
    function changeValue(key, value) {
        if (key === 'photo') return value ? 'Photo updated' : 'No photo';
        if (Array.isArray(value)) return value.length ? value.join(', ') : '—';
        if (value == null || value === '') return '—';
        return typeof value === 'object' ? JSON.stringify(value) : String(value);
    }
    function changePreview(request) {
        var changes = request.changes && typeof request.changes === 'object' ? request.changes : {};
        return Object.keys(changes).map(function (key) {
            var label = FIELD_LABELS[key] || key;
            var oldValue = changeValue(key, request.previous && request.previous[key]);
            var newValue = changeValue(key, changes[key]);
            return '<div class="rq-change-preview"><strong>' + esc(label) + ':</strong> ' +
                '<span class="rq-change-old">' + esc(oldValue) + '</span> ' +
                '<i class="bi bi-arrow-right" aria-hidden="true"></i> ' +
                '<span class="rq-change-new">' + esc(newValue) + '</span></div>';
        }).join('') || '<span class="text-muted">No field changes</span>';
    }
    function updateDashboard() {
        var counts = { pending: 0, approved: 0, rejected: 0, all: 0 };
        editRequests.forEach(function (item) {
            if (matchesDate(item)) counts[requestState(item)] = (counts[requestState(item)] || 0) + 1;
        });
        counts.all = counts.pending + counts.approved + counts.rejected;
        $('pendingRequestTotal').textContent = counts.pending;
        $('approvedRequestTotal').textContent = counts.approved;
        $('rejectedRequestTotal').textContent = counts.rejected;
        $('allRequestTotal').textContent = counts.all;
        var from = $('requestDateFrom').value, to = $('requestDateTo').value;
        $('requestDateLabel').textContent = from && to && from > to
            ? 'Invalid date range: the start date must be before the end date.'
            : (from || to ? 'Showing requests' + (from ? ' from ' + fmtDate(from) : '') + (to ? ' to ' + fmtDate(to) : '') : 'Showing requests from all dates');
        document.querySelectorAll('[data-request-status]').forEach(function (card) {
            card.classList.toggle('active', card.dataset.requestStatus === requestStatusFilter);
        });
        document.querySelectorAll('[data-request-range]').forEach(function (buttonEl) {
            buttonEl.classList.toggle('active', buttonEl.dataset.requestRange === requestDatePreset);
        });
    }
    function updatePendingTotal(editCount) {
        $('requestTotalCount').textContent = editCount + ' pending';
        var acceptAll = $('acceptAllRequests');
        if (acceptAll) {
            var selectedCount = editRequests.filter(function (item) {
                var member = findMember(item.memberId);
                return requestState(item) === 'pending' && selectedRequestIds.has(String(item.id)) &&
                    member && member.status !== 'Archived';
            }).length;
            acceptAll.textContent = selectedCount ? 'Accept selected (' + selectedCount + ')' : 'Accept all (0)';
            acceptAll.disabled = selectedCount === 0 || !CAN_REVIEW_MEMBER_REQUESTS;
        }
        var badge = document.querySelector('.sidebar-request-count');
        if (badge) {
            var allPendingEdits = editRequests.filter(function (item) { return requestState(item) === 'pending'; }).length;
            badge.textContent = allPendingEdits;
            badge.classList.toggle('has-pending', allPendingEdits > 0);
            badge.setAttribute('aria-label', allPendingEdits + ' pending profile edits');
        }
    }
    function render() {
        var datedEdits = editRequests.filter(matchesDate);
        var editPendingCount = datedEdits.filter(function (item) { return requestState(item) === 'pending'; }).length;
        var editRows = datedEdits.filter(matchesStatus).sort(function (a, b) {
            return String(b.requestedAt || '').localeCompare(String(a.requestedAt || ''));
        });
        $('requestsBody').closest('table').querySelector('thead').innerHTML =
            '<tr><th>Member</th><th>Requested On</th><th>Changes</th><th>Status</th><th class="text-end">Action</th></tr>';
        $('requestsBody').innerHTML = editRows.length ? editRows.map(function (item) {
            var member = findMember(item.memberId);
            return '<tr data-purok="' + esc(member ? member.purok || '' : '') + '"><td><div class="fw-bold">' + esc(member ? fullName(member) : item.memberName || 'Member record unavailable') + '</div><small class="text-muted">' + esc(item.memberNo || '') + '</small></td>' +
                '<td>' + dateText(item.requestedAt) + '</td><td>' + changePreview(item) + '</td><td>' + statusBadge(requestState(item) === 'pending' ? 'Pending' : item.status) + '</td>' +
                '<td class="text-end">' + checkButton(item) + button('review', item.id, 'Review profile edit', 'visually-hidden') + '</td></tr>';
        }).join('') : '<tr><td colspan="5" class="text-center text-muted py-4">No profile edit requests match these filters.</td></tr>';
        var selectAll = $('selectAllRequests');
        var pendingRows = editRows.filter(function (item) {
            var member = findMember(item.memberId);
            return requestState(item) === 'pending' && member && member.status !== 'Archived';
        });
        var selectedVisibleCount = pendingRows.filter(function (item) {
            return selectedRequestIds.has(String(item.id));
        }).length;
        if (selectAll) {
            selectAll.checked = pendingRows.length > 0 && selectedVisibleCount === pendingRows.length;
            selectAll.indeterminate = selectedVisibleCount > 0 && selectedVisibleCount < pendingRows.length;
            selectAll.disabled = pendingRows.length === 0 || !CAN_REVIEW_MEMBER_REQUESTS;
        }
        updatePendingTotal(editPendingCount);
        updateDashboard();
    }
    function valueHtml(key, value) {
        if (key === 'photo') return value ? '<img src="' + esc(value) + '" alt="" class="rounded border object-fit-cover" style="width:44px;height:44px;">' : '—';
        if (key === 'birthDate') return esc(fmtDate(value));
        if (Array.isArray(value)) return value.length ? esc(value.join(', ')) : '—';
        return value == null || value === '' ? '—' : esc(value);
    }
    function attachmentHtml(request) {
        if (!CAN_REVIEW_MEMBER_REQUESTS || !Array.isArray(request.attachments) || !request.attachments.length) return '';
        var attachments = request.attachments.filter(function (attachment) {
            return attachment && /^[a-f0-9]{32}\.(jpg|png|webp|pdf)$/.test(String(attachment.id || ''));
        });
        if (!attachments.length) return '';
        return '<section class="mt-3"><h6 class="fw-bold">Private supporting documents</h6><ul class="mb-0">' +
            attachments.map(function (attachment) {
                var href = 'api/member-documents.php?id=' + encodeURIComponent(attachment.id);
                var size = Number(attachment.size);
            var uploader = attachment.uploadedBy ? ' · uploaded by ' + esc(attachment.uploadedBy) : '';
                return '<li><a href="' + href + '" target="_blank" rel="noopener noreferrer">' +
                    esc(attachment.name || 'Supporting document') + '</a>' +
                (Number.isFinite(size) && size > 0 ? ' <small class="text-muted">(' + Math.ceil(size / 1024) + ' KB' + uploader + ')</small>' : '') +
                    '</li>';
            }).join('') + '</ul><small class="text-muted">Only the SK Chairperson and Secretary can access these files.</small></section>';
    }
    function showEditRequest(id) {
        var request = editRequests.filter(function (item) { return String(item.id) === String(id); })[0];
        if (!request) return;
        currentEditRequestId = request.id;
        var member = findMember(request.memberId);
        var changes = request.changes && typeof request.changes === 'object' ? request.changes : {};
        var rows = Object.keys(changes).map(function (key) {
            return '<tr><td class="fw-bold">' + esc(FIELD_LABELS[key] || key) + '</td><td>' + valueHtml(key, request.previous && request.previous[key]) + '</td><td>' + valueHtml(key, changes[key]) + '</td></tr>';
        }).join('');
        var pending = request.status === 'Edit Request';
        $('reviewBody').innerHTML =
            '<div class="d-flex justify-content-between align-items-start mb-3"><div><h6 class="fw-bold mb-0">' + esc(member ? fullName(member) : request.memberName || 'Member record unavailable') + '</h6>' +
            '<small class="text-muted">' + esc(request.memberNo || '') + ' • requested ' + dateText(request.requestedAt) + ' by ' + esc(request.requestedBy || '—') + '</small></div>' + statusBadge(request.status) + '</div>' +
            (pending && (!member || member.status === 'Archived') ? '<div class="alert alert-warning small">This member is missing or archived. The edit cannot be approved.</div>' : '') +
            (pending && !CAN_REVIEW_MEMBER_REQUESTS ? '<div class="alert alert-info small">Only the SK Chairperson or Secretary can approve or reject profile changes.</div>' : '') +
            '<div class="table-responsive"><table class="table table-sm align-middle"><thead><tr><th>Field</th><th>Current (official)</th><th>Requested</th></tr></thead><tbody>' +
            (rows || '<tr><td colspan="3" class="text-muted">No changed fields were recorded.</td></tr>') + '</tbody></table></div>' +
            attachmentHtml(request) +
            (!pending ? '<div class="small text-muted">Reviewed ' + dateText(request.reviewedAt) + ' by ' + esc(request.reviewedBy || '—') + '</div>' : '');
        $('approveBtn').classList.toggle('d-none', !pending || !CAN_REVIEW_MEMBER_REQUESTS);
        $('rejectBtn').classList.toggle('d-none', !pending || !CAN_REVIEW_MEMBER_REQUESTS);
        editModal.show();
    }
    function archiveMembersOverAgeLimit() {
        var count = 0;
        members.forEach(function (member) {
            if (member.status === 'Archived' || !member.birthDate || !(calcAge(member.birthDate) > MAX_AGE)) return;
            var reason = 'Automatically archived – exceeded youth age eligibility.';
            var archivedAt = nowISO();
            member.status = 'Archived';
            member.archiveReason = reason;
            member.archivedAt = archivedAt;
            member.archivedBy = 'System';
            member.archiveType = 'auto';
            if (!Array.isArray(member.history)) member.history = [];
            member.history.push({ at: archivedAt, action: 'Auto-archived', by: 'System', note: reason });
            editRequests.forEach(function (request) {
                if (String(request.memberId) === String(member.id) && request.status === 'Edit Request') {
                    request.status = 'Rejected';
                    request.reviewedAt = nowISO();
                    request.reviewedBy = 'System';
                    request.remarks = 'Profile was archived.';
                }
            });
            count++;
        });
        return count;
    }
    function reviewEditRequest(approve) {
        var request = editRequests.filter(function (item) { return String(item.id) === String(currentEditRequestId); })[0];
        if (!request || request.status !== 'Edit Request') {
            notify('warning', 'This profile edit request was already reviewed.');
            return;
        }
        var member = findMember(request.memberId);
        var remarks = '';
        if (approve && (!member || member.status === 'Archived')) {
            notify('danger', 'This profile is missing or archived, so the changes cannot be applied.');
            return;
        }
        var now = nowISO();
        var oldMembers = JSON.stringify(members);
        var oldRequests = JSON.stringify(editRequests);
        if (approve) {
            Object.keys(request.changes || {}).forEach(function (key) { member[key] = request.changes[key]; });
            if (!Array.isArray(member.history)) member.history = [];
            member.updatedAt = now;
            member.history.push({ at: now, action: 'Edit approved', by: OFFICER,
                note: Object.keys(request.changes || {}).map(function (key) { return FIELD_LABELS[key] || key; }).join(', ') });
            request.status = 'Approved';
        } else {
            request.status = 'Rejected';
            if (member) {
                if (!Array.isArray(member.history)) member.history = [];
                member.history.push({ at: now, action: 'Edit rejected', by: OFFICER, note: remarks || 'Changes were not approved.' });
            }
        }
        request.reviewedAt = now;
        request.reviewedBy = OFFICER;
        request.remarks = remarks;
        selectedRequestIds.delete(String(request.id));
        var archivedCount = approve ? archiveMembersOverAgeLimit() : 0;
        try {
            if (window.USE_MEMBERS_TABLE) {
                if (approve) DB.saveMembers(members);
                setList('kkEditRequests', editRequests);
            } else {
                DB.saveMemberData(members, editRequests);
            }
        } catch (error) {
            members = JSON.parse(oldMembers);
            editRequests = JSON.parse(oldRequests);
            notify('danger', error.message || 'Could not save the request review.');
            return;
        }
        editModal.hide();
        notify('success', approve ? 'Changes approved and the profile was updated.' : 'Changes rejected. The profile was left unchanged.');
        if (archivedCount) {
            notify('info', archivedCount + ' profile' + (archivedCount === 1 ? ' was' : 's were') + ' automatically archived for exceeding the youth age limit.');
        }
        render();
    }
    function acceptAllPendingRequests() {
        if (!CAN_REVIEW_MEMBER_REQUESTS) {
            notify('warning', 'Only the SK Chairperson or Secretary can approve profile changes.');
            return;
        }
        var pending = editRequests.filter(function (item) {
            return selectedRequestIds.has(String(item.id)) && requestState(item) === 'pending';
        });
        if (!pending.length) return;
        var ineligible = pending.filter(function (request) {
            var member = findMember(request.memberId);
            return !member || member.status === 'Archived';
        });
        if (ineligible.length) {
            notify('danger', ineligible.length + ' selected request' + (ineligible.length === 1 ? ' has' : 's have') +
                ' a missing or archived profile and cannot be approved.');
            return;
        }
        var oldMembers = JSON.stringify(members);
        var oldRequests = JSON.stringify(editRequests);
        var accepted = 0;
        var now = nowISO();
        pending.forEach(function (request) {
            var member = findMember(request.memberId);
            Object.keys(request.changes || {}).forEach(function (key) {
                member[key] = request.changes[key];
            });
            if (!Array.isArray(member.history)) member.history = [];
            member.updatedAt = now;
            member.history.push({
                at: now,
                action: 'Edit approved',
                by: OFFICER,
                note: 'Bulk approved: ' + Object.keys(request.changes || {}).map(function (key) {
                    return FIELD_LABELS[key] || key;
                }).join(', ')
            });
            request.status = 'Approved';
            request.reviewedAt = now;
            request.reviewedBy = OFFICER;
            request.remarks = 'Approved in bulk.';
            accepted++;
        });
        if (!accepted) {
            notify('danger', 'No eligible active profiles were found for the pending requests.');
            return;
        }
        var archivedCount = archiveMembersOverAgeLimit();
        try {
            if (window.USE_MEMBERS_TABLE) {
                DB.saveMembers(members);
                setList('kkEditRequests', editRequests);
            } else {
                DB.saveMemberData(members, editRequests);
            }
        } catch (error) {
            members = JSON.parse(oldMembers);
            editRequests = JSON.parse(oldRequests);
            notify('danger', error.message || 'Could not save the bulk request review.');
            return;
        }
        notify('success', accepted + ' profile edit request' + (accepted === 1 ? ' was' : 's were') + ' approved.');
        if (archivedCount) {
            notify('info', archivedCount + ' profile' + (archivedCount === 1 ? ' was' : 's were') + ' automatically archived for exceeding the youth age limit.');
        }
        selectedRequestIds.clear();
        render();
    }
    function dateInputValue(date) {
        return date.getFullYear() + '-' + String(date.getMonth() + 1).padStart(2, '0') + '-' + String(date.getDate()).padStart(2, '0');
    }
    function setDateRange(preset) {
        var today = new Date(), from = '', to = '';
        if (preset === 'today') from = to = dateInputValue(today);
        else if (preset === 'week') {
            var monday = new Date(today.getFullYear(), today.getMonth(), today.getDate());
            monday.setDate(monday.getDate() - ((monday.getDay() + 6) % 7));
            var sunday = new Date(monday.getFullYear(), monday.getMonth(), monday.getDate());
            sunday.setDate(sunday.getDate() + 6);
            from = dateInputValue(monday); to = dateInputValue(sunday);
        } else if (preset === 'first-half') {
            from = dateInputValue(new Date(today.getFullYear(), today.getMonth(), 1));
            to = dateInputValue(new Date(today.getFullYear(), today.getMonth(), 15));
        } else if (preset === 'second-half') {
            from = dateInputValue(new Date(today.getFullYear(), today.getMonth(), 16));
            to = dateInputValue(new Date(today.getFullYear(), today.getMonth() + 1, 0));
        } else if (preset === 'month') {
            from = dateInputValue(new Date(today.getFullYear(), today.getMonth(), 1));
            to = dateInputValue(new Date(today.getFullYear(), today.getMonth() + 1, 0));
        }
        $('requestDateFrom').value = from;
        $('requestDateTo').value = to;
        requestDatePreset = preset;
        render();
    }

    document.addEventListener('click', function (event) {
        var selectButton = event.target.closest('[data-select-request]');
        if (selectButton) {
            var id = String(selectButton.dataset.selectRequest);
            if (selectedRequestIds.has(id)) selectedRequestIds.delete(id);
            else selectedRequestIds.add(id);
            render();
            return;
        }
        var statusCard = event.target.closest('[data-request-status]');
        if (statusCard) {
            requestStatusFilter = statusCard.dataset.requestStatus;
            render();
            return;
        }
        var rangeButton = event.target.closest('[data-request-range]');
        if (rangeButton) {
            setDateRange(rangeButton.dataset.requestRange);
            return;
        }
        var actionButton = event.target.closest('[data-act]');
        if (!actionButton) return;
        var id = actionButton.dataset.id;
        if (actionButton.dataset.act === 'review') showEditRequest(id);
    });
    document.addEventListener('change', function (event) {
        if (!event.target.matches('#selectAllRequests')) return;
        var checked = event.target.checked;
        var rows = Array.prototype.slice.call($('requestsBody').rows);
        rows.forEach(function (row) {
            if (row.style.display === 'none' || !row.cells[3] || !/pending|edit request/i.test(row.cells[3].innerText)) return;
            var selection = row.querySelector('[data-select-request]');
            if (!selection || selection.disabled) return;
            var id = String(selection.dataset.selectRequest);
            if (checked) selectedRequestIds.add(id);
            else selectedRequestIds.delete(id);
        });
        render();
    });
    $('approveBtn').addEventListener('click', function () { reviewEditRequest(true); });
    $('rejectBtn').addEventListener('click', function () { reviewEditRequest(false); });
    $('acceptAllRequests').addEventListener('click', acceptAllPendingRequests);
    ['requestDateFrom', 'requestDateTo'].forEach(function (id) {
        $(id).addEventListener('change', function () {
            requestDatePreset = '';
            render();
        });
    });
    window.addEventListener('storage', function (event) {
        if (['kkMembers', 'kkEditRequests'].indexOf(event.key) >= 0) {
            try { loadData(); render(); } catch (error) { notify('danger', error.message); }
        }
    });
    try {
        loadData();
        render();
    } catch (error) {
        notify('danger', error.message || 'Could not load request data.');
    }
})();
