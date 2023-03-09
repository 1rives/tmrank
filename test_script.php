<?php

session_start();

include_once('./functions/php/functions.php');

require_once('./class/autoload.php'); // API
require_once('./class/tmfcolorparser.inc.php'); // Nickname parser

//echo phpinfo();
//exit;



// Print the value
echo $value;

///////////////////////////////////
// MEMCACHED CODE
//////////////////////////////////
//$memcached = New Memcached();
//
//$memcached->getServerList();
//echo $memcached->getResultCode() . ' ';
//
//echo $memcached->getResultCode() . " ";
//
//$lol = $memcached->getServerList();
//print_r($lol);
//
////memcached->setOption(Memcached::OPT_BINARY_PROTOCOL, true);
////echo $memcached->getResultCode() . ' ';
//
//$memcached->set('key', false);
//var_dump($memcached->get('key'). ' ');      // boolean false
//var_dump($memcached->getResultCode(). ' '); // int 0 which is  Memcached::RES_SUCCESS
//
//$test = $memcached->getAllKeys();
//echo $test . ' ';
//echo $memcached->getResultCode() . ' ';

//var_dump($test);


try {
    $apiuser = $_ENV['TMFWEBSERVICE_USER'];
    $apipw = $_ENV['TMFWEBSERVICE_PASSWORD'];

    // Variables
    $offset = '0'; // Request offset
    $flag = 'default'; // Placeholder flag
    $player_environment = 'Merge';

    // Initialize
    $colorparser = new \TMFColorParser(); // Color parser
    $worldinfo = new stdClass();
    $world_varname = getVariableName($worldinfo);

    $environments = array(
        'Merge', // General ranking
        'Stadium',
        'Desert',
        'Island',
        'Rally',
        'Coast',
        'Bay',
        'Snow'
    );


    ////////////////////////////////////////
    //    Get player rank for $offset     //
    ////////////////////////////////////////

    if (isset($login)) {
        if (validateLogin($login) != 0) {
            // RIP
        } else {
            // Get the current rank of the player for later
            $player = new \TrackMania\WebServices\Players($apiuser, $apipw);
            $player_rank = $player->getMultiplayerRankingForEnvironment($login, $player_environment);

            // Convert ranking to offset replacing last number for 0
            $offset = substr_replace($player_rank->ranks[0]->rank, '0', -1);
        }

    }


    // Connection for World data
    $world = new \TrackMania\WebServices\MultiplayerRankings($apiuser, $apipw);

    //////////////////////////////////////////////////////////////////////////
    //                             Getting data                             //
    //////////////////////////////////////////////////////////////////////////

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // General ranking data
        $worldinfo = $world->getPlayerRanking('World', $environments[0], $offset);

        for ($x = 0; $x < 10; $x++) {

            ////////////////////////////////////////////
            //           World top 10 data            //
            ////////////////////////////////////////////

            // Declare class
            ${$world_varname . $environments[0]}[] = new stdClass();

            // Current rank
            ${$world_varname . $environments[0]}[$x]->rank =
                $worldinfo->players[$x]->rank;

            // Nickname parsed to HTML
            ${$world_varname . $environments[0]}[$x]->nickname =
                $colorparser->toHTML($worldinfo->players[$x]->player->nickname);

            // Nation
            $temp = explode('|', $worldinfo->players[$x]->player->path); // Explode path

            ${$world_varname . $environments[0]}[$x]->nation =
                $temp[1];

            //////////////////////////
            // Flag of the country

            // 1. First we get the 3 letter country flag
            $nation = mapCountry($temp[1]); // Always 1 for country

            // 2. Check for flag associated to country
            if (file_exists('assets/img/flag/' . $nation . '.png')) $flag = $nation; // ex.: Argentina = ARG

            // 3. Correspondent flag name
            ${$world_varname . $environments[0]}[$x]->flag = $nation;

            //
            //////////////////////////

            // Ladder Points
            ${$world_varname . $environments[0]}[$x]->points =
                number_format($worldinfo->players[$x]->points) . ' LP';

        }

        // Return data player
        return ${$world_varname . $environments[0]};

    } else {

        // Declare array for data return
        $worldinfoAll = new stdClass();

        // Getting all the data
        for ($i = 0; $i < count($environments); $i++) {
            $worldinfo = $world->getPlayerRanking('World', $environments[$i], $offset);

            for ($x = 0; $x < 10; $x++) {
                ////////////////////////////////////////////
                //           World top 10 data            //
                ////////////////////////////////////////////

                // Declare class
                ${$world_varname . $environments[$i]}[] = new stdClass();

                // Current rank
                ${$world_varname . $environments[$i]}[$x]->rank =
                    $worldinfo->players[$x]->rank;

                // Nickname parsed to HTML

                ${$world_varname . $environments[$i]}[$x]->nickname =
                    $colorparser->toHTML($worldinfo->players[$x]->player->nickname);

                // Nation
                $temp = explode('|', $worldinfo->players[$x]->player->path); // Explode path

                ${$world_varname . $environments[$i]}[$x]->nation =
                    $temp[1];

                //////////////////////////
                // Flag of the country

                // 1. First we get the 3 letter country flag
                $nation = mapCountry($temp[1]); // Always 1 for country

                // 2. Check for flag associated to country
                if (file_exists('assets/img/flag/' . $nation . '.png')) $flag = $nation; // ex.: Argentina = ARG

                // 3. Correspondent flag name
                ${$world_varname . $environments[$i]}[$x]->flag = $nation;

                //
                //////////////////////////

                // Ladder Points
                ${$world_varname . $environments[$i]}[$x]->points =
                    number_format($worldinfo->players[$x]->points) . ' LP';
            }

            $worldinfoAll->leaderboard[$environments[$i]] = ${$world_varname . $environments[$i]};
        }

        saveWorldCache($worldinfoAll);

        return "1";

    }

//            // DEBUG: Show all tables
//            for ($i = 0; $i < count($environments); $i++)
//            {
//                echo " - $environments[$i]";
//                echo '<br><br>';
//
//                for ($x = 0; $x < 10; $x++)
//                {
//
//                    print_r(${$world_varname . $environments[$i]}[$x]);
//                    echo '<br>';
//
//                }
//
//            }
} catch (\TrackMania\WebServices\Exception $e) {
    var_dump($e->getHTTPStatusCode(), $e->getHTTPStatusMessage(), $e->getMessage());
    $_SESSION['errorMessage'] = $e->getMessage();;
}

function object2file()
{
    //fopen('test', 'w');
    file_put_contents('test', date('H:i:s'));
    //fclose('test');
}



?>

