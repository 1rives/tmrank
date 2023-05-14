<?php 


//session_start();

require_once('class/autoload.php'); // API

use TMRank\Players;
use TMRank\World;
use TMRank\Utils;
use TMRank\Database;

// 
// Created for AJAX request testing
//

// Disable errors
//error_reporting(E_ERROR);


// TODO: Create different pages for ajax

// Used for caching data
$utils = new Utils();
$db = new Database();
print_r($db->getCacheData(
  getenv('REDIS_VARIABLE_PLAYERS') . '.' . $utils->sanitizeLogin($_POST['login'])
));
exit;


if($_SERVER['REQUEST_METHOD'] == 'GET')
{
    // players: All player public information

    $utils->validateLogin($_GET['login']);

    $login = $utils->sanitizeLogin($_GET['login']);

    // Generate Redis key
    $loginKey = getenv('REDIS_VARIABLE_PLAYERS') . '.' . $login;

    if(!$db->getCacheDataLength($loginKey))
    {
      $players = new Players();
      $playersLoginData = $players->getData($login);

      echo json_encode($playersLoginData);

      // Save to database
      $db->saveCacheData($playersLoginData, $loginKey);      
      exit;
    }
    else
    {
      echo json_encode($db->getCacheData($loginKey));
      exit;
    }
 
}

if($_SERVER['REQUEST_METHOD'] == 'POST')
{
    // world: Player position in Merge ladder

    $utils->validateLogin($_POST['login']);

    $login = $utils->sanitizeLogin($_POST['login']);

    // Generate Redis key
    $loginKey = getenv('REDIS_VARIABLE_WORLD') . '.' . $login;

    if(!$db->getCacheDataLength($loginKey))
    {
      $world = new World();
      $worldLoginData = $world->getData($login);

      echo json_encode($worldLoginData);

      // Save to database
      $db->saveCacheData($worldLoginData, $loginKey);      
      exit;
    }
    else
    {
      echo json_encode($db->getCacheData($loginKey));
      exit;
    }



}

?>