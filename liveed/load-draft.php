<?php
// File: load-draft.php
require_once __DIR__ . '/../CMS/includes/auth.php';
require_once __DIR__ . '/../CMS/includes/data.php';
require_login();

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if(!$id){
    http_response_code(400);
    echo 'Invalid ID';
    exit;
}

header('Content-Type: application/json');
echo json_encode(load_page_draft($id));
