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
                    
                    <h1 class="title pt-5">General information</h1>

                    <div class="columns">
                        <div class="column has-text-centered is-full">
                        <div class="box">
                            <h1 class="title">General information</h1>
                            <p class="subtitle">World Ranking: </p>
                            <p class="subtitle">Zone Ranking: </p>
                        </div>
                        </div>
                    </div>
                
                    <!-- Forever and United environments -->
                    <div class="columns is-centered">
                        <div class="column is-one-third-desktop">
                            <div class="box">
                                <h1 class="title">General</h1>
                                <p class="subtitle">World Ranking: </p>
                                <p class="subtitle">Zone Ranking: </p>
                            </div>
                        </div>
                        <div class="column is-one-third-desktop">
                            <div class="box">
                                <h1 class="title">Stadium</h1>
                                <p class="subtitle">World Ranking: </p>
                                <p class="subtitle">Zone Ranking: </p>
                            </div>
                        </div>
                    </div>

                    <!-- United only environments -->
                    <div class="columns">
                        <div class="column">
                            <div class="box">
                                <h1 class="title">Desert</h1>
                                <p class="subtitle">World Ranking: </p>
                                <p class="subtitle">Zone Ranking: </p>
                            </div>
                        </div>
                        <div class="column">
                            <div class="box">
                                <h1 class="title">Island</h1>
                                <p class="subtitle">World Ranking: </p>
                                <p class="subtitle">Zone Ranking: </p>
                            </div>
                        </div>
                        <div class="column">
                            <div class="box">
                                <h1 class="title">Coast</h1>
                                <p class="subtitle">World Ranking: </p>
                                <p class="subtitle">Zone Ranking: </p>
                            </div>
                        </div>
                    </div>
                    <div class="columns">
                        <div class="column">
                            <div class="box">
                                <h1 class="title">Rally</h1>
                                <p class="subtitle">World Ranking: </p>
                                <p class="subtitle">Zone Ranking: </p>
                            </div>
                        </div>
                        <div class="column">
                            <div class="box">
                                <h1 class="title">Bay</h1>
                                <p class="subtitle">World Ranking: </p>
                                <p class="subtitle">Zone Ranking: </p>
                            </div>
                        </div>
                        <div class="column">
                            <div class="box">
                                <h1 class="title">Snow</h1>
                                <p class="subtitle">World Ranking: </p>
                                <p class="subtitle">Zone Ranking: </p>
                            </div>
                        </div>
                    </div>

                    <!-- United only -->
                    <div class="columns is-centered">
                        <div class="column has-text-centered is-half-desktop ">
                            <div class="box">
                                <h1 class="title">Solo ladder</h1>
                                <p class="subtitle">World Ranking: </p>
                                <p class="subtitle">Zone Ranking: </p>
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
    <script src="assets/js/ajax.js"></script>
</body>

</html>