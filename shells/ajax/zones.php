<?php 

//session_start()

require_once('../../class/autoload.php'); // API

use TMRank\Zones;
use TMRank\Utils;
use TMRank\Database;

$utils = new Utils();
$db = new Database();
$zones = new Zones();


// TODO: Refactor players and world to use a general function

if($_SERVER['REQUEST_METHOD'] == 'GET')
{

  // Values to use
  $className = $utils->getCurrentFileName();
  $classPrefix = getenv("REDIS_VARIABLE_" . strtoupper($className));

  // Redis key
  $redisKey = $classPrefix . '.ladder';

  if(!$db->getCacheDataLength($redisKey))
  {
    // Get new information
    $newAPIData = $zones->getData();
    
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