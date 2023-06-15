<?php 

session_start();

require_once('../../class/autoload.php'); // API

use TMRank\Utils;
use TMRank\Database;

$utils = new Utils();
$db = new Database();

if($_SERVER['REQUEST_METHOD'] == 'GET')
{
  $redisKey = $db->getCurrentRequestRedisKey(null);

  $db->returnAJAXRequest(null, $redisKey);
}

?>