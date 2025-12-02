<?php
header('Content-Type: text/css');

$post_id = 123;
$ref = isset($_GET['ref']) ? urlencode($_GET['ref']) : '';

$css = file_get_contents(__DIR__ . '/../css/stylesheet.css');

$css = str_replace('{{ post.id }}', $post_id, $css);
$css = str_replace('{{ request.META.HTTP_REFERER }}', $ref, $css);
?>