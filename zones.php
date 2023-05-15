<?php

    
    //session_cache_limiter('private');
    session_start();

    require_once('class/autoload.php'); // API

    use TMRank\Database;
    use TMRank\Utils;
    use TMRank\Players;

    $db = new Database();

    // DEV ONLY
    //
    // $db->deleteAllCache();
    // exit;

    // Disable errors
    error_reporting(E_ERROR);
    
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link defer rel="stylesheet" href="https://classless.de/classless-tiny.css">
    <!-- Include Animate.css library -->
    <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"
  />
    <title>Tests</title>
    <style>
        .error {
            color: red;
        }
        body {
        padding: 25px;
        background-color: lightsteelblue;
        transition: 0.3s;
        color: black;
        font-size: 25px;
        }

        .dark-mode {
        background-color: #1c1c25;
        transition: 0.3s;
        color: white;
        }
    </style>
    <script src="assets/jquery/jquery-3.3.1.min.js"></script>
    <script>
        function myFunction() {
            var element = document.body;
            element.classList.toggle("dark-mode");
            }
    </script>
    <script>
        function secondsUntil23() {
            const now = new Date();
            const millisUntil23 = new Date(now.getFullYear(), now.getMonth(), now.getDate(), 23, 0, 0, 0) - now;
            const secondsUntil23 = Math.floor(millisUntil23 / 1000);
            console.log(secondsUntil23);
        }
    </script>
</head>
<body>
    <div>
        <h1>TMRank</h1>
        <br>
        <button onclick="myFunction()"><em>night</em></button>
        <br>
        <a href="/tmrank/players.php">
            <button>Players</button>
        </a>
        <a href="/tmrank/world.php">
            <button>World</button>
        </a>

        
    </div>
    <p>Obtained data is shown on the console</p>
    <div>
        <div id="general"></div>
    </div>
    
    
    
    <script src="assets/js/ajax.js"></script>

</body>
</html>

<?php $_SESSION['errorMessage'] = ""; ?>