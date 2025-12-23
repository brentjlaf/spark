<?php
// File: auth.php
session_start();
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/schema.php';
require_once __DIR__ . '/data.php';
require_once __DIR__ . '/settings.php';

ensure_site_timezone();

$usersFile = __DIR__ . '/../data/users.json';
$usersTable = cms_schema_for_json($usersFile)['table'] ?? 'cms_users';
initialize_users_table($usersTable);
$users = read_json_file($usersFile);

if (empty($users)) {
    seed_default_admin($usersTable);
    $users = read_json_file($usersFile);
    if (empty($users)) {
        render_installation_required('No admin account exists. Set ADMIN_USERNAME and ADMIN_PASSWORD to create the first user.');
    }
}

function initialize_users_table(string $table): void
{
    try {
        $pdo = get_db_connection();
        $pdo->exec("CREATE TABLE IF NOT EXISTS `{$table}` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `username` VARCHAR(255) NOT NULL UNIQUE,
            `role` VARCHAR(50) DEFAULT 'admin',
            `status` VARCHAR(50) DEFAULT 'active',
            `created_at` INT DEFAULT 0,
            `last_login` INT NULL,
            `password` VARCHAR(255) NOT NULL,
            `payload` JSON NOT NULL,
            INDEX `idx_status` (`status`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    } catch (Throwable $e) {
        // Table initialization best-effort; failures handled downstream
    }
}

function seed_default_admin(string $table): void
{
    try {
        $pdo = get_db_connection();
        $exists = $pdo->query("SELECT COUNT(*) as c FROM `{$table}`")->fetchColumn();
        if ((int)$exists > 0) {
            return;
        }

        $username = getenv('ADMIN_USERNAME') ?: null;
        $password = getenv('ADMIN_PASSWORD') ?: null;

        if (!$username || !$password) {
            render_installation_required('ADMIN_USERNAME and ADMIN_PASSWORD must be set to create the first administrator account.');
        }

        $payload = [
            'id' => 1,
            'username' => $username,
            'role' => 'admin',
            'status' => 'active',
            'created_at' => time(),
            'last_login' => null
        ];
        $stmt = $pdo->prepare("INSERT INTO `{$table}` (`id`,`payload`,`username`,`role`,`status`,`created_at`,`last_login`,`password`) VALUES (1,?,?,?,?,?,?,?)");
        $stmt->execute([
            json_encode($payload, JSON_UNESCAPED_SLASHES),
            $payload['username'],
            $payload['role'],
            $payload['status'],
            $payload['created_at'],
            $payload['last_login'],
            password_hash($password, PASSWORD_DEFAULT),
        ]);
    } catch (Throwable $e) {
        render_installation_required('Unable to create the initial administrator account. Please verify your database and environment configuration.', $e);
    }
}

function find_user($username) {
    global $users;
    foreach ($users as $user) {
        if (strtolower($user['username']) === strtolower($username)) {
            return $user;
        }
    }
    return null;
}

function update_user_login(array $user): array
{
    global $users, $usersFile;
    $user['last_login'] = time();
    foreach ($users as &$existing) {
        if ($existing['id'] == $user['id']) {
            $existing = $user;
            break;
        }
    }
    unset($existing);
    write_json_file($usersFile, $users);
    return $user;
}

function require_login() {
    if (!isset($_SESSION['user'])) {
        header('Location: login.php');
        exit;
    }
}

function is_logged_in() {
    return isset($_SESSION['user']);
}
?>
