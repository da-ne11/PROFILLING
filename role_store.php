<?php

function sk_role_rows($pdo, $forUpdate = false) {
    if (USE_SK_ROLES_TABLE) {
        $sql = 'SELECT role_data FROM sk_roles' . ($forUpdate ? ' FOR UPDATE' : '');
        $rows = $pdo->query($sql)->fetchAll(PDO::FETCH_COLUMN);
    } else {
        $sql = "SELECT data_value FROM app_data WHERE data_key = 'skRoles'" . ($forUpdate ? ' FOR UPDATE' : '');
        $raw = $pdo->query($sql)->fetchColumn();
        $rows = $raw === false ? [] : [$raw];
    }
    $roles = [];
    foreach ($rows as $raw) {
        if (USE_SK_ROLES_TABLE) {
            $role = json_decode((string)$raw, true);
            if (!is_array($role)) throw new RuntimeException('An SK role record is invalid.');
            $roles[] = $role;
            continue;
        }
        $decoded = json_decode((string)$raw, true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($decoded)) {
            throw new RuntimeException('The skRoles data is invalid.');
        }
        foreach ($decoded as $role) if (is_array($role)) $roles[] = $role;
    }
    return $roles;
}

function sk_default_committees() {
    return [
        'Committee on Good Governance and Active Citizenship',
        'Committee on Environment & Disaster Risk Resiliency',
        'Committee on Anti-Drug Abuse and Social Protection',
        'Committee on Education and Culture',
        'Committee on Health and Sanitation',
        'Committee on Youth and Sports Development',
        'Committee on Budget and Appropriation',
    ];
}

function sk_committee_names($pdo = null, $forUpdate = false) {
    if ($pdo === null) return sk_default_committees();
    $sql = "SELECT data_value FROM app_data WHERE data_key = 'skCommittees'" . ($forUpdate ? ' FOR UPDATE' : '');
    $raw = $pdo->query($sql)->fetchColumn();
    if ($raw === false) return sk_default_committees();
    $names = json_decode((string)$raw, true);
    if (!is_array($names) || ($names !== [] && array_keys($names) !== range(0, count($names) - 1))) {
        throw new RuntimeException('Committee data is invalid.');
    }
    foreach ($names as $name) {
        if (!is_string($name) || trim($name) === '') throw new RuntimeException('Committee data is invalid.');
    }
    return array_values(array_unique(array_merge(sk_default_committees(), $names)));
}

