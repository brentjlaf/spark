<?php
// File: save-draft.php
require_once __DIR__ . '/../CMS/includes/auth.php';
require_once __DIR__ . '/../CMS/includes/data.php';
require_once __DIR__ . '/../CMS/includes/sanitize.php';
require_login();

$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$content = sanitize_html($_POST['content'] ?? '');
$timestamp = isset($_POST['timestamp']) ? intval($_POST['timestamp']) : time();

if(!$id){
    http_response_code(400);
    echo 'Invalid ID';
    exit;
}

if (!save_page_draft($id, $content, $timestamp)) {
    http_response_code(500);
    echo 'Unable to save draft';
    exit;
}

echo 'OK';
