<?php
// post.php

include 'includes/base.php';

$slug = $_GET['slug'] ?? '';

$path = __DIR__ . '/posts/' . basename($slug) . '.md';

if (!file_exists($path))
{
    http_response_code(404);
    echo "Post not found";
    exit;
}

$post = file_get_contents($path);

$title = $slug;

renderPage('content/post.php', ['post' => $post, 'slug' => $slug]);
?>