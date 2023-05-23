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
            
            <section class="section pt-5 pb-3 mb-0">
                <div id="data-container" class="container is-hidden">
                
                    <!-- General player information -->
                    <div class="columns">
                        <div class="column has-text-centered is-full">
                        <div id="player-account-container" class="box">
                            <div class="column box has-background-white-ter">
                                <h2 id="player-nickname" class="title has-text-weight-normal is-size-1 is-size-4-mobile">-</h2>
                            </div>
                            <p id="player-account-type" class="is-size-5 py-1">Account type: -</p>
                            <p id="player-account-location" class="is-size-5 py-1">Location: -</p>
                        </div>
                        </div>
                    </div>
                
                    <!-- Forever and United environments -->
                    <div class="columns is-centered">
                        <div class="column is-fullwidth">
                            <div id="merge-container" class="box">
                                <h2 class="title mb-3">General</h2>
                                <p id="merge-world-ranking" class="is-size-5 py-1">World Ranking: -</p>
                                <p id="merge-nation-ranking" class="is-size-5 py-1">Nation Ranking: -</p>
                                <p id="merge-points" class="is-size-5 py-1">Ladder points: -</p>
                            </div>
                        </div>
                        <div class="column is-mobile">
                            <div id="stadium-container" class="box">
                                <h2 class="title mb-3">Stadium</h2>
                                <p id="stadium-world-ranking" class="is-size-5 py-1">World Ranking: -</p>
                                <p id="stadium-nation-ranking" class="is-size-5 py-1">Nation Ranking: -</p>
                                <p id="stadium-points" class="is-size-5 py-1">Ladder points: -</p>
                            </div>
                            
                        </div>
                    </div>
                    

                    <!-- United only environments -->
                    <div id="united-container" class="">
                        <div class="columns">
                            <div class="column">
                                <div id="desert-container" class="box">
                                    <h2 class="title mb-3">Desert</h2>
                                    <p id="desert-world-ranking" class="is-size-5 py-1">World Ranking: -</p>
                                    <p id="desert-nation-ranking" class="is-size-5 py-1">Nation Ranking: -</p>
                                    <p id="desert-points" class="is-size-5 py-1">Ladder points: -</p>
                                </div>
                            </div>
                            <div class="column">
                                <div id="island-container" class="box">
                                    <h2 class="title mb-3">Island</h2>
                                    <p id="island-world-ranking" class="is-size-5 py-1">World Ranking: -</p>
                                    <p id="island-nation-ranking" class="is-size-5 py-1">Nation Ranking: -</p>
                                    <p id="island-points" class="is-size-5 py-1">Ladder points: -</p>
                                </div>
                            </div>
                        </div>

                        <div class="columns">
                            <div class="column">
                                <div id="coast-container" class="box">
                                    <h2 class="title mb-3">Coast</h2>
                                    <p id="coast-world-ranking" class="is-size-5 py-1">World Ranking: -</p>
                                    <p id="coast-nation-ranking" class="is-size-5 py-1">Nation Ranking: -</p>
                                    <p id="coast-points" class="is-size-5 py-1">Ladder points: -</p>
                                </div>
                            </div>
                            <div class="column">
                                <div id="rally-container" class="box">
                                    <h2 class="title mb-3">Rally</h2>
                                    <p id="rally-world-ranking" class="is-size-5 py-1">World Ranking: -</p>
                                    <p id="rally-nation-ranking" class="is-size-5 py-1">Nation Ranking: -</p>
                                    <p id="rally-points" class="is-size-5 py-1">Ladder points: -</p>
                                </div>
                            </div>
                        </div>

                        <div class="columns">
                            <div class="column">
                                <div id="bay-container" class="box">
                                    <h2 class="title">Bay</h2>
                                    <p id="bay-world-ranking" class="is-size-5 py-1">World Ranking: -</p>
                                    <p id="bay-nation-ranking" class="is-size-5 py-1">Nation Ranking: -</p>
                                    <p id="bay-points" class="is-size-5 py-1">Ladder points: -</p>
                                </div>
                            </div>
                            <div class="column">
                                <div id="snow-container" class="box">
                                    <h2 class="title">Snow</h2>
                                    <p id="snow-world-ranking" class="is-size-5 py-1">World Ranking: -</p>
                                    <p id="snow-nation-ranking" class="is-size-5 py-1">Nation Ranking: -</p>
                                    <p id="snow-points" class="is-size-5 py-1">Ladder points: -</p>
                                </div>
                            </div>
                        </div>
                        

                        <!-- United only -->
                        <div class="columns is-centered">
                            <div class="column has-text-centered is-half-desktop ">
                                <div id="solo-container" class="box">
                                    <h2 class="title">Solo ladder</h2>
                                    <p id="solo-world-ranking" class="is-size-5 py-1">World Ranking: -</p>
                                   <!--  <p id="solo-nation-ranking" class="is-size-5 py-1">Nation Ranking: -</p> -->
                                    <p id="solo-points" class="is-size-5 py-1">Skill points: -</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    

                </div>
            </section>

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