function sk_role_write($pdo, $role, $updatedBy) {
    $payload = json_encode($role, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION | JSON_THROW_ON_ERROR);
    if (USE_SK_ROLES_TABLE) {
        $date = function ($key) use ($role) {
            $value = $role[$key] ?? null;
            return $value === '' ? null : $value;
        };
        $pdo->prepare("INSERT INTO sk_roles
            (role_code, kk_id, position, status, basis, election_year, date_elected, term_start, term_end, role_data, updated_by)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE kk_id=VALUES(kk_id), position=VALUES(position), status=VALUES(status),
                basis=VALUES(basis), election_year=VALUES(election_year), date_elected=VALUES(date_elected),
                term_start=VALUES(term_start), term_end=VALUES(term_end), role_data=VALUES(role_data),
                updated_by=VALUES(updated_by)")
            ->execute([
                (string)$role['id'], (string)$role['kkId'], (string)$role['position'], (string)$role['status'],
                $role['basis'] ?? null, isset($role['electionYear']) ? (string)$role['electionYear'] : null,
                $date('dateElected'), $date('termStart'), $date('termEnd'), $payload, $updatedBy,
            ]);
        return;
    }

    $roles = sk_role_rows($pdo, true);
    $replaced = false;
    foreach ($roles as $index => $existing) {
        if ((string)($existing['id'] ?? '') === (string)$role['id']) {
            $roles[$index] = $role;
            $replaced = true;
            break;
        }
    }
    if (!$replaced) $roles[] = $role;
    $save = $pdo->prepare("INSERT INTO app_data (data_key, data_value, updated_by) VALUES ('skRoles', ?, ?)
        ON DUPLICATE KEY UPDATE data_value=VALUES(data_value), updated_by=VALUES(updated_by)");
    $save->execute([
        json_encode($roles, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE | JSON_THROW_ON_ERROR),
        $updatedBy,
    ]);
}

function sk_role_find($roles, $id) {
    foreach ($roles as $role) if ((string)($role['id'] ?? '') === (string)$id) return $role;
    return null;
}

function sk_role_is_current($role, $today = null) {
    if (!is_array($role) || ($role['status'] ?? '') !== 'Active') return false;
    $today = $today ?? date('Y-m-d');
    return (empty($role['termStart']) || $role['termStart'] <= $today)
        && (empty($role['termEnd']) || $role['termEnd'] >= $today);
}

function sk_role_member_has_current_assignment($roles, $legacyId, $exceptRoleId = null, $today = null) {
    foreach ($roles as $role) {
        if ((string)($role['kkId'] ?? '') === (string)$legacyId
            && (string)($role['id'] ?? '') !== (string)$exceptRoleId
            && sk_role_is_current($role, $today)) {
            return true;
        }
    }
    return false;
}

function sk_role_canonicalize($value) {
    if (!is_array($value)) return $value;
    $isList = array_keys($value) === range(0, count($value) - 1);
    if (!$isList) ksort($value, SORT_STRING);
    foreach ($value as $key => $child) $value[$key] = sk_role_canonicalize($child);
    return $value;
}

function sk_role_equal($left, $right) {
    return json_encode(sk_role_canonicalize($left), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION | JSON_THROW_ON_ERROR)
        === json_encode(sk_role_canonicalize($right), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION | JSON_THROW_ON_ERROR);
}

function sk_member_for_role_change($pdo, $legacyId, $lock = false) {
    if (USE_MEMBERS_TABLE) {
        $st = $pdo->prepare('SELECT * FROM members WHERE legacy_id = ?' . ($lock ? ' FOR UPDATE' : ''));
        $st->execute([$legacyId]);
        $row = $st->fetch(PDO::FETCH_ASSOC);
        if (!$row) return null;
        $member = kk_member_row_to_object($row);
        return json_decode(json_encode($member, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE), true);
    }
    $sql = "SELECT data_value FROM app_data WHERE data_key = 'kkMembers'" . ($lock ? ' FOR UPDATE' : '');
    $raw = $pdo->query($sql)->fetchColumn();
    $members = $raw === false ? [] : json_decode($raw, true);
    foreach (is_array($members) ? $members : [] as $member) {
        if (is_array($member) && (string)($member['id'] ?? '') === (string)$legacyId) return $member;
    }
    return null;
}

function audit_log_request_metadata() {
    $userAgent = (string)($_SERVER['HTTP_USER_AGENT'] ?? '');
    $lowerAgent = strtolower($userAgent);
    if (preg_match('/ipad|tablet|kindle|silk|playbook|macintosh.*mobile/', $lowerAgent)
        || (strpos($lowerAgent, 'android') !== false && strpos($lowerAgent, 'mobile') === false)) {
        $device = 'Tablet';
    } elseif (preg_match('/mobile|iphone|ipod|windows phone|android.*mobile/', $lowerAgent)) {
        $device = 'Mobile';
    } elseif ($userAgent === '') {
        $device = 'Unknown';
    } else {
        $device = 'Desktop';
    }

    $browser = 'Other';
    foreach ([
        'Edg/' => 'Microsoft Edge',
        'EdgA/' => 'Microsoft Edge',
        'EdgiOS/' => 'Microsoft Edge',
        'OPR/' => 'Opera',
        'SamsungBrowser/' => 'Samsung Internet',
        'Chrome/' => 'Google Chrome',
        'CriOS/' => 'Google Chrome',
        'Firefox/' => 'Mozilla Firefox',
        'FxiOS/' => 'Mozilla Firefox',
        'Safari/' => 'Safari',
    ] as $token => $name) {
        if (stripos($userAgent, $token) !== false) {
            $browser = $name;
            break;
        }
    }
    if ($userAgent === '') $browser = 'Unknown';

    $remoteAddress = (string)($_SERVER['REMOTE_ADDR'] ?? '');
    $ipAddress = filter_var($remoteAddress, FILTER_VALIDATE_IP) ? $remoteAddress : null;
    $trustedProxies = array_filter(array_map('trim', explode(',', (string)getenv('KK_TRUSTED_PROXIES'))));
    if ($ipAddress !== null && in_array($remoteAddress, $trustedProxies, true)) {
        $forwarded = trim((string)($_SERVER['HTTP_X_FORWARDED_FOR'] ?? ''));
        if ($forwarded !== '') {
            $chain = array_map('trim', explode(',', $forwarded));
            $chain[] = $remoteAddress;
            for ($index = count($chain) - 1; $index >= 0; $index--) {
                $candidate = $chain[$index];
                if (!filter_var($candidate, FILTER_VALIDATE_IP)) break;
                $ipAddress = $candidate;
                if (!in_array($candidate, $trustedProxies, true)) break;
            }
        }
    }

    return [
        'device_type' => $device,
        'browser' => $browser,
        'user_agent' => $userAgent === '' ? null : $userAgent,
        'ip_address' => $ipAddress,
        'location' => null,
    ];
}

function audit_log_normalize_browser_location($location) {
    if (!is_array($location)
        || !isset($location['latitude'], $location['longitude'], $location['accuracy'])
        || !is_numeric($location['latitude'])
        || !is_numeric($location['longitude'])
        || !is_numeric($location['accuracy'])) {
        return null;
    }

    $latitude = (float)$location['latitude'];
    $longitude = (float)$location['longitude'];
    $accuracy = (float)$location['accuracy'];
    if (!is_finite($latitude) || !is_finite($longitude) || !is_finite($accuracy)
        || $latitude < -90 || $latitude > 90
        || $longitude < -180 || $longitude > 180
        || $accuracy < 0 || $accuracy > 100000) {
        return null;
    }

    return [
        'source' => 'browser',
        'latitude' => round($latitude, 6),
        'longitude' => round($longitude, 6),
        'accuracy_m' => round($accuracy, 1),
    ];
}

function audit_log_module_for_action($action, $newValue) {
    if ($action === 'page_view' && is_array($newValue) && !empty($newValue['page'])) {
        return basename((string)$newValue['page'], '.php');
    }
    $action = strpos($action, 'api_') === 0 ? substr($action, 4) : $action;
    if (in_array($action, ['login', 'login_failed', 'logout', 'password'], true)) return 'Authentication';
    if ($action === 'audit_detail_viewed') return 'Audit Log';
    if (strpos($action, 'official_') === 0) return 'Official Profiles';
    if (strpos($action, 'transparency_') === 0 || strpos($action, 'expense_') === 0) return 'Budget Transparency';
    if (strpos($action, 'backup_') === 0) return 'Backups';
    if (strpos($action, 'member_portal_') === 0 || strpos($action, 'kk_id_') === 0
        || $action === 'member_profile_photo_changed') return 'Member Portal';
    if (strpos($action, 'member_') === 0) return 'KK Member Records';
    if (strpos($action, 'role_') === 0) return 'SK Roles';
    if (strpos($action, 'meeting_') === 0) return 'Meetings';
    if (strpos($action, 'event_') === 0) return 'Activities';
    if (strpos($action, 'account_') === 0) return 'Accounts';
    if (strpos($action, 'committee_') === 0) return 'Committees';
    if (in_array($action, ['set', 'remove', 'profile_files', 'receipt_upload'], true)) return 'System Data';
    return 'System';
}

function sk_audit($pdo, $actor, $targetLegacyId, $action, $oldValue, $newValue, $description = null, $actorName = null, $browserLocation = null) {
    $encode = function ($value) {
        return $value === null ? null : json_encode(
            $value,
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION | JSON_THROW_ON_ERROR
        );
    };
    $identity = is_array($actor) ? $actor : [];
    $username = (string)($identity['username'] ?? $actorName ?? 'system');
    $fullName = (string)($identity['name'] ?? $identity['full_name'] ?? $actorName ?? $username);
    $role = (string)($identity['role'] ?? 'Unknown');
    if ($description === null && $action === 'page_view' && is_array($newValue) && !empty($newValue['page'])) {
        $description = 'Viewed the ' . basename((string)$newValue['page']) . ' page.';
    }
    if ($description === null && $action === 'login') $description = 'Signed in successfully.';
    if ($description === null && $action === 'logout') $description = 'Signed out.';
    $description = $description ?? ucwords(str_replace('_', ' ', $action));
    $metadata = audit_log_request_metadata();
    if ($action === 'login') {
        $normalizedLocation = audit_log_normalize_browser_location($browserLocation);
        if ($normalizedLocation !== null) {
            $metadata['location'] = json_encode($normalizedLocation, JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
        }
    }
    $pdo->prepare('INSERT INTO audit_log
        (actor_user_id, actor, actor_full_name, actor_role, target_legacy_id, action, module, description,
         old_value, new_value, device_type, browser, user_agent, ip_address, location)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)')
        ->execute([
            $identity['id'] ?? null,
            mb_substr($username, 0, 120, 'UTF-8'),
            mb_substr($fullName, 0, 150, 'UTF-8'),
            mb_substr($role, 0, 60, 'UTF-8'),
            $targetLegacyId,
            $action,
            mb_substr((string)audit_log_module_for_action($action, $newValue), 0, 100, 'UTF-8'),
            mb_substr((string)$description, 0, 1000, 'UTF-8'),
            $encode($oldValue),
            $encode($newValue),
            $metadata['device_type'],
            $metadata['browser'],
            $metadata['user_agent'],
            $metadata['ip_address'],
            $metadata['location'],
        ]);
    $GLOBALS['SK_AUDIT_RECORD_WRITTEN'] = true;
}

function sk_role_valid_date($value, $field, $required = false) {
    if ($value === null || $value === '') {
        if ($required) throw new RuntimeException($field . ' is required.');
        return null;
    }
    if (!is_string($value)) throw new RuntimeException($field . ' must use YYYY-MM-DD.');
    $date = DateTimeImmutable::createFromFormat('!Y-m-d', $value);
    if (!$date || $date->format('Y-m-d') !== $value) throw new RuntimeException($field . ' must use YYYY-MM-DD.');
    return $value;
}

function sk_role_committee_is_assigned($roles, $committee, $exceptRoleId = null) {
    foreach ($roles as $role) {
        if (!is_array($role)
            || (string)($role['id'] ?? '') === (string)$exceptRoleId
            || ($role['position'] ?? '') !== 'SK Kagawad'
            || ($role['status'] ?? '') !== 'Active'
            || !in_array($committee, sk_role_committee_values($role), true)) continue;
        return true;
    }
    return false;
}

function sk_role_committee_values($role) {
    $values = $role['committees'] ?? null;
    $legacy = $role['committee'] ?? null;
    if (!is_array($values) || ($values === [] && $legacy !== null && $legacy !== '')) {
        $values = is_array($legacy) ? $legacy : ($legacy === null || $legacy === '' ? [] : [$legacy]);
    }
    $committees = [];
    foreach ($values as $value) {
        if (!is_string($value) || trim($value) === '' || in_array($value, $committees, true)) continue;
        $committees[] = $value;
    }
    return $committees;
}

function sk_role_account_seat_counts($pdo) {
    $counts = ['SK Chairperson' => 0, 'SK Secretary' => 0];
    $rows = $pdo->query("SELECT role, COUNT(*) AS seat_count
        FROM users
        WHERE is_active = 1 AND legacy_id IS NULL
          AND role IN ('SK Chairman', 'SK Chairperson', 'SK Secretary')
        GROUP BY role")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rows as $row) {
        $position = $row['role'] === 'SK Chairman' ? 'SK Chairperson' : $row['role'];
        if (array_key_exists($position, $counts)) {
            $counts[$position] += (int)$row['seat_count'];
        }
    }
    return $counts;
}

function sk_role_seat_error($pdo, $roles, $position, $exceptRoleId = null) {
    $capacities = ['SK Chairperson' => 1, 'SK Chairman' => 1, 'SK Secretary' => 1, 'SK Treasurer' => 1, 'SK Kagawad' => 7];
    if (!isset($capacities[$position])) return '';

    $used = 0;
    foreach ($roles as $role) {
        $rolePosition = ($role['position'] ?? '') === 'SK Chairman' ? 'SK Chairperson' : ($role['position'] ?? '');
        if ((string)($role['id'] ?? '') === (string)$exceptRoleId
            || $rolePosition !== $position
            || !sk_role_is_current($role)) continue;
        $used++;
    }
    $accountSeats = sk_role_account_seat_counts($pdo);
    $used += $accountSeats[$position] ?? 0;
    if ($used < $capacities[$position]) return '';
    return $capacities[$position] === 1
        ? 'There is already an active ' . $position . ' role or account. Reassign that seat first.'
        : 'All ' . $capacities[$position] . ' ' . $position . ' seats are already filled by active members.';
}

function sk_role_validate_payload($input, $allowLegacyViceChairperson = false, $pdo = null) {
    if (!is_array($input)) throw new RuntimeException('A role object is required.');
    $positions = ['SK Chairperson', 'SK Secretary', 'SK Treasurer', 'SK Kagawad'];
    if ($allowLegacyViceChairperson) $positions[] = 'SK Vice Chairperson';
    $committees = sk_committee_names($pdo);
    $position = $input['position'] ?? '';
    $committeeInput = $input['committees'] ?? ($input['committee'] ?? '');
    $basis = $input['basis'] ?? '';
    $year = (string)($input['electionYear'] ?? '');
    if (!in_array($position, $positions, true)) throw new RuntimeException('Select a valid SK position.');
    if (is_array($committeeInput)) {
        if ($committeeInput !== [] && array_keys($committeeInput) !== range(0, count($committeeInput) - 1)) {
            throw new RuntimeException('Committee assignments are invalid.');
        }
        $selectedCommittees = $committeeInput;
    } elseif (is_string($committeeInput) && $committeeInput !== '') {
        $selectedCommittees = [$committeeInput];
    } elseif ($committeeInput === null || $committeeInput === '') {
        $selectedCommittees = [];
    } else {
        throw new RuntimeException('Committee assignments are invalid.');
    }
    $normalizedCommittees = [];
    foreach ($selectedCommittees as $selectedCommittee) {
        if (!is_string($selectedCommittee)) throw new RuntimeException('Select valid committees.');
        $selectedCommittee = trim($selectedCommittee);
        if (strpos($selectedCommittee, 'Other: ') === 0) {
            $customCommittee = trim(substr($selectedCommittee, 7));
            if (!preg_match('/^[^<>\r\n]{1,100}$/u', $customCommittee)) {
                throw new RuntimeException('Enter a valid other committee name (up to 100 characters).');
            }
            $selectedCommittee = 'Other: ' . $customCommittee;
        } elseif (!in_array($selectedCommittee, $committees, true)) {
            throw new RuntimeException('Select valid committees.');
        }
        if (!in_array($selectedCommittee, $normalizedCommittees, true)) $normalizedCommittees[] = $selectedCommittee;
    }
    if ($position === 'SK Kagawad' && !$normalizedCommittees) {
        throw new RuntimeException('Select at least one committee for an SK Kagawad.');
    }
    if ($position !== 'SK Kagawad') $normalizedCommittees = [];
    if (count($normalizedCommittees) > 51) throw new RuntimeException('Too many committee assignments.');
    if (!in_array($basis, ['elected', 'appointed'], true)) throw new RuntimeException('Basis must be elected or appointed.');
    if (!preg_match('/^\d{4}$/', $year) || (int)$year < 2000 || (int)$year > 2100) {
        throw new RuntimeException('Enter a valid election year.');
    }
    $elected = sk_role_valid_date($input['dateElected'] ?? null, 'Date elected', true);
    if (substr($elected, 0, 4) !== $year) throw new RuntimeException('Election year must match date elected.');
    $start = sk_role_valid_date($input['termStart'] ?? null, 'Term start');
    $end = sk_role_valid_date($input['termEnd'] ?? null, 'Term end');
    if ($start !== null && $end !== null && $end <= $start) throw new RuntimeException('Term end must be after term start.');
    if ($start !== null && $start < $elected) throw new RuntimeException('Term cannot start before date elected.');
    foreach (['description' => 500, 'responsibilities' => 600] as $field => $limit) {
        if (isset($input[$field]) && (!is_string($input[$field]) || mb_strlen($input[$field]) > $limit)) {
            throw new RuntimeException($field . ' is too long or invalid.');
        }
    }
    return [
        'position' => $position,
        'committee' => $normalizedCommittees ? $normalizedCommittees[0] : null,
        'committees' => $normalizedCommittees,
        'basis' => $basis,
        'electionYear' => $year,
        'dateElected' => $elected,
        'termStart' => $start,
        'termEnd' => $end,
        'description' => (string)($input['description'] ?? ''),
        'responsibilities' => (string)($input['responsibilities'] ?? ''),
    ];
}
