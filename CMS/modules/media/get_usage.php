<?php
// File: get_usage.php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/data.php';
require_once __DIR__ . '/../../includes/sanitize.php';
require_login();

header('Content-Type: application/json');

$id = sanitize_text($_GET['id'] ?? '');
if ($id === '') {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => 'Missing media identifier.'
    ]);
    exit;
}

$mediaFile = __DIR__ . '/../../data/media.json';
$media = read_json_file($mediaFile);
$mediaItem = null;
foreach ($media as $item) {
    if (($item['id'] ?? '') === $id) {
        $mediaItem = $item;
        break;
    }
}

if (!$mediaItem) {
    http_response_code(404);
    echo json_encode([
        'status' => 'error',
        'message' => 'Media item not found.'
    ]);
    exit;
}

$filePath = trim((string)($mediaItem['file'] ?? ''));
if ($filePath === '') {
    echo json_encode([
        'status' => 'success',
        'file' => '',
        'usage' => []
    ]);
    exit;
}

$needles = [$filePath];
if ($filePath[0] !== '/') {
    $needles[] = '/' . $filePath;
}
$needles = array_values(array_unique(array_filter($needles)));

$root = dirname(__DIR__, 2);
$dataDir = $root . '/data';
$usage = [];
$scanned = [];

$pagesFile = $dataDir . '/pages.json';
if (is_file($pagesFile)) {
    $scanned[] = realpath($pagesFile);
    $pages = read_json_file($pagesFile);
    $usage = array_merge($usage, collect_usage($pages, $needles, 'Page', function(array $page): array {
        $name = $page['title'] ?? ($page['slug'] ?? ('Page #' . ($page['id'] ?? '?')));
        $details = [];
        if (!empty($page['slug'])) {
            $details[] = 'Slug: ' . $page['slug'];
        }
        if (isset($page['id'])) {
            $details[] = 'ID: ' . $page['id'];
        }
        return [
            'name' => $name,
            'detail' => implode(' • ', $details)
        ];
    }));
}

$postsFile = $dataDir . '/blog_posts.json';
if (is_file($postsFile)) {
    $scanned[] = realpath($postsFile);
    $posts = read_json_file($postsFile);
    $usage = array_merge($usage, collect_usage($posts, $needles, 'Blog Post', function(array $post): array {
        $name = $post['title'] ?? ($post['slug'] ?? ('Post #' . ($post['id'] ?? '?')));
        $details = [];
        if (!empty($post['slug'])) {
            $details[] = 'Slug: ' . $post['slug'];
        }
        if (!empty($post['status'])) {
            $details[] = 'Status: ' . ucfirst((string)$post['status']);
        }
        return [
            'name' => $name,
            'detail' => implode(' • ', $details)
        ];
    }));
}

$eventsFile = $dataDir . '/events.json';
if (is_file($eventsFile)) {
    $scanned[] = realpath($eventsFile);
    $events = read_json_file($eventsFile);
    $usage = array_merge($usage, collect_usage($events, $needles, 'Event', function(array $event): array {
        $name = $event['title'] ?? ($event['id'] ?? 'Event');
        $details = [];
        if (!empty($event['id'])) {
            $details[] = 'ID: ' . $event['id'];
        }
        if (!empty($event['status'])) {
            $details[] = 'Status: ' . ucfirst((string)$event['status']);
        }
        return [
            'name' => $name,
            'detail' => implode(' • ', $details)
        ];
    }));
}

$settingsFile = $dataDir . '/settings.json';
if (is_file($settingsFile)) {
    $scanned[] = realpath($settingsFile);
    $settings = read_json_file($settingsFile);
    $matches = gather_matches($settings, $needles);
    if (!empty($matches)) {
        $fields = format_fields($matches);
        $usage[] = [
            'type' => 'Settings',
            'name' => 'Site Settings',
            'details' => !empty($fields) ? 'Fields: ' . implode(', ', $fields) : null
        ];
    }
}

