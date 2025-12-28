<?php
// File: delete_post.php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/data.php';
require_login();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$rawInput = file_get_contents('php://input');
$payload = json_decode($rawInput, true);
if (!is_array($payload)) {
    $payload = $_POST;
}

if (!is_array($payload) || !isset($payload['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid request payload.']);
    exit;
}

$postId = is_numeric($payload['id']) ? (int)$payload['id'] : 0;
if ($postId <= 0) {
    http_response_code(422);
    echo json_encode(['error' => 'A valid post ID is required.']);
    exit;
}

$postsFile = __DIR__ . '/../../data/blog_posts.json';
$posts = read_json_file($postsFile);
if (!is_array($posts)) {
    $posts = [];
}

$found = false;
foreach ($posts as $index => $post) {
    if ((int)($post['id'] ?? 0) === $postId) {
        unset($posts[$index]);
        $found = true;
        break;
    }
}

if (!$found) {
    http_response_code(404);
    echo json_encode(['error' => 'Post not found.']);
    exit;
}

if (!write_json_file($postsFile, array_values($posts))) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to delete blog post.']);
    exit;
}

echo json_encode(['success' => true, 'id' => $postId]);
?>
