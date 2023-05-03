<?php

session_start();

// Page created with the purpose of debugging data

include_once('functions/php/functions.php');

header('Content-Type: application/json');

// Init
$apiuser = $_ENV['TMFWEBSERVICE_USER']; 
$apipw = $_ENV['TMFWEBSERVICE_PASSWORD'];

$colorparser = new \TMFColorParser(); // Color parser
$zonesinfo = new stdClass();

// Param
$param = "World|Germany|Lower Saxony";

// Conn
// $request = new \TrackMania\WebServices\MultiplayerRankings($apiuser, $apipw);

// $data = $request->getZoneRanking($param, 0 , 10); 

$request = new \TrackMania\WebServices\Zones($apiuser, $apipw);

$data = $request->getAll(0 , 100); 

//print_r($data);                           // Object
echo json_encode($data, JSON_PRETTY_PRINT); // JSON 

exit;