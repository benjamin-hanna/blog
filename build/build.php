<?php

require 'vendor/autoload.php';
require_once 'src/includes/base.php';

use Symfony\Component\Yaml\Yaml;

$config = Yaml::parseFile('build/config.yaml');

$outputDir = $config['output_dir'];

if (is_dir($outputDir)) {
    $delete = function ($dir) use (&$delete) {
        $items = array_diff(scandir($dir), ['.', '..']);
        foreach ($items as $item) {
            $path = "$dir/$item";
            is_dir($path) ? $delete($path) : unlink($path);
        }
        rmdir($dir);
    };
    $delete($outputDir);
}

mkdir($outputDir, 0755, true);

foreach ($config['directories'] as $dir) {
    mkdir("$outputDir/$dir", 0755, true);
}

foreach ($config['assets'] as $asset) {
    copy($asset['src'], "$outputDir/{$asset['dest']}");
}

foreach ($config['pages'] as $page) {
    $dest = "$outputDir/{$page['dest']}";
    ob_start();
    renderPage($page['src'], $page['title'], $dest);
    file_put_contents($dest, ob_get_clean());
}

$postsDir = $config['posts_dir'];

foreach ($config['posts'] as $posts) {
    copy($posts['src'], "$outputDir/{$posts['dest']}");
}

$posts = array_filter(scandir($postsDir), fn ($f) => str_ends_with($f, '.md'));

$parsedown = new ParsedownExtra();

foreach ($posts as $p) {
    $md = file_get_contents("$postsDir/$p");
    $html = $parsedown->text($md);
    
    $tmp = tempnam(sys_get_temp_dir(), 'post_');
    file_put_contents($tmp, $html);
    
    $dest = "$outputDir/posts/" . basename($p, '.md') . '.html';
    ob_start();
    renderPage($tmp, 'Post', $dest);
    file_put_contents($dest, ob_get_clean());
    
    unlink($tmp);
}

$post_index = $config['post_index'];
$dest = "$outputDir/{$post_index['dest']}";
ob_start();
renderPage($post_index['src'], $post_index['title'], $dest);
file_put_contents($dest, ob_get_clean());