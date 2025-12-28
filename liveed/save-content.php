<?php
// File: save-content.php
require_once __DIR__ . '/../CMS/includes/auth.php';
require_once __DIR__ . '/../CMS/includes/data.php';
require_once __DIR__ . '/../CMS/includes/sanitize.php';
require_login();

$pagesFile = __DIR__ . '/../CMS/data/pages.json';
$pages = read_json_file($pagesFile);

$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$content = sanitize_html($_POST['content'] ?? '');
if (!$id) {
    http_response_code(400);
    echo 'Invalid ID';
    exit;
}

foreach ($pages as &$p) {
    if ((int)$p['id'] === $id) {
        $p['content'] = $content;
        $p['last_modified'] = time();
        break;
    }
}
unset($p);

write_json_file($pagesFile, $pages);
require_once __DIR__ . '/../CMS/modules/sitemap/generate.php';

// remove saved draft if exists
delete_page_draft($id);

echo 'OK';
