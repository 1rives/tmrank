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
    <!-- STYLES -->
</head>
<body>

    <?php include_once('template/navbar.php'); ?>

    <?php include_once('template/zones/hero.php'); ?>

    <div class="box container is-max-widescreen">
        <h1 class="title"> Zones ladder </h1>

        <div id="general"></div>
    </div>


    <?php include_once('template/footer.php'); ?>

    <?php include_once('template/body_scripts.php'); ?>
    <!-- Additional scripts below this line -->
    <!-- jQuery AJAX -->
    <script src="assets/jquery/jquery-3.3.1.min.js"></script>
    <script src="assets/js/ajax.js"></script>
</body>
</html>