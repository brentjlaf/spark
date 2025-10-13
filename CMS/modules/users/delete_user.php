<?php
// File: delete_user.php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/data.php';
require_once __DIR__ . '/../../includes/sanitize.php';
require_login();

$usersFile = __DIR__ . '/../../data/users.json';
$users = read_json_file($usersFile);
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT) ?: 0;
$users = array_filter($users, function($u) use ($id) { return $u['id'] != $id; });
file_put_contents($usersFile, json_encode(array_values($users), JSON_PRETTY_PRINT));
echo 'OK';
?>
