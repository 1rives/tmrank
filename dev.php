<?php

require_once('/var/www/html/tmrank/class/autoload.php'); // API

session_start();




// include 'vendor/autoload.php';

//use GuzzleHttp\Client;
//use GuzzleHttp\Promise;

// Page created with the purpose of debugging data

// TEST VERSION
/* function requestData(array $params) {
    // Username and password generated from
    // http://developers.trackmania.com
    $apiuser = $_ENV['TMFWEBSERVICE_USER'];
    $apipw = $_ENV['TMFWEBSERVICE_PASSWORD'];

    $login = $_POST['login'];

    $accept = 'application/json';
    $contentType = 'application/json';

    // Client configuration
    $client = new Client([
        'base_uri' => 'http://ws.trackmania.com',
        'auth' => [ 
            $apiuser, $apipw 
        ],
       
        'timeout' => 10.0,
    ]);

    $environments = array(
        'Merge', // General ranking
        'Stadium',
        'Desert',
        'Island',
        'Rally',
        'Coast',
        'Bay',
        'Snow'
    );


    try 
    {
        $promises = [];
        $body = array();

        // Create all asynchronous requests
        foreach($params as $param) {
            $promises[] = $client->getAsync($param);
        }
        
        $results = Promise\Utils::unwrap($promises);

        // Get every body of $results
        foreach($results as $result) {
            $body[] = $result->getBody();
        }
        return $body;
    } 
    catch (GuzzleHttp\Exception\ClientException $e) {
        echo Psr7\Message::toString($e->getRequest());
        echo Psr7\Message::toString($e->getResponse());
    }
  } */

    //////////////////////////////////////////
    // All available and necessary requests 

    $login = $_POST['login'];


    //// Player data
    /*     $player_infoURI = sprintf('/tmf/players/%s/', $login);
        $player_multirankURI = sprintf('/tmf/players/%s/rankings/multiplayer/', $login);
        $player_solorankURI = sprintf('/tmf/players/%s/rankings/solo/', $login);
    */
    //// World data

    // getPlayerRank()
    $world_environment_rankingURI = sprintf('/tmf/rankings/multiplayer/players/%s/%s/?offset=%s&length=10', $path, $environments, $offset);
    // getMultiplayerRankingForEnvironment()
    $world_playerURI = sprintf('/tmf/players/%s/rankings/multiplayer/%s/', $login, $environments[0]);

    //// Zones data
    $zone_playerURI = sprintf('/tmf/players/%s/', $login);
    $zone_infoURI = sprintf('/tmf/players/%s/rankings/multiplayer/', $login);
    $zone_infoURI = sprintf('/tmf/players/%s/rankings/solo/', $login);


// Request types, for now: Player and World
if($_SERVER['REQUEST_METHOD'] == 'POST'){

  if(isset($_POST['login']) && $_POST['searchtype'] == 'player') {
    
     $player = new \TMRank\Players();
     $body = $player->getData($login);
  }

  if($_POST['searchtype'] == 'world') {

    
 }

}
    // General world data
    /* $world = new \TMRank\World();
    $body = $world->getData($login); */

    // General zones data
    $zones = new \TMRank\Zones();
    $body = $zones->getData();
 

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
    <input type="radio" id="searchtype" name="searchtype" value="world">
    <label for="world">World (Empty login for all data)</label><br>
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