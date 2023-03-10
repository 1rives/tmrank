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

        $playerinfo = getPlayerInfo($login);
    }

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <meta name="generator" content="Hugo 0.84.0">
    <title>Search a player</title>

    <link rel="canonical" href="https://getbootstrap.com/docs/5.0/examples/pricing/">

    <script src="http://code.jquery.com/jquery-2.0.0.js"></script>
    <!-- Bootstrap core CSS -->
    <link href="assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">


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
            <h1 class="display-4 fw-normal">Search a player</h1>
        </div>
        <br>
        <main class="w-100 mx-at py-3">
            <form action="" method="POST">
                <span class="fw-bold mb-0" style="color: red">
                <?php if ($_SESSION['errorMessage']) echo $_SESSION['errorMessage']; else echo ""; ?>
                </span>

                <div class="form-floating mb-3 d-flex">
                    <input required type="text" name="login" class="form-control" id="floatingInput" placeholder="" maxlength="20">
                    <label for="floatingInput">Player login</label>
                    <button class="btn btn-primary" name="submit" type="submit">Search</button>
                </div>
            </form>
            <br>

            <?php
            if (isset($playerinfo)) {
                include_once "templates/datatables/player-datatable.php";
            }
            ?>
        </main>
    </div>

<script src='assets/bootstrap/js/bootstrap.bundle.min.js'></script>
<script src='assets/js/scripts.js'></script>


</body>

    <?php include_once "templates/footer.php" ?>

</html>

