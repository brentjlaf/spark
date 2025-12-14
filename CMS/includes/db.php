<?php
// Centralized PDO database connection helper

/**
 * Get a shared PDO connection configured for UTF-8, exceptions, and prepared statements.
 *
 * Environment variables:
 *  - DB_HOST (default: localhost)
 *  - DB_NAME (default: spark_cms)
 *  - DB_USER (default: spark)
 *  - DB_PASS (default: spark)
 *  - DB_CHARSET (default: utf8mb4)
 */
function get_db_connection(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $host = getenv('DB_HOST') ?: 'localhost';
    $name = getenv('DB_NAME') ?: 'spark_cms';
    $user = getenv('DB_USER') ?: 'spark';
    $pass = getenv('DB_PASS') ?: 'spark';
    $charset = getenv('DB_CHARSET') ?: 'utf8mb4';

    $dsn = "mysql:host={$host};dbname={$name};charset={$charset}";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    $pdo = new PDO($dsn, $user, $pass, $options);
    $pdo->exec("SET NAMES {$charset} COLLATE {$charset}_general_ci");
    return $pdo;
}

function db_fetch_all(string $sql, array $params = []): array
{
    $stmt = get_db_connection()->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function db_execute(string $sql, array $params = []): bool
{
    $stmt = get_db_connection()->prepare($sql);
    return $stmt->execute($params);
}

?>
