<?php
function renderPage($file, $params = [])
{
    extract($params);

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
    <?php include 'footer.html';    
}