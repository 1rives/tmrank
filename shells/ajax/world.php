<?php 

//session_start()

require_once('../../class/autoload.php'); // API

use TMRank\World;
use TMRank\Utils;
use TMRank\Database;

// Used for caching data
$utils = new Utils();
$db = new Database();
$world = new World();

if($_SERVER['REQUEST_METHOD'] == 'GET')
{
  // First validate the login
  $utils->validateLogin($_GET['login']);

  // Values to use
  $login = $utils->sanitizeLogin($_GET['login']);
  $classPrefix = getenv('REDIS_VARIABLE_WORLD');
  
  // Name for the key used to cache data
  $redisKey = $classPrefix . '.' . $login;

  if(!$db->getCacheDataLength($redisKey))
  {
    
    // Get new information
    $processedAPIData = $world->getData($login);
    
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