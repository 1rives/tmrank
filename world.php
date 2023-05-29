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

            <section class="section pt-5 pb-3">

                <div id="tableTabs" class="tabs is-centered is-boxed pb-0 mb-0">
                    <ul>
                        <li id="tabMerge" class="is-active">
                            <a>
                                <span>General</span>
                            </a>
                        </li>
                        <li id="tabStadium" class="">
                            <a>
                                <span>Stadium</span>
                            </a>
                        </li>
                        <li id="tabDesert" class="">
                            <a>
                                <span>Desert</span>
                            </a>
                        </li>
                        <li id="tabIsland" class="">
                            <a>
                                <span>Island</span>
                            </a>
                        </li>
                        <li id="tabRally" class="">
                            <a>
                                <span>Rally</span>
                            </a>
                        </li>
                        <li id="tabCoast" class="">
                            <a>
                                <span>Coast</span>
                            </a>
                        </li>
                        <li id="tabBay" class="">
                            <a>
                                <span>Bay</span>
                            </a>
                        </li>
                        <li id="tabSnow" class="">
                            <a>
                                <span>Snow</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <div id="tableTabPlayer" class="tabs is-centered is-boxed pb-0 mb-0 is-hidden">
                    <ul>
                        <li id="tabPlayer" class="is-active">
                            <a>
                                <span>
                                    <i class="fa-regular fa-circle-xmark"></i>
                                    Go back
                                </span>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Inline width dirty fix  -->
                <table id="tablePlayer" class="table is-fullwidth" style="width: 100%;">
                </table>
                <table id="tableMerge" class="table is-fullwidth" style="width: 100%;">
                </table>
                <table id="tableStadium" class="table is-fullwidth" style="width: 100%;">
                </table>
                <table id="tableDesert" class="table is-fullwidth" style="width: 100%;">
                </table>
                <table id="tableIsland" class="table is-fullwidth" style="width: 100%;">
                </table>
                <table id="tableRally" class="table is-fullwidth" style="width: 100%;">
                </table>
                <table id="tableCoast" class="table is-fullwidth" style="width: 100%;">
                </table>
                <table id="tableBay" class="table is-fullwidth" style="width: 100%;">
                </table>
                <table id="tableSnow" class="table is-fullwidth" style="width: 100%;">
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