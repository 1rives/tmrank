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

    ///////////////////////////////////////////////////

     // players: All player public information

 
     // Generate Redis key
    //  $loginKey = 'Players.dragsterboy01';
 
    //  if(!$db->getCacheDataLength($loginKey))
    //  {
    //    $players = new Players();
    //    $playersLoginData = $players->getData('dragsterboy01');
 
    //    print_r($playersLoginData);
 
    //    // Save to database
    //    $db->saveCacheData($playersLoginData, $loginKey);      
    //    exit;
    //  }
    //  else
    //  {
    //     print_r(json_encode($db->getCacheData($loginKey)));
    //    exit;
    //  }

    ////////////////////////////////////////////////////

    
    // CHECK FOR EXISTING DATA ON CACHE
    //
    // var_dump($db->getCacheDataLength(
    //     getenv('REDIS_VARIABLE_PLAYERS') . '.test'
    //   ));
    //   exit;

    //


    // Disable errors
    //error_reporting(E_ERROR);

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
        <a href="/tmrank/world.php">
            <button>World</button>
        </a>

        
    </div>
    <p>Obtained data is shown on the console</p>

    <div>
        <form id="loginForm">
            <h4>Player login</h4>
            
            <input type="text" name="login" id="login">
            <input type="submit" value="submit">
        </form>
    </div>
    

    <!-- <div id="testDiv"></div> -->
    <div><?php //if($_POST['searchtype']) echo "<h3>" . ucfirst($_POST['searchtype']) . "</h3><br>";?></div>
    <div><?php //foreach($body as $b) {echo $b. "<br>";}
                //print_r($body);?></div>

    <script src="assets/js/ajax.js"></script>

</body>
</html>

<?php $_SESSION['errorMessage'] = ""; ?>