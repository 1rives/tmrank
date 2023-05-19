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
        <div class="box">
            <section class="section pt-0 pb-6">
                <table id="tableZones" class="table is-hoverable is-fullwidth">
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