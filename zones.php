<?php

session_start();

// Disable errors
//error_reporting(E_ERROR);

// require('class/autoload.php');

// use TMRank\Database;

// $db = new Database;

// $db->deleteAllCache();
// exit;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TMRank</title>
    <?php include_once('template/head_styles.php'); ?>
    <!-- Page-specific styles -->
    <link href="assets/DataTables/datatables.min.css" rel="stylesheet"/>
</head>
<body>

    <?php include_once('template/navbar.php'); ?>

    <?php include_once('template/zones/hero.php'); ?>

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
            
            <section class="section pt-0 pb-6">
                <table id="tableZones" class="table is-fullwidth">
               </table>
            </section>
        </div>
    </div>


    <?php include_once('template/footer.php'); ?>

    <?php include_once('template/body_scripts.php'); ?>
    <!-- Additional scripts below this line -->
    <!-- jQuery AJAX -->
    <script src="assets/jquery/jquery-3.3.1.min.js"></script>
    <script src="assets/js/ajax.js"></script>
    <!-- jQuery DataTables -->
    <script src="assets/DataTables/datatables.min.js"></script>
</body>
</html>