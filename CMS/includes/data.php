<?php
// File: data.php
// Utility functions for reading/writing JSON files with simple in-memory caching
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/schema.php';
require_once __DIR__ . '/migration.php';

/**
 * Read and decode a JSON file or mapped database table.
 *
 * @param string $file Path to the JSON file
 * @return array Decoded JSON data or empty array on failure
 */
function read_json_file($file) {
    $schema = cms_schema_for_json($file);
    if ($schema) {
        return read_table_as_array($schema);
    }
    if (!file_exists($file)) {
        return [];
    }
    $data = json_decode(file_get_contents($file), true);
    return $data ?: [];
}

/**
 * Persist an array to the JSON file or mapped database table using pretty print formatting.
 *
 * @param string $file  Path to the JSON file
 * @param mixed  $data  Data to encode
 * @return bool True on success, false on failure
 */
function write_json_file($file, $data) {
    $schema = cms_schema_for_json($file);
    if ($schema) {
        $pdo = get_db_connection();
        if (!cms_table_exists($pdo, $schema['table'])) {
            cms_ensure_table($pdo, $schema, is_array($data) ? $data : []);
        }
        return write_table_from_array($schema, $data);
    }
    return file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT)) !== false;
}

/**
 * Load and decode a JSON file while caching the result within the request.
 *
 * @param string $file Path to the JSON file
 * @return array Decoded JSON data or empty array on failure
 */
function get_cached_json($file) {
    static $cache = [];
    if (!isset($cache[$file])) {
        $cache[$file] = read_json_file($file);
    }
    return $cache[$file];
}

/**
 * Map a table row set to the array format expected by callers.
 */
function read_table_as_array(array $schema): array
{
    try {
        $rows = db_fetch_all("SELECT `{$schema['primary']}`, `{$schema['json_column']}` FROM `{$schema['table']}` ORDER BY `{$schema['primary']}`");
        $decoded = [];
        foreach ($rows as $row) {
            $payload = json_decode($row[$schema['json_column']], true) ?: [];
            if (!isset($payload[$schema['primary']])) {
                $payload[$schema['primary']] = $row[$schema['primary']];
            }
            if ($schema['primary'] === 'setting_key') {
                $decoded[$row[$schema['primary']]] = $payload['value'] ?? $payload;
            } else {
                $decoded[] = $payload;
            }
        }
        return $decoded;
    } catch (Throwable $e) {
        return [];
    }
}

/**
 * Replace table contents from an array of associative arrays.
 */
function write_table_from_array(array $schema, $data): bool
{
    if (!is_array($data)) {
        return false;
    }

    if ($schema['primary'] === 'setting_key' && array_values($data) !== $data) {
        $normalized = [];
        foreach ($data as $key => $value) {
            $normalized[] = ['setting_key' => $key, 'value' => $value];
        }
        $data = $normalized;
    }

    try {
        $pdo = get_db_connection();
        $pdo->beginTransaction();
        $pdo->exec("TRUNCATE TABLE `{$schema['table']}`");

        $insertColumns = array_merge([$schema['primary'], $schema['json_column']], array_values($schema['columns']));
        $placeholders = rtrim(str_repeat('?,', count($insertColumns)), ',');
        $sql = "INSERT INTO `{$schema['table']}` (`" . implode('`,`', $insertColumns) . "`) VALUES ({$placeholders})";
        $stmt = $pdo->prepare($sql);

        $rows = array_values($data);
        $nextId = 1;
        foreach ($rows as $row) {
            if (!is_array($row)) {
                continue;
            }
            $primaryValue = $row[$schema['primary']] ?? $nextId;
            $nextId = is_numeric($primaryValue) ? max($nextId + 1, (int)$primaryValue + 1) : $nextId + 1;
            $payload = json_encode($row, JSON_UNESCAPED_SLASHES);
            $values = [$primaryValue, $payload];
            foreach ($schema['columns'] as $columnKey => $sourceKey) {
                $values[] = $row[$sourceKey] ?? null;
            }
            $stmt->execute($values);
        }

        $pdo->commit();
        return true;
    } catch (Throwable $e) {
        if (isset($pdo) && $pdo->inTransaction()) {
            $pdo->rollBack();
        }
        return false;
    }
}

function cms_table_exists(PDO $pdo, string $table): bool
{
    try {
        $stmt = $pdo->prepare('SHOW TABLES LIKE ?');
        $stmt->execute([$table]);
        return (bool) $stmt->fetchColumn();
    } catch (Throwable $e) {
        return false;
    }
}

function ensure_drafts_table(): void
{
    static $ensured = false;
    if ($ensured) {
        return;
    }

    $pdo = get_db_connection();
    $sql = "CREATE TABLE IF NOT EXISTS `cms_page_drafts` (
        `page_id` INT PRIMARY KEY,
        `content` LONGTEXT NOT NULL,
        `updated_at` INT NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    $pdo->exec($sql);
    $ensured = true;
}

function load_page_draft(int $pageId): array
{
    ensure_drafts_table();
    $rows = db_fetch_all('SELECT content, updated_at FROM cms_page_drafts WHERE page_id = ?', [$pageId]);
    if (!$rows) {
        return ['content' => '', 'timestamp' => 0];
    }

    return ['content' => $rows[0]['content'], 'timestamp' => (int) $rows[0]['updated_at']];
}

function save_page_draft(int $pageId, string $content, int $timestamp): bool
{
    ensure_drafts_table();
    return db_execute(
        'INSERT INTO cms_page_drafts (page_id, content, updated_at) VALUES (?, ?, ?)
         ON DUPLICATE KEY UPDATE content = VALUES(content), updated_at = VALUES(updated_at)',
        [$pageId, $content, $timestamp]
    );
}

function delete_page_draft(int $pageId): void
{
    ensure_drafts_table();
    db_execute('DELETE FROM cms_page_drafts WHERE page_id = ?', [$pageId]);
}
?>
