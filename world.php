<?php

    session_start();



    include_once('functions/php/functions.php'); // General functions

    if(!isset($_POST['submit']))
    {
        unset($_SESSION['errorMessage']);
    }


    if (isset($_POST['submit']) && isset($_POST['login']))
    {
        $login = $_POST['login'];
    }
    
    // Redis key name to get data
    $php_script_name = explode(".", basename($_SERVER['PHP_SELF'])); // world.php
    $ladder_name = strtoupper($php_script_name[0]); // WORLD
    $redis_name = $_ENV["REDIS_VARIABLE_$ladder_name"]; // REDIS_VARIABLE_WORLD

    // Get data for showing
    if(isset($login)){
        $world = getWorldInfo($login);
    }
    else{
        $world = getCacheObject($redis_name);
    }


    // print_r($world->leaderboard['Merge'][0]->rank);

    // For showing data
    $player_environment = "Merge"; 

?>

<!doctype html>
<html lang="en" data-bs-theme='dark'>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <meta name="generator" content="Hugo 0.84.0">
    <title>Search a player</title>

    <link rel="canonical" href="https://getbootstrap.com/docs/5.0/examples/pricing/">

    
    <!-- CSS styles -->
    <link href='assets/bootstrap/css/bootstrap.min.css' rel='stylesheet'>
    <link href='assets/css_old/main.css' rel='stylesheet'>


</head>


<svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
    <symbol id="check" viewBox="0 0 16 16">
        <title>Check</title>
        <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z"/>
    </symbol>
</svg>


<?php include_once "templates/navbar.php" ?>

<body>

<div class="container py-3">
    
        
    <div class="pricing-header p-3 pb-md-4 mx-auto text-center">
        <h1 class="display-4 fw-normal">World ranking</h1>
    </div>

    <br>
    <main class='w-100 mx-at py-3'>
        <form action='' method='POST'>
            <span class='fw-bold mb-0' style='color: red'>
              <?php if ($_SESSION['errorMessage']) echo $_SESSION['errorMessage']; else echo ''; ?>
            </span>

            <div class="form-floating mb-3 d-flex">
                <input required type="text" name="login" class="form-control" id="floatingInput" placeholder=""
                       maxlength="20">
                <label for="floatingInput">Player login</label>
                <button class="btn btn-primary" name="submit" type="submit">Search</button>
            </div>
        </form>
        
        <ul class='nav nav-tabs justify-content-center ' id='myTab' role='tablist'>
            <li class='nav-item' role='presentation'>
                <button class='nav-link active' id='merge-leaderboard' data-bs-toggle='tab' data-bs-target='#merge' type='button'
                        role='tab' aria-controls='merge' aria-selected='true'>General
                </button>
            </li>
            <li class='nav-item' role='presentation'>
                <button class='nav-link' id='stadium-leaderboard' data-bs-toggle='tab' data-bs-target='#stadium' type='button'
                        role='tab' aria-controls='stadium' aria-selected='false' <?php playerDisableButton($login); ?>>Stadium
                </button>
            </li>
            <li class='nav-item' role='presentation'>
                <button class='nav-link' id='island-leaderboard' data-bs-toggle='tab' data-bs-target='#island' type='button'
                        role='tab' aria-controls='island' aria-selected='false' <?php playerDisableButton($login); ?>>Island
                </button>
            </li>
            <li class='nav-item' role='presentation'>
                <button class='nav-link' id='desert-leaderboard' data-bs-toggle='tab' data-bs-target='#desert' type='button'
                        role='tab' aria-controls='desert' aria-selected='false' <?php playerDisableButton($login); ?>>Desert
                </button>
            </li>
            <li class='nav-item' role='presentation'>
                <button class='nav-link' id='coast-leaderboard' data-bs-toggle='tab' data-bs-target='#coast' type='button'
                        role='tab' aria-controls='coast' aria-selected='false' <?php playerDisableButton($login); ?>>Coast
                </button>
            </li>
            <li class='nav-item' role='presentation'>
                <button class='nav-link' id='rally-leaderboard' data-bs-toggle='tab' data-bs-target='#rally' type='button'
                        role='tab' aria-controls='rally' aria-selected='false' <?php playerDisableButton($login); ?>>Rally
                </button>
            </li>
            <li class='nav-item' role='presentation'>
                <button class='nav-link' id='bay-leaderboard' data-bs-toggle='tab' data-bs-target='#bay' type='button'
                        role='tab' aria-controls='bay' aria-selected='false' <?php playerDisableButton($login); ?>>Bay
                </button>
            </li>
            <li class='nav-item' role='presentation'>
                <button class='nav-link' id='snow-leaderboard' data-bs-toggle='tab' data-bs-target='#snow' type='button'
                        role='tab' aria-controls='snow' aria-selected='false' <?php playerDisableButton($login); ?>>Snow
                </button>
            </li>
        </ul>
        <div class='tab-content' id='myTabContent'>
            <?php showWorldTable($login, $world, $player_environment); ?>
        </div>
    </main>

</div>

<script src='assets/bootstrap/js/bootstrap.bundle.min.js'></script>
<script src='assets/jquery/jquery-3.3.1.min.js'></script>

</body>
<?php include_once "templates/footer.php" ?>
</html>