$draftDir = $dataDir . '/drafts';
if (is_dir($draftDir)) {
    foreach (glob($draftDir . '/*.json') as $draftFile) {
        $real = realpath($draftFile);
        if ($real !== false) {
            $scanned[] = $real;
        }
        $draftData = read_json_file($draftFile);
        $matches = gather_matches($draftData, $needles);
        if (!empty($matches)) {
            $fields = format_fields($matches);
            $usage[] = [
                'type' => 'Draft',
                'name' => basename($draftFile),
                'details' => !empty($fields) ? 'Fields: ' . implode(', ', $fields) : null
            ];
        }
    }
}

if (is_dir($dataDir)) {
    foreach (glob($dataDir . '/*.json') as $file) {
        $real = realpath($file);
        if ($real !== false && in_array($real, $scanned, true)) {
            continue;
        }
        $data = read_json_file($file);
        $matches = gather_matches($data, $needles);
        if (!empty($matches)) {
            $fields = format_fields($matches);
            $usage[] = [
                'type' => 'Data File',
                'name' => basename($file),
                'details' => !empty($fields) ? 'Fields: ' . implode(', ', $fields) : null
            ];
        }
    }
}

usort($usage, function(array $a, array $b) {
    $typeCompare = strcasecmp($a['type'] ?? '', $b['type'] ?? '');
    if ($typeCompare !== 0) {
        return $typeCompare;
    }
    return strcasecmp($a['name'] ?? '', $b['name'] ?? '');
});

echo json_encode([
    'status' => 'success',
    'file' => $filePath,
    'usage' => array_values(array_map(function(array $item) {
        if (!isset($item['details']) || $item['details'] === null || $item['details'] === '') {
            unset($item['details']);
        }
        return $item;
    }, $usage))
]);

function collect_usage($items, array $needles, string $type, callable $descriptor): array {
    $results = [];
    if (!is_array($items)) {
        return $results;
    }
    foreach ($items as $item) {
        if (!is_array($item)) {
            continue;
        }
        $matches = gather_matches($item, $needles);
        if (empty($matches)) {
            continue;
        }
        $fields = format_fields($matches);
        $meta = $descriptor($item);
        $entry = [
            'type' => $type,
            'name' => $meta['name'] ?? $type
        ];
        $detailParts = [];
        if (!empty($meta['detail'])) {
            $detailParts[] = $meta['detail'];
        }
        if (!empty($fields)) {
            $detailParts[] = 'Fields: ' . implode(', ', $fields);
        }
        if (!empty($detailParts)) {
            $entry['details'] = implode(' • ', $detailParts);
        }
        $results[] = $entry;
    }
    return $results;
}

function gather_matches($data, array $needles, string $path = ''): array {
    if (is_object($data)) {
        $data = (array) $data;
    }
    $matches = [];
    if (is_array($data)) {
        foreach ($data as $key => $value) {
            $childPath = $path === '' ? (string)$key : $path . '.' . $key;
            $matches = array_merge($matches, gather_matches($value, $needles, $childPath));
        }
    } elseif (is_string($data)) {
        foreach ($needles as $needle) {
            if ($needle !== '' && stripos($data, $needle) !== false) {
                $matches[] = $path;
                break;
            }
        }
    }
    return $matches;
}

function format_fields(array $paths): array {
    $fields = [];
    foreach ($paths as $path) {
        $fields[] = normalize_field($path);
    }
    $fields = array_unique(array_filter($fields));
    sort($fields, SORT_NATURAL | SORT_FLAG_CASE);
    return $fields;
}

function normalize_field(string $path): string {
    if ($path === '') {
        return 'value';
    }
    $parts = explode('.', $path);
    foreach ($parts as $part) {
        $part = (string)$part;
        if ($part === '' || ctype_digit($part)) {
            continue;
        }
        return $part;
    }
    return end($parts) ?: 'value';
}
