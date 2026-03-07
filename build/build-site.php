<?php
require '../vendor/autoload.php';
require_once '../src/includes/base.php';

function render($file, $params) {
    ob_start();

    renderPage($file, $params);

    return ob_get_clean();
}

$public = __DIR__ . '/../public';

/**
 *  Start delete public folder contents
 */

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
/**
 *  End delete public folder contents
 */

/**
 *  Start .md to .html conversion
 */

$pubPosts = __DIR__ . '/../public/posts';

if (!is_dir($pubPosts)) {
    mkdir($pubPosts, 0755, true);
}

$pubImgs = __DIR__ . '/../public/posts/img/posts';

if (!is_dir($pubImgs)) {
    mkdir($pubImgs, 0755, true);
}

shell_exec("cp ../posts/img/posts/* ../public/posts/img/posts/");

$posts = __DIR__ . '/../posts';

$files = array_filter(scandir($posts), fn($f) => substr($f, -3) === '.md');

foreach ($files as $f) {
    $Parsedown = new Parsedown();

    $md = file_get_contents($posts . '/' . $f);

    $html = $Parsedown->text($md);

    $out = basename($f, '.md') . '.html';

    file_put_contents('../public/posts/' . $out, $html);
    file_put_contents('../public/posts/' .$out, render('../public/posts/' .$out, ['title' => 'Post']));
}
/**
 *  End .md to .html coversion
 */

$assets = __DIR__ . '/../public/assets';

if (!is_dir($assets)) {
    mkdir($assets, 0755, true);
}

shell_exec("cp -r ../src/assets/css/ ../public/assets/");

$scripts = __DIR__ . '/../public/assets/scripts';

if (!is_dir($scripts)) {
    mkdir($scripts, 0755, true);
}

shell_exec("cp -r ../src/assets/scripts/ ../public/assets/");

file_put_contents('../public/index.html', render('../src/content/about.html', ['title' => 'About']));
file_put_contents('../public/now.html', render('../src/content/now.html', ['title' => 'Now']));
file_put_contents('../public/colophon.html', render('../src/content/colophon.html', ['title' => 'Colophon']));
file_put_contents('../public/contact.html', render('../src/content/contact.html', ['title' => 'Contact']));
file_put_contents('../public/posts.html', render('../src/content/posts.html', ['title' => 'Posts']));
