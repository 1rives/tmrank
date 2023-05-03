<?php

session_start();

include 'vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Promise;

// Page created with the purpose of debugging data

// include_once('functions/php/functions.php');
// header('Content-Type: application/json');

$apiuser = $_ENV['TMFWEBSERVICE_USER'];
$apipw = $_ENV['TMFWEBSERVICE_PASSWORD'];
$login = "dragsterboy01";

$accept = 'application/json';
$contentType = 'application/json';

// Set up the HTTP client with a base URI
$client = new Client([
    'base_uri' => 'http://ws.trackmania.com',
    'auth' => [ 
        $apiuser, $apipw 
    ],
    'headers' => [
        'accept' => $accept,
        'Content-Type' => $contentType,
    ],
    'verify' => false, // Insecure
    'protocol' => 1.0, // Insecure
    'timeout' => 10.0,
]);

$playerURI = sprintf('/tmf/players/%s/', $login);
$multirankURI = sprintf('/tmf/players/%s/rankings/multiplayer/', $login);
$solorankURI = sprintf('/tmf/players/%s/rankings/solo/', $login);

try 
{
    $promises = [
        'player'    => $client->getAsync($playerURI),
        'multirank' => $client->getAsync($multirankURI),
        'solorank'  => $client->getAsync($solorankURI),
    ];
    $results = Promise\Utils::unwrap($promises);

    // Get the responses
    $body1 = $results['player']->getBody();
    $body2 = $results['multirank']->getBody();
    $body3 = $results['solorank']->getBody();

    echo $body1;
    echo $body2;
    echo $body3;
} 
catch (Exception $e) 
{
    echo 'Caught exception: ',  $e->getMessage(), "\n";
}