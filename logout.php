<?php
require_once __DIR__ . '/config.php';
$user = current_user();
if ($user) {
    require_once __DIR__ . '/role_store.php';
    sk_audit(db(), $user, null, 'logout', null, ['event' => 'logout']);
}
$_SESSION = [];
session_destroy();
header('Location: login.php');
exit;
