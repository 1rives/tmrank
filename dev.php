<?php

    require_once('/var/www/html/tmrank/class/autoload.php'); // API

    session_start();

    // Disable errors
    error_reporting(E_ERROR);

    $login = $_POST['login'];
    

    if($_SERVER['REQUEST_METHOD'] == 'POST'){

        // player: All player public information
        // world1: Every environment top 10
        // world2: Player position in Merge ladder
        // zone: All zones ranking

        $body = "Insert an option.";

        if($_POST['searchtype'] == 'player') 
        {
            if(empty($login))
            {
                $body = "Insert a login";
            }
            else
            {
                $player = new \TMRank\Players();
                $body = $player->getData($login);
                //print_r($body);
            }
        }

        if($_POST['searchtype'] == 'world1') 
        {
            $world = new \TMRank\World();
            $body = $world->getData(null);   
            //print_r($body);
            
        }
        if($_POST['searchtype'] == 'world2')
        {
            if(empty($login))
            {
                $body = "Insert a login";
            }
            else
            {
                $world = new \TMRank\World();
                $body = $world->getData($login);   
                //print_r($body);
            }
            
        }
        
        if($_POST['searchtype'] == 'zone') 
        {
            $zones = new \TMRank\Zones();
            $body = $zones->getData();
        }

    }

 
        
    
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://classless.de/classless-tiny.css">
    <title>Tests</title>
    <style>
        .error {
            color: red;
        }
    </style>
</head>
<body>
<form action="" method="post">
    <h1>TMRank</h1>
    <br>
    
    <?php if ($_SESSION['errorMessage']) echo '<div class="error">'.$_SESSION['errorMessage']."</div>"; else echo ""; ?></p>
    <label for="login">Player login</label><br>
    
    
    <input type="text" name="login" id="login">
    <input type="submit" value="submit">
    
    <h4>Select search parameter</h4>
    <input type="radio" id="searchtype" name="searchtype" value="player">
    <label for="player">Player</label><br>
    <input type="radio" id="searchtype" name="searchtype" value="world1">
    <label for="world">World - Player position</label><br>
    <input type="radio" id="searchtype" name="searchtype" value="world2">
    <label for="world">World - All top 10s</label><br>
    <input type="radio" id="searchtype" name="searchtype" value="zone">
    <label for="zone">Zones</label>
</form>
<br>
<div><?php if($_POST['searchtype']) echo "<h3>" . ucfirst($_POST['searchtype']) . "</h3><br>";?></div>
<div><?php //foreach($body as $b) {echo $b. "<br>";}
            print_r($body);?></div>

</body>
</html>

<?php $_SESSION['errorMessage'] = ""; ?>