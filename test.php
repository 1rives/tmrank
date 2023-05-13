<?php 


//session_start();

require_once('class/autoload.php'); // API

use TMRank\Players;
use TMRank\World;
use TMRank\Utils;

// 
// Created for AJAX request testing
//

// Disable errors
error_reporting(E_ERROR);


// Retrieve all keys for deletion
//
/* $redis = new Redis();
$redis->connect('127.0.0.1', 6379);

// Initialize the cursor to 0
$cursor = 0;

// Retrieve all keys with the prefix "Player."
$keys = array();
do {
  // Scan for keys with the prefix "Player." and a cursor position of $cursor
  // The SCAN command returns an array with the new cursor position and an array of matching keys
  $result = $redis->scan($cursor, 'MATCH', 'Player.*');

  // Update the cursor position
  $cursor = $result[0];

  // Add the matching keys to the $keys array
  $keys = array_merge($keys, $result[1]);
} while ($cursor != 0);

// Print the names of the matching keys
foreach ($keys as $key) {
  echo $key . "\n";
}

// Close the Redis connection
$redis->close(); */



// Used for caching data
$utils = new Utils();


if($_SERVER['REQUEST_METHOD'] == 'GET')
{
    // players: All player public information

    $login = $_GET['login'];
    $utils->validateLogin($login);

    $player = new Players();
    echo json_encode($player->getData($login));
 
}

if($_SERVER['REQUEST_METHOD'] == 'POST')
{
    // world: Player position in Merge ladder

    $login = $_POST['login'];
    $utils->validateLogin($login);

    $world = new World();
    echo json_encode($world->getData($login));   

}

?>