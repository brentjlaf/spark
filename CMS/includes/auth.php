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
$users = using_database_for_users() ? [] : read_json_file($usersFile);

if (using_database_for_users()) {
    rotate_default_admin_if_needed($usersTable, $usersFile);
}

if (empty($users) && using_database_for_users()) {
    $seeded = seed_default_admin($usersTable, $usersFile);
    if ($seeded) {
        purge_users_json_credentials($usersFile);
    }
} elseif (!using_database_for_users() && empty($users)) {
    seed_legacy_admin_file($usersFile);
    $users = read_json_file($usersFile);
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

function using_database_for_users(): bool
{
    $schema = cms_schema_for_json(__DIR__ . '/../data/users.json');
    return $schema !== null && is_database_configured();
}

function seed_default_admin(string $table, string $usersFile): bool
{
    try {
        $pdo = get_db_connection();
        $exists = $pdo->query("SELECT COUNT(*) as c FROM `{$table}`")->fetchColumn();
        if ((int)$exists > 0) {
            return false;
        }

        $temporaryPassword = generate_secure_password();
        $payload = [
            'id' => 1,
            'username' => 'admin',
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
            password_hash($temporaryPassword, PASSWORD_DEFAULT),
        ]);

        write_admin_bootstrap_file($usersFile, $payload['username'], $temporaryPassword, true);
        return true;
    } catch (Throwable $e) {
        return false;
    }
}

function rotate_default_admin_if_needed(string $table, string $usersFile): void
{
    try {
        $pdo = get_db_connection();
        $stmt = $pdo->prepare("SELECT `id`, `username`, `password`, `payload` FROM `{$table}` WHERE `username` = 'admin' LIMIT 1");
        $stmt->execute();
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$admin || empty($admin['password'])) {
            return;
        }

        $knownDefaultHashes = [
            '$2y$12$bZQja9BxSl9mw6MRCs0Um.vXqOfr67DAVqDJ3gs2yu8J1FOx1/Sdm', // packaged seed
        ];

        $isDefault = false;
        foreach ($knownDefaultHashes as $hash) {
            if (hash_equals($hash, $admin['password']) || password_verify('password', $admin['password'])) {
                $isDefault = true;
                break;
            }
        }

        if (!$isDefault) {
            return;
        }

        $newPassword = generate_secure_password();
        $payload = json_decode($admin['payload'], true) ?: [];
        $payload['last_login'] = null;

        $update = $pdo->prepare("UPDATE `{$table}` SET `password` = ?, `payload` = ?, `last_login` = NULL WHERE `id` = ?");
        $update->execute([password_hash($newPassword, PASSWORD_DEFAULT), json_encode($payload, JSON_UNESCAPED_SLASHES), $admin['id']]);
        write_admin_bootstrap_file($usersFile, $admin['username'], $newPassword, false);
        purge_users_json_credentials($usersFile);
    } catch (Throwable $e) {
        // best-effort
    }
}

function seed_legacy_admin_file(string $usersFile): void
{
    if (file_exists($usersFile)) {
        return;
    }

    $password = generate_secure_password();
    $user = [
        'id' => 1,
        'username' => 'admin',
        'password' => password_hash($password, PASSWORD_DEFAULT),
        'role' => 'admin',
        'status' => 'active',
        'created_at' => time(),
        'last_login' => null
    ];
    write_json_file($usersFile, [$user]);
    write_admin_bootstrap_file($usersFile, $user['username'], $password, true);
}

function write_admin_bootstrap_file(string $usersFile, string $username, string $password, bool $isNew): void
{
    $hint = "A secure admin account was " . ($isNew ? 'created' : 'updated') . " automatically.\n";
    $hint .= "Username: {$username}\nPassword: {$password}\n";
    $hint .= "Please log in and rotate this credential immediately, then delete this file.\n";

    $target = dirname($usersFile) . '/initial_admin_credentials.txt';
    file_put_contents($target, $hint);
    @chmod($target, 0600);
}

function generate_secure_password(int $length = 16): string
{
    try {
        $bytes = random_bytes(32);
    } catch (Throwable $e) {
        $bytes = openssl_random_pseudo_bytes(32);
    }

    $encoded = rtrim(strtr(base64_encode($bytes), '+/', '-_'), '=');
    if ($length < 8) {
        $length = 8;
    }
    return substr($encoded, 0, $length);
}

function purge_users_json_credentials(string $usersFile): void
{
    if (is_file($usersFile)) {
        file_put_contents($usersFile, json_encode([], JSON_PRETTY_PRINT));
    }
}

function find_user($username) {
    global $users, $usersTable;

    if (using_database_for_users()) {
        return fetch_user_from_database($usersTable, $username);
    }

    foreach ($users as $user) {
        if (strtolower($user['username']) === strtolower($username)) {
            return $user;
        }
    }
    return null;
}

function fetch_user_from_database(string $table, string $username): ?array
{
    try {
        $stmt = get_db_connection()->prepare("SELECT `id`, `username`, `role`, `status`, `created_at`, `last_login`, `password`, `payload` FROM `{$table}` WHERE LOWER(`username`) = LOWER(?) LIMIT 1");
        $stmt->execute([$username]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return null;
        }

        $payload = json_decode($row['payload'], true) ?: [];
        $payload['id'] = $row['id'];
        $payload['username'] = $row['username'];
        $payload['role'] = $row['role'] ?? ($payload['role'] ?? 'editor');
        $payload['status'] = $row['status'] ?? ($payload['status'] ?? 'active');
        $payload['created_at'] = $row['created_at'] ?? ($payload['created_at'] ?? 0);
        $payload['last_login'] = $row['last_login'] ?? ($payload['last_login'] ?? null);
        $payload['password'] = $row['password'];

        return $payload;
    } catch (Throwable $e) {
        return null;
    }
}

function update_user_login(array $user): array
{
    global $users, $usersFile, $usersTable;

    if (using_database_for_users()) {
        try {
            $pdo = get_db_connection();
            $stmt = $pdo->prepare("SELECT `id`, `payload` FROM `{$usersTable}` WHERE `id` = ? LIMIT 1");
            $stmt->execute([$user['id']]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                $payload = json_decode($row['payload'], true) ?: [];
                $payload['last_login'] = time();
                $update = $pdo->prepare("UPDATE `{$usersTable}` SET `last_login` = ?, `payload` = ? WHERE `id` = ?");
                $update->execute([$payload['last_login'], json_encode($payload, JSON_UNESCAPED_SLASHES), $row['id']]);
                $user['last_login'] = $payload['last_login'];
            }
        } catch (Throwable $e) {
            // fall through to session update
        }
        return $user;
    }

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
