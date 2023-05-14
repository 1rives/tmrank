<?php 

//session_start()

require_once('../../class/autoload.php'); // API

use TMRank\Players;
use TMRank\Utils;
use TMRank\Database;

// Used for caching data
$utils = new Utils();
$db = new Database();
$players = new Players();

if($_SERVER['REQUEST_METHOD'] == 'GET')
{
  // First validate the login
  $utils->validateLogin($_GET['login']);

  // Values to use
  $login = $utils->sanitizeLogin($_GET['login']);
  $classPrefix = getenv('REDIS_VARIABLE_PLAYERS');
  
  // Name for the key used to cache data
  $redisKey = $classPrefix . '.' . $login;
  
  if(!$db->getCacheDataLength($redisKey))
  {
    // Get new information
    $processedAPIData = $players->getData($login);
    
    // Save to database
    $db->saveCacheData($processedAPIData, $redisKey);   
    
    // Return AJAX data
    echo json_encode($processedAPIData); 

    exit;
  }
  else
  {
    echo json_encode($db->getCacheData($redisKey));

    exit;
  }
 
}

?>