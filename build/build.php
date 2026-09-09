<?php

require 'vendor/autoload.php';

use Symfony\Component\Yaml\Yaml;

$config = Yaml::parseFile('build/config.yaml');

$outputDir = $config['output_dir'];

$parsedown = new ParsedownExtra();

$loader = new \Twig\Loader\FilesystemLoader('src/templates');
$twig = new \Twig\Environment($loader);

$verbose = in_array('-v', $argv);
vecho("Building site infrastructure");

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
        vecho("removed: $dir");
    };
    $delete($outputDir);
}

/*
* File tree build
*/
mkdir($outputDir, 0755, true);

foreach ($config['directories'] as $dir) {
    mkdir("$outputDir/$dir", 0755, true);
    if ($verbose) { echo "made: $outputDir/$dir\n"; }
}

foreach ($config['assets'] as $asset) {
    copy($asset['src'], "$outputDir/{$asset['dest']}");
    vecho("copied: {$asset['src']}");
}

foreach ($config['app'] as $app) {
    copy($app['src'], "$outputDir/{$app['dest']}");
    vecho("copied: {$asset['src']}");
}

foreach ($config['pages'] as $pages) {
    copy($pages['src'], "$outputDir/{$pages['dest']}");
    vecho("copied: {$asset['src']}");
}

foreach ($config['files'] as $files) {
    copy($files['src'], "$outputDir/{$files['dest']}");
    vecho("copied: {$asset['src']}");
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
        'depth'   => 1
    ]);

    file_put_contents($outputDir . '/' . $dest, $output);
    vecho("copied page: {$page['title']}");
}

/*
* Blog index and content build
*/
$posts = $config['posts'];
$files = array_values(array_filter(scandir($posts), fn ($f) => str_ends_with($f, '.md')));
$titles = array_map(fn($f) => basename($f, '.md'), $files);

$slugs = array_map(function($title) {
    $slug = iconv('UTF-8', 'ASCII//TRANSLIT', $title);
    $slug = strtolower($slug);
    $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
    $slug = preg_replace('/[\s-]+/', '-', $slug);
    $slug = trim($slug, '-');

    return [
        'title' => $title,
        'slug'  => $slug,
    ];
}, $titles);

$output = $twig->render('posts.html.twig', [
'slugs'  => $slugs,
'title'  => 'Posts',
'dest'   => 'posts.html',
'depth'  => 1
]);

file_put_contents($outputDir . '/pages/blog.html', $output);
vecho("copied page: blog");

foreach ($files as $f) {;
    $md = file_get_contents($posts . '/' . $f);
    $html = $parsedown->text($md);

    $title = basename($f, '.md');
    $match = array_filter($slugs, fn($s) => $s['title'] === $title);
    $slug = $match ? array_values($match)[0]['slug'] : null;

    $dest = "$outputDir/posts/" . $slug . '.html';

    $depth = substr_count($dest, '/') - 1;
    $root = $depth > 0 ? str_repeat('/', $depth) : 0;

    $output = $twig->render('post.html.twig', [
        'content' => $html,
        'title'   => basename($f, '.md'),
        'dest'    => $dest,
        'root'    => '../',
        'depth'   => 1,
    ]);
    file_put_contents($dest, $output);
    vecho("copied post: $title");
}

function vecho($msg) {
    global $verbose;
    if ($verbose) echo "$msg\n";
}
