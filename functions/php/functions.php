<?php

    require_once('/var/www/html/tmrank/class/autoload.php'); // API
    require_once('/var/www/html/tmrank/class/tmfcolorparser.inc.php'); // Nickname parser

    // TODO: Refactor functions creating classes (If needed)
    // - Class for world (Separate player search for no-player search), player, zone

    ///////////////////////////////////////////////////////////////////////////


    /////////////////////////////////
    ///
    ///  CACHE FUNCTIONS
    ///

    /** Save data to redis.
     *
     * @param string $key Name of key
     *
     * @return stdClass Data obtained from redis
     * @throws RedisException
     */
    function saveCacheData($data, $key)
        {
            // Takes the variable name as value
            $host = $_ENV['REDIS_HOST'];
            $port = $_ENV['REDIS_PORT'];

            $timeout = getTimeUntilMidnight();

            // Database connection
            $redis = new Redis();
            $redis->connect($host, $port);

            $redis->set($key, encodeCacheData($data));
            $redis->expireAt($key, $timeout);

            $redis->close();
        }

    /**
     * Get data from redis.
     *
     * @param string $key Name of key
     *
     * @return stdClass Data obtained from redis
     * @throws RedisException
     */
    function getCacheData($key)
    {
        $host = $_ENV['REDIS_HOST'];
        $port = $_ENV['REDIS_PORT'];

        // Database connection
        $redis = new Redis();

        $redis->connect($host, $port);
        $data = decodeCacheData($redis->get($key));

        $redis->close();

        // For objects
        // if(strpos($data, 'stdClass'))
        //      $data = (object) $data;

        return $data;

    }

    /**
     * Get data length from redis.
     *
     * @param string $key Name of key
     *
     * @return int Length of key
     * @throws RedisException
     */
    function getCacheDataLength($key)
    {
        $host = $_ENV['REDIS_HOST'];
        $port = $_ENV['REDIS_PORT'];

        // Database connection
        $redis = new Redis();

        $redis->connect($host, $port);
        $data = $redis->strLen($key);
        $redis->close();

        return $data;
    }

    /**
     * ONLY FOR DEV
     */
    function deleteCacheData($key)
    {
        $host = $_ENV['REDIS_HOST'];
        $port = $_ENV['REDIS_PORT'];

        // Database connection
        $redis = new Redis();

        $redis->connect($host, $port);

        if($redis->exists($key))
        {
            $redis->del($key);
        }

        $redis->close();

    }

    /////////////////////////////////
    ///
    ///  GET DATA FROM API
    ///

    /**
     * Loads all player data to an object.
     *
     * Uses the validateLogin function to check
     * a correct login format.
     *
     * @param string $login TMF player login
     *
     * @author Rives <rives@outlook.jp>
     * @return object Player data
     */
    function getPlayerInfo($login)
    {
        if($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            if(validateLogin($login) != 0)
            {
                // RIP
            }
            else
            {
                // API Credentials
                $apiuser = $_ENV['TMFWEBSERVICE_USER'];
                $apipw = $_ENV['TMFWEBSERVICE_PASSWORD'];


                // Initialize
                $colorparser = new \TMFColorParser(); // Color parser
                $zonesinfo = new stdClass();

                // Connections
                $zones = new \TrackMania\WebServices\Players($apiuser, $apipw);

                // Requests
                $data_player = $zones->get($login); // Player info
                $data_multirank = $zones->getMultiplayerRanking($login); // World ranking (Player)


                try
                {

                    /////////////////////////////////////
                    // Player basic information
                    /////////////////////////////////////

                    // Nickname parsed to HTML
                    $zonesinfo->nickname =
                        $colorparser->toHTML($data_player->nickname);

                    // Account type (bool)
                    ($data_player->united) ? $zonesinfo->account = "United" : $zonesinfo->account = "Forever"; // False

                    // Player path/nation
                    $temp = str_replace('World|', '', $data_player->path);
                    $zonesinfo->nation = str_replace('|', ', ', $temp);

                    /////////////////////////////////////
                    // Player online ladder
                    /////////////////////////////////////

                    // Online Ladder Points - Merge
                    $zonesinfo->multiPoints = number_format($data_multirank->points);

                    // Having 0 Ladder Points means an unranked player
                    if($zonesinfo->multiPoints == 0)
                    {
                        $zonesinfo->multiWorld = 'Unranked';
                        $zonesinfo->multiZone = 'Unranked';
                    }
                    else
                    {
                        $zonesinfo->multiWorld = number_format($data_multirank->ranks[0]->rank);
                        $zonesinfo->multiZone = number_format($data_multirank->ranks[1]->rank);
                    }


                    /////////////////////////////////////
                    // Player solo ladder (Only United)
                    /////////////////////////////////////

                    // Free accounts doesn't have solo ladder
                    if($data_player->united)
                    {
                        $data_solorank = $zones->getSoloRanking($login);

                        // Campaign Ladder Points
                        $zonesinfo->soloPoints = 'Skill Points: '.number_format($data_solorank->points);
                        $zonesinfo->soloWorld = 'World ranking: '.number_format($data_solorank->ranks[0]->rank);
                    }
                    else
                    {
                        // Campaign Ladder Points
                        $zonesinfo->soloPoints = '';
                        $zonesinfo->soloWorld = "Not available on $zonesinfo->account account";
                    }

                    return $zonesinfo;
                }
                catch (\TrackMania\WebServices\Exception $e)
                {
                    //var_dump($e->getHTTPStatusCode(), $e->getHTTPStatusMessage(), $e->getMessage());
                    $_SESSION['errorMessage'] = $e->getMessage();

                    if (strcmp($_SESSION['errorMessage'], "Unkown player") == 0)
                    {
                        $_SESSION['errorMessage'] = "Player not found";
                    }

                }

            }
        }
    }

    /**
     * Loads current top 10 based on Ladder Points.
     *
     * Submitting a valid login will show the position of
     * the player via $offset on request.
     *
     * Cached data should be updated every midnight.
     *
     * @param string $login TMF player login
     *
     * @author Rives <rives@outlook.jp>
     * @return object Player data
     */
    function getWorldInfo($login)
    {
        try
        {
            // API Credentials
            $apiuser = $_ENV['TMFWEBSERVICE_FETCHER_USER'];
            $apipw = $_ENV['TMFWEBSERVICE_FETCHER_PASSWORD'];

            // Variables
            $offset = '0'; // Request offset
            $flag = 'default'; // Placeholder flag
            $path = 'World';

            // Initialize
            $colorparser = new \TMFColorParser(); // Color parser
            $worldinfo = new stdClass();
            $varname = getVariableName($worldinfo);

            // Connections
            $zones = new \TrackMania\WebServices\Players($apiuser, $apipw);
            $world = new \TrackMania\WebServices\MultiplayerRankings($apiuser, $apipw);

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

            if(isset($login))
            {
                if(validateLogin($login) != 0)
                {
                    // RIP
                }
                else
                {
                    // Get the current rank of the player for later
                    $zones_rank = $zones->getMultiplayerRankingForEnvironment($login, $environments[0]);

                    // Convert ranking to offset replacing last number for 0
                    $offset = substr_replace($zones_rank->ranks[0]->rank, '0', -1);
                }

            }


            //////////////////////////////////////////////////////////////////////////
            //                             Getting data                             //
            //////////////////////////////////////////////////////////////////////////

            if($_SERVER['REQUEST_METHOD'] == 'POST')
            {
                // General ranking data
                $worldinfo = $world->getPlayerRanking($path, $environments[0], $offset);

                for ($x = 0; $x < 10; $x++)
                {

                    ////////////////////////////////////////////
                    //           World top 10 data            //
                    ////////////////////////////////////////////

                    // Declare class
                    ${$varname . $environments[0]}[] = new stdClass();

                    // Current rank
                    ${$varname . $environments[0]}[$x]->rank =
                        $worldinfo->players[$x]->rank;

                    // Nickname parsed to HTML
                    ${$varname . $environments[0]}[$x]->nickname =
                        $colorparser->toHTML($worldinfo->players[$x]->player->nickname);

                    // Nation
                    $temp = explode('|', $worldinfo->players[$x]->player->path); // Explode path

                    ${$varname . $environments[0]}[$x]->nation =
                        $temp[1];

                    //////////////////////////
                    // Flag of the country

                    // 1. First we get the 3 letter country flag
                    $nation = mapCountry($temp[1]); // Always 1 for country

                    // 2. Check for flag associated to country
                    if (file_exists('assets/img/flag/' . $nation . '.png')) $flag = $nation; // ex.: Argentina = ARG

                    // 3. Correspondent flag name
                    ${$varname . $environments[0]}[$x]->flag = $nation;

                    //
                    //////////////////////////

                    // Ladder Points
                    ${$varname . $environments[0]}[$x]->points =
                        number_format($worldinfo->players[$x]->points) . ' LP';

                }

                // Return data player
                return ${$varname . $environments[0]};

            }
            else
            {

                // Declare array for data return
                $worldinfoAll = new stdClass();

                // Getting all the data
                for ($i = 0; $i < count($environments); $i++)
                {
                    $worldinfo = $world->getPlayerRanking($path , $environments[$i], $offset);

                    for ($x = 0; $x < 10; $x++)
                    {
                        ////////////////////////////////////////////
                        //           World top 10 data            //
                        ////////////////////////////////////////////

                        // Declare class
                        ${$varname . $environments[$i]}[] = new stdClass();

                        // Current rank
                        ${$varname . $environments[$i]}[$x]->rank =
                            $worldinfo->players[$x]->rank;

                        // Nickname parsed to HTML

                        ${$varname . $environments[$i]}[$x]->nickname =
                            $colorparser->toHTML($worldinfo->players[$x]->player->nickname);

                        // Nation
                        $temp = explode('|', $worldinfo->players[$x]->player->path); // Explode path

                        ${$varname . $environments[$i]}[$x]->nation =
                            $temp[1];

                        //////////////////////////
                        // Flag of the country

                        // 1. First we get the 3 letter country flag
                        $nation = mapCountry($temp[1]); // Always 1 for country

                        // 2. Check for flag associated to country
                        if (file_exists('assets/img/flag/' . $nation . '.png')) $flag = $nation; // ex.: Argentina = ARG

                        // 3. Correspondent flag name
                        ${$varname . $environments[$i]}[$x]->flag = $nation;

                        //
                        //////////////////////////

                        // Ladder Points
                        ${$varname . $environments[$i]}[$x]->points =
                            number_format($worldinfo->players[$x]->points) . ' LP';
                    }

                    $worldinfoAll->leaderboard[$environments[$i]] = ${$varname . $environments[$i]};
                }

                // Return all data
                return $worldinfoAll;

            }

            // DEBUG: Show all tables
            // for ($i = 0; $i < count($environments); $i++)
            // {
            //     echo " - $environments[$i]";
            //     echo "<br><br>";

            //     for ($x = 0; $x < 10; $x++)
            //     {

            //         print_r(${$varname . $environments[$i]}[$x]);
            //         echo '<br>';

            //     }

            // }

        }
        catch (\TrackMania\WebServices\Exception $e)
        {
            var_dump($e->getHTTPStatusCode(), $e->getHTTPStatusMessage(), $e->getMessage());
            $_SESSION['errorMessage'] = $e->getMessage();;

            if (strcmp($_SESSION['errorMessage'], 'Unkown player') == 0) {
                $_SESSION['errorMessage'] = 'Player not found';
            }
        }
    }

    /**
         * Load all zones.
         *
         * Cached data should be updated every midnight.
         *
         * @param string $login TMF player login
         *
         * @author Rives <rives@outlook.jp>
         * @return object Player data
         */
    function getZonesInfo()
    {
        try
        {
            // API Credentials
            $apiuser = $_ENV['TMFWEBSERVICE_FETCHER_USER'];
            $apipw = $_ENV['TMFWEBSERVICE_FETCHER_PASSWORD'];

            // Variables
            $api_length = 10; // Request lenght (MAX 10)
            $api_path = 'world'; // Request path
            $api_offset = 0; // Request offset

            $ladder_rank = 1;


            $api_data_quantity = 10; // Number of calls made, ex.: 10 different variables with data

            $save_start = 0; // Used for saving data
            $save_end = 10; // Used for saving data

            // Initialize
            $zonesinfoAll = new stdClass();


            //$varname = getVariableName($worldinfo);

            // Connections
            $zones = new \TrackMania\WebServices\MultiplayerRankings($apiuser, $apipw);


            //////////////////////////////////////////////////////////////////////////
            //                             Getting data                             //
            //////////////////////////////////////////////////////////////////////////



            for ($i = 0; $i < $api_data_quantity; $i++)
            {
                $zonesinfo[$i] = $zones->getZoneRanking($api_path, $api_offset, $api_length);
                $api_offset += 10;
            }

            // print_r($zonesinfo);


            // Getting all the data
            for ($i = 0; $i < $api_data_quantity; $i++)
            {
                // Data position on $zonesinfo[]
                $pos = 0;

               for ($x = $save_start; $x < $save_end; $x++)
               {
                    ////////////////////////////////////////////
                    //          Zone rank (10 pages)          //
                    ////////////////////////////////////////////

                    if(empty($zonesinfo[$i]->zones[$pos]->zone->name))
                    {
                        // No more records available
                        // - Without this will keep saving empty data
                        $i = $api_data_quantity;
                        $x = $api_data_quantity * 10;
                    }
                    else
                    {
                        // Define object
                        $zonesLadder[$x] = new stdClass();

                        // Current rank
                        $zonesLadder[$x]->rank =
                            $ladder_rank;

                        // Nation
                        $zonesLadder[$x]->name = $zonesinfo[$i]->zones[$pos]->zone->name;

                        //////////////////////////
                        // Flag of the country
                        //////////////////////////

                        // 1. Get flag name
                        $nation_flag = mapCountry($zonesinfo[$i]->zones[$pos]->zone->name);

                        // 2. Check for flag associated to country
                        // TODO: Make a function for dynamic ubication of assets
                        if (file_exists('../assets/img/flag/' . $nation_flag . '.png'))
                            $flag = $nation_flag;
                        else
                            $flag = 'missing';

                        // 3. Correspondent flag name
                        $zonesLadder[$x]->flag = $flag;
                        //
                        //////////////////////////

                        // Ladder Points
                        $zonesLadder[$x]->points =
                            $zonesinfo[$i]->zones[$pos]->points . ' LP';


                        // Save obtained data
                        $zonesinfoAll->ladder[$x] = $zonesLadder[$x];

                        $ladder_rank++;
                        $pos++;

                        //print_r($zonesinfoAll->ladder[$x]);
                        //echo " //////////////////////////////// ";
                        //exit;
                    }
               }

               //print_r($zonesinfoAll->ladder);
               //var_dump($save_start, $save_end);
               $save_start += 10;
               $save_end += 10;

            }

            return $zonesinfoAll;


        }
        catch (\TrackMania\WebServices\Exception $e)
        {
            var_dump($e->getHTTPStatusCode(), $e->getHTTPStatusMessage(), $e->getMessage());
            $_SESSION['errorMessage'] = $e->getMessage();;
        }
    }

    /////////////////////////////////
    ///
    ///  SHOW TABLE WITH DATA
    ///

    /**
     * Shows table with the top 10 players on the world
     * depending on the selected environment
     *
     * @param string $login Player login
     * @param stdClass $data World data
     * @param string $environment Trackmania environment
     *
     * @return void
     */
    function showWorldTable($login, $data)
    {

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

        $rank_text = 'Rank';
        $nickname_text = 'Nickname';
        $nation_text = 'Country';
        $ladderpoints_text = 'Ladder Points';

        if(!$data)
        {
            if(!isset($_SESSION['errorMessage']))
                echo "<p class='text-danger'>Couldn't get the top 10 for every environment, try later.</p>";
        }
        else
        {
            if(isset($login))
            {
                // First part
                echo "<div class='tab-pane fade show active' id='". $environments[0] ."' role='tabpanel' aria-labelledby='". $environments[0] ."-leaderboard'>
                        <table class='table table-bordered table-hover'>
                            <thead>
                                <tr>
                                <th class='fixedrank'>$rank_text</th>
                                <th class='fixednickname'>$nickname_text</th>
                                <th class='fixednation'>$nation_text</th>
                                <th class='fixedlp'>$ladderpoints_text</th>
                                </tr>
                            </thead>
                        <tbody>";

                // Content
                for ($x = 0; $x < 10; $x++)
                {
                    // Data structure differs for player submitted
                    $world_rank = number_format($data[$x]->rank , 0, ',', '.');
                    $world_nickname = $data[$x]->nickname;
                    $world_country = $data[$x]->nation;
                    $world_ladderpoints = $data[$x]->points;

                    echo "
                            <tr>
                                <td>$world_rank</td>
                                <td>$world_nickname</td>
                                <td>$world_country</td>
                                <td>$world_ladderpoints</td>
                            </tr>";
                }

                // End of table
                echo ' </tbody>
                </table>
                </div>';
            }
            else
            {
                for ($i = 0; $i < count($environments); $i++)
                {
                    // Sets the Merge table active
                    $activeTabs = activeTabs($i);

                    // First part
                    echo "<div class='tab-pane fade". $activeTabs . "' id='".strtolower($environments[$i])."' role='tabpanel' aria-labelledby='".strtolower($environments[$i])."-leaderboard'>
                        <table class='table table-bordered table-hover'>
                            <thead>
                                <tr>
                                    <th class='fixedrank'>$rank_text</th>
                                    <th class='fixednickname'>$nickname_text</th>
                                    <th class='fixednation'>$nation_text</th>
                                    <th class='fixedlp'>$ladderpoints_text</th>
                                </tr>
                            </thead>
                        <tbody>";

                    foreach ($data['leaderboard'][$environments[$i]] as $player)
                    {
                        $world_rank = number_format($player['rank'] , 0, ',', '.');
                        $world_nickname = $player['nickname'];
                        $world_country = $player['nation'];
                        $world_ladderpoints = $player['points'];

                        echo "
                            <tr>
                                <td>$world_rank</td>
                                <td>$world_nickname</td>
                                <td>$world_country</td>
                                <td>$world_ladderpoints</td>
                            </tr>";
                    }

                    // End of table
                    echo '</tbody>
                </table>
                </div>';
                }
            }
        }
    }

    /**
     * Shows table of the zones ladder
     *
     * @param stdClass $data World data
     *
     * @return void
     */
    function showZonesTable($data)
    {
        $array_search = 'ladder';

        $rank_text = 'Rank';
        $nation_text = 'Country';
        $ladderpoints_text = 'Ladder Points';


        if(!$data)
        {
            if(!isset($_SESSION['errorMessage']))
                echo "<p class='text-danger'>Couldn't get all zones data, try later.</p>";
        }
        else
        {
            // First part
            echo "<table id='datatable' class='display'>
                    <thead class='table-light'>
                        <tr>
                            <th class='fixedrank' scope='col'>$rank_text</th>
                            <th class='fixednickname' scope='col'>$nation_text</th>
                            <th class='fixedlp' scope='col'>$ladderpoints_text</th>
                        </tr>
                    </thead>
                <tbody>";


            foreach ($data[$array_search] as $zone)
            {
                // Data structure differs for player submitted
                $zones_rank = number_format($zone['rank'] , 0, ',', '.');
                $zones_nation = $zone['name'];
                $zones_flag = $zone['flag'];
                $zones_ladderpoints = $zone['points'];

                echo "                                                                                                                           
                 <tr>                                                                                                                     
                     <td>$zones_rank</td>                                                                                                 
                     <td><img src='assets/img/flag/$zones_flag.png' alt='$zones_nation flag' width='3%'>    " . $zones_nation . "</td>    
                     <td>$zones_ladderpoints</td>                                                                                         
             </tr>";
            }

            // End of table
            echo ' </tbody>
            </table>';
        }

    }

    /**
     * Encodes data for caching
     *
     * @param unknown_type $login Data
     *
     * @return unknown_Type Encoded data
     */
    function encodeCacheData($data)
    {
        return serialize(json_decode(json_encode($data), true));
    }

    /**
     * Decodes data for usage
     *
     * @param unknown_type $login Encoded dada
     *
     * @return unknown_Type Data
     */
    function decodeCacheData($data)
    {
        return unserialize(json_decode(json_encode($data)));
    }

    ///////////////////////////////////////////////////////////////////////////

    /////////////////////////////////
    ///
    ///  GENERAL FUNCTIONS
    ///

    /**
     * Returns parameter of defined function
     *
     * @param string Redis parameter
     *
     * @return string Parameter
     */
    function getFunctionParam($param)
    {
        $redis_world = $_ENV['REDIS_VARIABLE_WORLD'];

        // Create variables with parameters for
        // the existing functions
        $redis_world_param = "\$login";

        if(!strcmp($param, $redis_world))
            return $redis_world_param;

        return "";
    }

    /**
     * Verifies that the entered input is correct.
     *
     * The login doesn't need to be sanitized; the function sanitizes $login for good measure
     *
     * Returns 0 for valid login.
     *
     * @param string $login TMF player login
     *
     * @author Rives <rives@outlook.jp>
     * @return Int
     */
    function validateLogin($login)
    {
        // For good measure - Sanitize login
        $login_clear = sanitizeLogin($login);

        $error = 0;

        try
        {
            if (empty($login_clear) && $error == 0)
            {
                $_SESSION['errorMessage'] = 'Player login cant be empty';
                $error = 1;
                //throw new Exception("Player login cant be empty");

            }

            if (strlen($login_clear) > 20 && $error == 0)
            {
                $_SESSION['errorMessage'] = 'Lenght of login is not correct';
                $error = 2;
                //throw new Exception('Lenght of login is not correct');

            }

            if (!preg_match('/^[a-z0-9_]*$/', $login_clear) && $error == 0)
            {
                $_SESSION['errorMessage'] = 'Not a valid player login';
                $error = 3;
                //throw new Exception("Not a valid player login");

            }

            // 0 for correct, anything else is wrong
            return $error;

        }
        catch (Exception $e)
        {
            echo $e->getMessage();

        }

    }

    /**
     * Sanitize TMF player login
     *
     * Doesn't need to be called individually, already used by validateLogin()
     *
     * @param string $login TMF player login
     *
     * @author Rives <rives@outlook.jp>
     * @return string Sanitized value
     */
    function sanitizeLogin($login)
    {
        $login = stripslashes($login);
        $login = htmlspecialchars($login);

        $login = preg_replace('/\s+/', ' ', $login); // Multiples espacios por uno
        $login = preg_replace('/^((?=^)(\s*))|((\s*)(?>$))/si', "", $login); // Trim que no es trim...
        $login = strtolower($login); // Caracteres en minuscula

        //preg_replace('/\s+/', '', $login); // Remueve todos los espacios

        return $login;

    }

    /**
     * In case of submitting a player login, disables all buttons except 'General' (Merge)
     * with the CSS property 'disabled'
     *
     * @param $var Player login
     *
     * @return void
     */
    function playerDisableButton($var)
    {
        if(isset($var)) echo "hidden";
    }

    /**
     * Generates the time left in seconds for
     * the cache to expire.
     *
     * @return int
     */
    function getTimeUntilMidnight()
    {
        // For specific hour: 'today 17:20pm'
        $datetime = 'tomorrow midnight';

        $midnight = strtotime($datetime);

        return $midnight;
    }

    /**
     * FROM TMFDataFetcher v1.5B
     *
     * Ripped out of XAseco, thanks to Xymph for the effort =)
     *
     * @param unknown_type $country
     * @return unknown
     */
    function mapCountry($country)
    {
        $nations = array(
            'Afghanistan' => 'AFG',
            'Albania' => 'ALB',
            'Algeria' => 'ALG',
            'Andorra' => 'AND',
            'Angola' => 'ANG',
            'Argentina' => 'ARG',
            'Armenia' => 'ARM',
            'Aruba' => 'ARU',
            'Australia' => 'AUS',
            'Austria' => 'AUT',
            'Azerbaijan' => 'AZE',
            'Bahamas' => 'BAH',
            'Bahrain' => 'BRN',
            'Bangladesh' => 'BAN',
            'Barbados' => 'BAR',
            'Belarus' => 'BLR',
            'Belgium' => 'BEL',
            'Belize' => 'BIZ',
            'Benin' => 'BEN',
            'Bermuda' => 'BER',
            'Bhutan' => 'BHU',
            'Bolivia' => 'BOL',
            'Bosnia&Herzegovina' => 'BIH',
            'Botswana' => 'BOT',
            'Brazil' => 'BRA',
            'Brunei' => 'BRU',
            'Bulgaria' => 'BUL',
            'Burkina Faso' => 'BUR',
            'Burundi' => 'BDI',
            'Cambodia' => 'CAM',
            'Cameroon' => 'CMR',  // Original value: 'CAR'
            'Canada' => 'CAN',
            'Cape Verde' => 'CPV',
            'Central African Republic' => 'CAF',
            'Chad' => 'CHA',
            'Chile' => 'CHI',
            'China' => 'CHN',
            'Chinese Taipei' => 'TPE',
            'Colombia' => 'COL',
            'Congo' => 'CGO',
            'Costa Rica' => 'CRC',
            'Croatia' => 'CRO',
            'Cuba' => 'CUB',
            'Cyprus' => 'CYP',
            'Czech Republic' => 'CZE',
            'Czech republic' => 'CZE',
            'DR Congo' => 'COD',
            'Denmark' => 'DEN',
            'Djibouti' => 'DJI',
            'Dominica' => 'DMA',
            'Dominican Republic' => 'DOM',
            'Ecuador' => 'ECU',
            'Egypt' => 'EGY',
            'El Salvador' => 'ESA',
            'Eritrea' => 'ERI',
            'Estonia' => 'EST',
            'Ethiopia' => 'ETH',
            'Fiji' => 'FIJ',
            'Finland' => 'FIN',
            'France' => 'FRA',
            'Gabon' => 'GAB',
            'Gambia' => 'GAM',
            'Georgia' => 'GEO',
            'Germany' => 'GER',
            'Ghana' => 'GHA',
            'Greece' => 'GRE',
            'Grenada' => 'GRN',
            'Guam' => 'GUM',
            'Guatemala' => 'GUA',
            'Guinea' => 'GUI',
            'Guinea-Bissau' => 'GBS',
            'Guyana' => 'GUY',
            'Haiti' => 'HAI',
            'Honduras' => 'HON',
            'Hong Kong' => 'HKG',
            'Hungary' => 'HUN',
            'Iceland' => 'ISL',
            'India' => 'IND',
            'Indonesia' => 'INA',
            'Iran' => 'IRI',
            'Iraq' => 'IRQ',
            'Ireland' => 'IRL',
            'Israel' => 'ISR',
            'Italy' => 'ITA',
            'Ivory Coast' => 'CIV',
            'Jamaica' => 'JAM',
            'Japan' => 'JPN',
            'Jordan' => 'JOR',
            'Kazakhstan' => 'KAZ',
            'Kenya' => 'KEN',
            'Kiribati' => 'KIR',
            'Korea' => 'KOR',
            'Kuwait' => 'KUW',
            'Kyrgyzstan' => 'KGZ',
            'Laos' => 'LAO',
            'Latvia' => 'LAT',
            'Lebanon' => 'LIB',
            'Lesotho' => 'LES',
            'Liberia' => 'LBR',
            'Libya' => 'LBA',
            'Liechtenstein' => 'LIE',
            'Lithuania' => 'LTU',
            'Luxembourg' => 'LUX',
            'Macedonia' => 'MKD',
            'Malawi' => 'MAW',
            'Malaysia' => 'MAS',
            'Mali' => 'MLI',
            'Malta' => 'MLT',
            'Mauritania' => 'MTN',
            'Mauritius' => 'MRI',
            'Mexico' => 'MEX',
            'Moldova' => 'MDA',
            'Monaco' => 'MON',
            'Mongolia' => 'MGL',
            'Montenegro' => 'MNE',
            'Morocco' => 'MAR',
            'Mozambique' => 'MOZ',
            'Myanmar' => 'MYA',
            'Namibia' => 'NAM',
            'Nauru' => 'NRU',
            'Nepal' => 'NEP',
            'Netherlands' => 'NED',
            'New Zealand' => 'NZL',
            'Nicaragua' => 'NCA',
            'Niger' => 'NIG',
            'Nigeria' => 'NGR',
            'Norway' => 'NOR',
            'Oman' => 'OMA',
            'Other Countries' => 'OTH',
            'Pakistan' => 'PAK',
            'Palau' => 'PLW',
            'Palestine' => 'PLE',
            'Panama' => 'PAN',
            'Paraguay' => 'PAR',
            'Peru' => 'PER',
            'Philippines' => 'PHI',
            'Poland' => 'POL',
            'Portugal' => 'POR',
            'Puerto Rico' => 'PUR',
            'Qatar' => 'QAT',
            'Romania' => 'ROU',  // Original value: 'ROM'
            'Russia' => 'RUS',
            'Rwanda' => 'RWA',
            'Samoa' => 'SAM',
            'San Marino' => 'SMR',
            'Saudi Arabia' => 'KSA',
            'Senegal' => 'SEN',
            'Serbia' => 'SRB',  // // Original value: 'SCG'
            'Sierra Leone' => 'SLE',
            'Singapore' => 'SIN',
            'Slovakia' => 'SVK',
            'Slovenia' => 'SLO',
            'Somalia' => 'SOM',
            'South Africa' => 'RSA',
            'Spain' => 'ESP',
            'Sri Lanka' => 'SRI',
            'Sudan' => 'SUD',
            'Suriname' => 'SUR',
            'Swaziland' => 'SWZ',
            'Sweden' => 'SWE',
            'Switzerland' => 'SUI',
            'Syria' => 'SYR',
            'Taiwan' => 'TWN',
            'Tajikistan' => 'TJK',
            'Tanzania' => 'TAN',
            'Thailand' => 'THA',
            'Togo' => 'TOG',
            'Tonga' => 'TGA',
            'Trinidad and Tobago' => 'TRI',
            'Tunisia' => 'TUN',
            'Turkey' => 'TUR',
            'Turkmenistan' => 'TKM',
            'Tuvalu' => 'TUV',
            'Uganda' => 'UGA',
            'Ukraine' => 'UKR',
            'United Arab Emirates' => 'UAE',
            'United Kingdom' => 'GBR',
            'United States of America' => 'USA',
            'Uruguay' => 'URU',
            'Uzbekistan' => 'UZB',
            'Vanuatu' => 'VAN',
            'Venezuela' => 'VEN',
            'Vietnam' => 'VIE',
            'Yemen' => 'YEM',
            'Zambia' => 'ZAM',
            'Zimbabwe' => 'ZIM',
        );

        if (array_key_exists($country, $nations))
        {
            $nation = $nations[$country];
        }
        else
        {
            $nation = 'missing';
        }
        return $nation;
    }

    /**
     * Returns the name of entered variable.
     *
     * @param var $var
     *
     * @return false|int|string Variable name
     */
    function getVariableName($var)
    {
        foreach($GLOBALS as $demo => $value)
        {
            if ($value === $var)
            {
                return $demo;
            }
        }
        return false;
    }

    /**
     * Returns CSS properties for default tab (First)
     *
     * @param $number Position
     *
     * @return string CSS properties
     */
    function activeTabs($number)
    {
        if ($number == 0)
                return " show active";
            else
                return "";
    }

    /**
     * DEV: Get type of request from CRON
     *
     * @param $request_type
     * @return void
     */
    function getRequest($request_type)
    {
        $request_string = "Unknown";

        if($request_type == 1)
        {
            $request_string = "Midnight update";
        }
        if($request_type == 2)
        {
            $request_string = "Check inside update hour";
        }
        if($request_type == 3)
        {
            $request_string = "Check outside update hour";
        }
//        if(empty($request_type))
//        {
//            $request_string = 'No request';
//            $request_type = 'null';
//        }

        echo "Type of request: $request_string ($request_type)";

    }