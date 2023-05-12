<?php

    

    session_start();

    require_once('class/autoload.php'); // API

    use TMRank\Players;
    use TMRank\Zones;
    use TMRank\World;
    use TMRank\Utils;


    // Disable errors
    error_reporting(E_ERROR);

    
    

    /* if($_SERVER['REQUEST_METHOD'] == 'GET'){
        
        $login = $_GET['login'];
        // player: All player public information

        $body = "Insert an option.";

        if(empty($login))
        {
            $body = "Insert a login";
        }
        else
        {
            $player = new Players();
            $body = $player->getData($login);
            //print_r($body);
        }
        
    }

    if($_SERVER['REQUEST_METHOD'] == 'POST'){

  
        // world1: Every environment top 10
        // world2: Player position in Merge ladder
        // zone: All zones ranking
        $login = $_POST['login'];

        $body = "Insert an option.";

        if($_POST['searchtype'] == 'player') 
        {
            if(empty($login))
            {
                $body = "Insert a login";
            }
            else
            {
                $player = new Players();
                $body = $player->getData($login);
                //print_r($body);
            }
        }

        if($_POST['searchtype'] == 'world2') 
        {
            $world = new World();
            $body = $world->getData(null);   
            //print_r($body);
            
        }
        if($_POST['searchtype'] == 'world1')
        {
            if(empty($login))
            {
                $body = "Insert a login";
            }
            else
            {
                $world = new World();
                $body = $world->getData($login);   
                //print_r($body);
            }
            
        }
        
        if($_POST['searchtype'] == 'zone') 
        {
            $zones = new Zones();
            $body = $zones->getData();
        }

    }
 */
 
        
    
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
</head>
<body>
    <form action="worldForm" method="post">
        <h1>TMRank</h1>
        <br>
        
        <!-- <?php if ($_SESSION['errorMessage']) echo '<div class="error">'.$_SESSION['errorMessage']."</div>"; else echo ""; ?></p>
        <label for="login">Player login</label><br>
        
        <input type="text" name="login" id="login">
        <input type="submit" value="submit">
        
        <h4>Select search parameter</h4>
        <input type="radio" id="searchtype" name="searchtype" value="world1">
        <label for="world">World - Player position</label><br>
        <input type="radio" id="searchtype" name="searchtype" value="world2">
        <label for="world">World - All top 10s</label><br>
        <input type="radio" id="searchtype" name="searchtype" value="zone">
        <label for="zone">Zones</label>
        <br> -->
    </form>

    <form id="playerForm">
    <code onclick="myFunction()">Dark mode</code>
        <h4>Player login</h4>
        
        <input type="text" name="login" id="loginTest">
        <input type="submit" value="submit">
    </form>
    <br>

    <div id="testDiv"></div>
    <div><?php if($_POST['searchtype']) echo "<h3>" . ucfirst($_POST['searchtype']) . "</h3><br>";?></div>
    <div><?php //foreach($body as $b) {echo $b. "<br>";}
                //print_r($body);?></div>

    <script src="assets/js/ajax.js"></script>

</body>
</html>

<?php $_SESSION['errorMessage'] = ""; ?>