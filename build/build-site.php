<?php

require_once '../src/includes/base.php';

$public = __DIR__ . '/../public';

if (!is_dir($public)) {
    mkdir($public, 0755, true);
} else {
    $it = new RecursiveDirectoryIterator($public, RecursiveDirectoryIterator::SKIP_DOTS);
    $files = new RecursiveIteratorIterator($it,
                 RecursiveIteratorIterator::CHILD_FIRST);
    foreach($files as $file) {
        if ($file->isDir()){
            rmdir($file->getPathname());
        } else {
            unlink($file->getPathname());
        }
    }
    rmdir($public);

    mkdir($public, 0755, true);
}

$assets = __DIR__ . '/../public/assets/css';

if (!is_dir($assets)) {
    mkdir($assets, 0755, true);
}

shell_exec("cp -r ../src/assets/css/ ../public/assets/css/");

$posts = __DIR__ . '/../public/posts';

if (!is_dir($posts)) {
    mkdir($posts, 0755, true);
}

$scripts = __DIR__ . '/../public/assets/scripts';

if (!is_dir($scripts)) {
    mkdir($scripts, 0755, true);
}

shell_exec("cp -r ../src/assets/scripts/ ../public/assets/scripts/");


function render($file, $params) {
    ob_start();

    renderPage($file, $params);

    return ob_get_clean();
}

file_put_contents('../public/index.html', render('../src/content/about.html', ['title' => 'About']));
file_put_contents('../public/now.html', render('../src/content/now.html', ['title' => 'Now']));
file_put_contents('../public/colophon.html', render('../src/content/colophon.html', ['title' => 'Colophon']));
file_put_contents('../public/contact.html', render('../src/content/contact.html', ['title' => 'Contact']));
file_put_contents('../public/posts.html', render('../src/content/posts.html', ['title' => 'Posts']));

$posts = __DIR__ . '/../src/posts';
$files = array_filter(scandir($posts), fn($f) => substr($f, -5) === '.html');

foreach ($files as $f) {
    file_put_contents('../public/posts/' . $f, render('../src/posts/' . $f, ['title' => 'Post']));
}

shell_exec("./deploy-prod.sh");