<?php 

require_once('../../class/autoload.php'); // API

use TMRank\Utils;
use TMRank\Database;

$utils = new Utils();
$db = new Database();

if($_SERVER['REQUEST_METHOD'] == 'GET')
{
  // Validates the login first
  if($_GET['login'])
    $utils->validateLogin($_GET['login']);

  // Player login
  $login = $_GET['login'];

  $redisKey = $db->getCurrentRequestRedisKey($login);

  $db->returnAJAXRequest($login, $redisKey);
}
?>