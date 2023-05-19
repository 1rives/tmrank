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
    <link href="assets/DataTables/datatables.min.css" rel="stylesheet"/>
</head>
<body>
    

    <?php include_once('template/navbar.php'); ?>

    <?php include_once('template/world/hero.php'); ?>

    <div class="container is-max-widescreen"> 
        <div class="box">

            <?php include_once('template/login_form.php'); ?>
                
            <section class="section pt-5 pb-3">
                <div class="tabs is-centered is-boxed pb-0 mb-0">
                    <ul>
                        <li id="tabMerge" class="is-active">
                            <a>
                            <span class="icon is-small">&#127760;</span>  
                                <span>Merge</span>
                            </a>
                        </li >
                        <li id="tabStadium" class="">
                            <a>
                                <span class="icon is-small">&#127950;</span>
                                <span>Stadium</span>
                            </a>
                        </li>
                        <li id="tabDesert" class="">
                            <a>
                                <span class="icon is-small">&#127797;</span>
                                <span>Desert</span>
                            </a>
                        </li>
                        <li id="tabIsland" class="">
                            <a>
                                <span class="icon is-small">&#127796;</span>
                                <span>Island</span>
                            </a>
                        </li>
                        <li id="tabRally" class="">
                            <a>
                                <span class="icon is-small">&#127794;</span>
                                <span>Rally</span>
                            </a>
                        </li>
                        <li id="tabCoast" class="">
                            <a>
                                <span class="icon is-small">&#127965;</span>    
                                <span>Coast</span>
                            </a>
                        </li>
                        <li id="tabBay" class="">
                            <a>
                                <span class="icon is-small">&#127980;</span>  
                                <span>Bay</span>
                            </a>
                        </li>
                        <li id="tabSnow" class="">
                            <a>
                                <span class="icon is-small">&#127956;</span>  
                                <span>Snow</span>
                            </a>
                        </li>
                    </ul>
                </div>
                <table id="tablePlayer" class="table is-fullwidth is-hidden">
                </table>
                <table id="tableMerge" class="table is-fullwidth">
                </table>
                <table id="tableStadium" class="table is-fullwidth is-hidden">
                </table>
                <table id="tableDesert" class="table is-fullwidth is-hidden">
                </table>
                <table id="tableIsland" class="table is-fullwidth is-hidden">
                </table>
                <table id="tableRally" class="table is-fullwidth is-hidden">
                </table>
                <table id="tableCoast" class="table is-fullwidth is-hidden">
                </table>
                <table id="tableBay" class="table is-fullwidth is-hidden">
                </table>
                <table id="tableSnow" class="table is-fullwidth is-hidden">
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
    <script src="assets/js/main.js"></script>
</body>
</html>