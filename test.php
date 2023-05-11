<?php 


session_start();

require_once('class/autoload.php'); // API

use TMRank\Players;
use TMRank\Zones;
use TMRank\World;

// Disable errors
error_reporting(E_ERROR);

$login = $_GET['login'];


if($_SERVER['REQUEST_METHOD'] == 'GET'){
        
    $login = $_GET['login'];
    // player: All player public information
    $body = "Insert an option.";

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

    $body = "Insert an option.";

    if($_POST['searchtype'] == 'player') 
    {
        if(empty($login))
        {
            return "Insert a login";
        }
        else
        {
            $player = new Players();
            return $player->getData($login);
            //print_r($body);
        }
    }

    if($_POST['searchtype'] == 'world2') 
    {
        $world = new World();
        return $world->getData(null);   
        //print_r($body);
        
    }
    if($_POST['searchtype'] == 'world1')
    {
        if(empty($login))
        {
            return $body = "Insert a login";
        }
        else
        {
            $world = new World();
            return $world->getData($login);   
            //print_r($body);
        }
        
    }
    
    if($_POST['searchtype'] == 'zone') 
    {
        $zones = new Zones();
        return $zones->getData();
    }

}

?>