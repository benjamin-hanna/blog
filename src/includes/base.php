<?php
function renderPage($file, $params = [])
{
    extract($params);

    $title = $params['title'];

    if ($title === 'Post') {
        include 'postHeader.html';
    } else {
        include 'header.html';
    }
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
    if ($title === 'Post') {
        include 'postFooter.html';
    } else {
        include 'footer.html';
    } 
}