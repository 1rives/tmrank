<?php 

//session_start()

require_once('../../class/autoload.php'); // API

use TMRank\World;
use TMRank\Utils;
use TMRank\Database;

$utils = new Utils();
$db = new Database();
$world = new World();

// TODO: Refactor players and world to use a general function

if($_SERVER['REQUEST_METHOD'] == 'GET')
{
  // First validate the login
  //$utils->validateLogin($_GET['login']);

  // Values to use
  $login = $utils->sanitizeLogin($_GET['login']);

  $className = $utils->getCurrentFileName();
  $classPrefix = getenv("REDIS_VARIABLE_" . strtoupper($className));
  
  // Redis key
  !$login ? 
    $redisKey = $classPrefix . '.ladder' :
    $redisKey = $classPrefix . '.' . $login;
  
  if(!$db->getCacheDataLength($redisKey))
  {
    // Get new information
    $newAPIData = $world->getData($login);
    
    // Save to database
    $db->saveCacheData($newAPIData, $redisKey);   
    
    // Return AJAX data
    echo json_encode($newAPIData); 
  }
  else
  {
    echo json_encode($db->getCacheData($redisKey));
  }
  
}


?>