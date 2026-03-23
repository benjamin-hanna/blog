<?php

function renderPage($file, $title, $dest)
{
    $depth = substr_count($dest, '/') - 1;
    $root = str_repeat('../', $depth);

    include 'header.html';
    ?>
    <main>
        <div class="container">
            <div class ="row">
                <div class="twelve columns">
                    <?php include $file; ?>
                </div>
            </div>
        </div>
    </main>
    <?php 
    include 'footer.html';
}