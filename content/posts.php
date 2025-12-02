<h1>Posts</h1>
<p>This is the blog index.</p>

<ul>
<?php

$posts = __DIR__ . '/../posts';
$files = array_filter(scandir($posts), fn($f) => substr($f, -3) === '.md');
$slugs = array_map(fn($f) => basename($f, '.md'), $files);

foreach ($slugs as $s): ?>
    <li>
        <a href="post.php?slug=<?= urlencode($s) ?>"><?= htmlspecialchars($s) . ".md"?></a>
    </li>
<?php endforeach; ?>
</ul>