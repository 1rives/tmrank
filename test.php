<?php 


session_start();

require_once('class/autoload.php'); // API

use TMRank\Players;
use TMRank\Zones;
use TMRank\World;


// 
// Created for AJAX request testing
//

// Disable errors
error_reporting(E_ERROR);

$login = $_GET['login'];


if($_SERVER['REQUEST_METHOD'] == 'GET'){
        
    // player: All player public information

    $login = $_GET['login'];

    if(empty($login))
    {
        echo json_encode("Insert a login");
    }
    else
    {
        $player = new Players();
        echo json_encode($player->getData($login));
        //print_r($body);
    }
    
}

if($_SERVER['REQUEST_METHOD'] == 'POST'){


    // world1: Every environment top 10
    // world2: Player position in Merge ladder
    // zone: All zones ranking
    $login = $_POST['login'];

    if($_POST['searchtype'] == 'player') 
    {
        if(empty($login))
        {
            echo json_encode("Insert a login");
        }
        else
        {
            $player = new Players();
            echo json_encode($player->getData($login));
            //print_r($body);
        }
    }

    if($_POST['searchtype'] == 'world2') 
    {
        $world = new World();
        echo json_encode($world->getData(null));   
        //print_r($body);
        
    }
    if($_POST['searchtype'] == 'world1')
    {
        if(empty($login))
        {
            echo json_encode($body = "Insert a login");
        }
        else
        {
            $world = new World();
            echo json_encode($world->getData($login));   
            //print_r($body);
        }
        
    }
    
    if($_POST['searchtype'] == 'zone') 
    {
        $zones = new Zones();
        echo json_encode($zones->getData());
    }

}

?>