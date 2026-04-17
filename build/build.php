<?php

require 'vendor/autoload.php';

use Symfony\Component\Yaml\Yaml;

$config = Yaml::parseFile('build/config.yaml');

$outputDir = $config['output_dir'];

$parsedown = new ParsedownExtra();

$loader = new \Twig\Loader\FilesystemLoader('src/templates');
$twig = new \Twig\Environment($loader);

/*
* Existing file removal
*/
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

/*
* File tree build
*/
mkdir($outputDir, 0755, true);

foreach ($config['directories'] as $dir) {
    mkdir("$outputDir/$dir", 0755, true);
}

foreach ($config['assets'] as $asset) {
    copy($asset['src'], "$outputDir/{$asset['dest']}");
}

foreach ($config['pages'] as $pages) {
    copy($pages['src'], "$outputDir/{$pages['dest']}");
}

/*
* Base page build
*/
foreach ($config['pages'] as $page) {
    $md = file_get_contents($page['src']);
    $html = $parsedown->text($md);
    $dest = $page['dest'];

    $output = $twig->render($page['template'], [
        'content' => $html,
        'title'   => $page['title'],
        'dest'    => $dest,
    ]);

    file_put_contents($outputDir . '/' . $dest, $output);
}

/*
* Blog index and content build
*/
foreach ($config['posts_src'] as $postSrc) {
    copy($postSrc['src'], "$outputDir/{$postSrc['dest']}");
}

$posts = $config['posts'];

$files = array_filter(scandir($posts), fn ($f) => str_ends_with($f, '.md'));
$slugs = array_map(fn($f) => basename($f, '.md'), $files);

$output = $twig->render('posts.html.twig', [
    'slugs'  => $slugs,
    'title'  => 'Posts',
    'dest'   => 'posts.html',
]);

file_put_contents($outputDir . '/posts.html', $output);

foreach ($files as $f) {
    $md = file_get_contents($posts . '/' . $f);
    $html = $parsedown->text($md);
    $dest = "$outputDir/posts/" . basename($f, '.md') . '.html';
    $depth = substr_count($dest, '/') - 1;
    $root = $depth > 0 ? str_repeat('../', $depth) : 0;

    $output = $twig->render('post.html.twig', [
        'content' => $html,
        'title'   => basename($f, '.md'),
        'dest'    => $dest,
        'root'    => '../',
        'depth'   => 1,
    ]);

    file_put_contents($dest, $output); // missing
}
