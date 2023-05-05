<?php

session_start();

include 'vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Promise;

// Page created with the purpose of debugging data

// include_once('functions/php/functions.php');
// header('Content-Type: application/json');
$body = array();

/**
 * undocumented function summary
 *
 * Undocumented function long description
 *
 * @param Type $var Description
 * @return type
 * @throws conditon
 **/
function requestData(array $params) {
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
        'headers' => [
            'accept' => $accept,
            'Content-Type' => $contentType,
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
            array_push($promises, $client->getAsync($param));
        }
        
        $results = Promise\Utils::unwrap($promises);

        // Get every body of $results
        foreach($results as $result) {
            array_push($body, $result->getBody());
        }

        return $body;
    } 
    catch (Exception $e) 
    {
        echo 'Caught exception: ',  $e->getMessage(), "\n";
    }
  }

// All available and necessary requests 

    $login = $_POST['login'];

    //// Player data
    $player_infoURI = sprintf('/tmf/players/%s/', $login);
    $player_multirankURI = sprintf('/tmf/players/%s/rankings/multiplayer/', $login);
    $player_solorankURI = sprintf('/tmf/players/%s/rankings/solo/', $login);

    //// World data

    // getPlayerRank()
    // Used for: player's world ranking - all environment's top 10 on the world.
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
     $body = requestData([$player_infoURI, $player_multirankURI, $player_solorankURI]);
  }

  if(isset($_POST['login']) && $_POST['searchtype'] == 'world') {
    $body = requestData([$world_playerURI]);
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
</head>
<body>
<form action="" method="post">
    <h1>TMRank</h1>
    <br>
    <label for="login">Player login</label>
    <br>
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
<div><?php foreach($body as $b) {echo $b;}?></div>

</body>
</html>