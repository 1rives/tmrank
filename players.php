<?php

session_start();

// Disable errors
//error_reporting(E_ERROR);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TMRank</title>
    <?php include_once('template/head_styles.php'); ?>
    <!-- Page-specific styles -->
    <!-- STYLES -->
</head>
<body>

    <?php include_once('template/navbar.php'); ?>

    <?php include_once('template/players/hero.php'); ?>

    <div class="container is-max-widescreen"> 
        <div class="box no-top-radius">

            <!-- Error for disabled JavaScript -->
            <noscript>
                <section class="section pt-0 pb-5 mt-0 mb-0">
                    <article class="message is-danger">

                        <div class="message-header">
                            <p>An error has occurred</p>
                        </div>

                        <div class="message-body">
                            <p>This page requires JavaScript to function properly. Please enable JavaScript in your browser settings and refresh the page.</p>
                        </div>

                    </article>
                </section>
            </noscript>

            <?php include_once('template/login_form.php'); ?>
            
            <?php include_once('template/players/content.php'); ?>

        </div>
    </div>

    <?php include_once('template/footer.php'); ?>

    <?php include_once('template/body_scripts.php'); ?>
    <!-- Additional scripts below this line -->
    <!-- jQuery AJAX -->
    <script src="assets/jquery/jquery-3.3.1.min.js"></script>
    <script defer src="assets/js/ajax.js"></script>
</body>

</html>