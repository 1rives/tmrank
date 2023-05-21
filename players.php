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
            
            <?php include_once('template/login_form.php'); ?>
            
            <section class="section pt-5 pb-3 mb-0">
                <div class="container">
                
                    <!-- General player information -->
                    <div class="columns">
                        <div class="column has-text-centered is-full">
                        <div class="box">
                            <div class="column box has-background-white-ter">
                                <h2 class="title has-text-weight-normal is-size-1 is-size-4-mobile">{NICKNAME}</h2>
                            </div>
                            <p class="is-size-5 py-1">Account type: </p>
                            <p class="is-size-5 py-1">Location: </p>
                        </div>
                        </div>
                    </div>
                
                    <!-- Forever and United environments -->
                    <div class="columns is-centered">
                        <div class="column is-fullwidth">
                            <div class="box">
                                <h2 class="title mb-3">General</h2>
                                <p class="is-size-5 py-1">World Ranking: </p>
                                <p class="is-size-5 py-1">Nation Ranking: </p>
                                <p class="is-size-5 py-1">Ladder points: </p>
                            </div>
                            <div>
                                
                            </div>
                        </div>
                        <div class="column is-mobile">
                            <div class="box">
                                <h2 class="title mb-3">Stadium</h2>
                                <p class="is-size-5 py-1">World Ranking: </p>
                                <p class="is-size-5 py-1">Nation Ranking: </p>
                                <p class="is-size-5 py-1">Ladder points: </p>
                            </div>
                            
                        </div>
                    </div>
                    

                    <!-- United only environments -->
                    <div class="is-unavailable is-unselectable is-hidden-mobile">
                        <div class="columns">
                            <div class="column">
                                <div class="box">
                                    <h2 class="title mb-3">Desert</h2>
                                    <p class="is-size-5 py-1">World Ranking: </p>
                                    <p class="is-size-5 py-1">Nation Ranking: </p>
                                    <p class="is-size-5 py-1">Ladder points: </p>
                                </div>
                            </div>
                            <div class="column">
                                <div class="box">
                                    <h2 class="title mb-3">Island</h2>
                                    <p class="is-size-5 py-1">World Ranking: </p>
                                    <p class="is-size-5 py-1">Nation Ranking: </p>
                                    <p class="is-size-5 py-1">Ladder points: </p>
                                </div>
                            </div>
                        </div>

                        <div class="columns">
                            <div class="column">
                                <div class="box">
                                    <h2 class="title mb-3">Coast</h2>
                                    <p class="is-size-5 py-1">World Ranking: </p>
                                    <p class="is-size-5 py-1">Nation Ranking: </p>
                                    <p class="is-size-5 py-1">Ladder points: </p>
                                </div>
                            </div>
                            <div class="column">
                                <div class="box">
                                    <h2 class="title mb-3">Rally</h2>
                                    <p class="is-size-5 py-1">World Ranking: </p>
                                    <p class="is-size-5 py-1">Nation Ranking: </p>
                                    <p class="is-size-5 py-1">Ladder points: </p>
                                </div>
                            </div>
                        </div>

                        <div class="columns">
                            <div class="column">
                                <div class="box">
                                    <h2 class="title">Bay</h2>
                                    <p class="is-size-5 py-1">World Ranking: </p>
                                    <p class="is-size-5 py-1">Nation Ranking: </p>
                                    <p class="is-size-5 py-1">Ladder points: </p>
                                </div>
                            </div>
                            <div class="column">
                                <div class="box">
                                    <h2 class="title">Snow</h2>
                                    <p class="is-size-5 py-1">World Ranking: </p>
                                    <p class="is-size-5 py-1">Nation Ranking: </p>
                                    <p class="is-size-5 py-1">Ladder points: </p>
                                </div>
                            </div>
                        </div>
                        

                        <!-- United only -->
                        <div class="columns is-centered">
                            <div class="column has-text-centered is-half-desktop ">
                                <div class="box">
                                    <h2 class="title">Solo ladder</h2>
                                    <p class="is-size-5 py-1">World Ranking: </p>
                                    <p class="is-size-5 py-1">Nation Ranking: </p>
                                    <p class="is-size-5 py-1">Ladder points: </p>
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