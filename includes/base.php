<?php
function renderPage($file, $params = [])
{
    extract($params);

    include 'includes/header.php';
    ?>

    <div class="container">
        <div class ="row">
            <div class="twelve columns">
                <?php include $file; ?>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php';    
}