<?php
// Migration script: creates MySQL tables for CMS JSON entities and imports existing data
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/schema.php';

$dataDir = realpath(__DIR__ . '/../data');
$backupDir = $dataDir . '/backup-' . date('Ymd_His');
if (!is_dir($backupDir)) {
    mkdir($backupDir, 0777, true);
}

echo "Backing up JSON files to {$backupDir}\n";
foreach (array_keys(cms_entity_schemas()) as $file) {
    $source = $dataDir . '/' . $file;
    if (is_file($source)) {
        copy($source, $backupDir . '/' . basename($source));
    }
}

$pdo = get_db_connection();

foreach (cms_entity_schemas() as $file => $schema) {
    $jsonPath = $dataDir . '/' . $file;
    $records = load_json_records($jsonPath, $schema);
    ensure_table($pdo, $schema, $records);
    import_records($pdo, $schema, $records);
}

echo "Migration complete.\n";

function load_json_records(string $path, array $schema): array
{
    if (!file_exists($path)) {
        return [];
    }
    $raw = json_decode(file_get_contents($path), true);
    if (!$raw) {
        return [];
    }

    // Settings file is a key-value map
    if ($schema['primary'] === 'setting_key' && array_values($raw) !== $raw) {
        $rows = [];
        foreach ($raw as $key => $value) {
            $rows[] = ['setting_key' => $key, 'value' => $value];
        }
        return $rows;
    }

    return $raw;
}

function ensure_table(PDO $pdo, array $schema, array $records = []): void
{
    $primary = $schema['primary'];
    $jsonCol = $schema['json_column'];
    $columns = $schema['columns'];

    $primaryType = infer_primary_type($records, $primary);
    $primaryDef = $primaryType === 'int'
        ? "INT AUTO_INCREMENT"
        : "VARCHAR(191)";

    $columnSql = [];
    foreach ($columns as $columnName => $sourceKey) {
        $columnSql[] = "`{$columnName}` VARCHAR(255) NULL";
    }
    $indexSql = [];
    foreach ($schema['indexes'] as $index) {
        $indexSql[] = "INDEX `idx_{$schema['table']}_{$index}` (`{$index}`)";
    }

    $sql = sprintf(
        "CREATE TABLE IF NOT EXISTS `%s` (\n%s\n) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",
        $schema['table'],
        implode(",\n", array_filter(array_merge([
            "`{$primary}` {$primaryDef} PRIMARY KEY",
            "`{$jsonCol}` JSON NOT NULL",
        ], $columnSql, $indexSql)))
    );

    $pdo->exec($sql);
}

function import_records(PDO $pdo, array $schema, array $records): void
{
    $pdo->beginTransaction();
    $pdo->exec("TRUNCATE TABLE `{$schema['table']}`");

    $columns = array_merge([$schema['primary'], $schema['json_column']], array_keys($schema['columns']));
    $placeholders = rtrim(str_repeat('?,', count($columns)), ',');
    $stmt = $pdo->prepare(
        "INSERT INTO `{$schema['table']}` (`" . implode('`,`', $columns) . "`) VALUES ({$placeholders})"
    );

    $nextId = 1;
    foreach ($records as $record) {
        if (!is_array($record)) {
            continue;
        }
        $primaryValue = $record[$schema['primary']] ?? $nextId;
        $nextId = is_numeric($primaryValue) ? max($nextId + 1, (int)$primaryValue + 1) : $nextId + 1;
        $values = [$primaryValue, json_encode($record, JSON_UNESCAPED_SLASHES)];
        foreach ($schema['columns'] as $columnName => $sourceKey) {
            if ($schema['primary'] === 'setting_key' && $sourceKey === 'setting_key' && !isset($record['setting_key'])) {
                $values[] = $record['key'] ?? null;
                continue;
            }
            $values[] = $record[$sourceKey] ?? null;
        }
        $stmt->execute($values);
    }

    $pdo->commit();
}

function infer_primary_type(array $records, string $primary): string
{
    foreach ($records as $record) {
        if (isset($record[$primary]) && is_numeric($record[$primary])) {
            return 'int';
        }
    }
    return 'string';
}
?>
