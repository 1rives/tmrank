<?php

    require_once('/var/www/html/tmrank/class/autoload.php'); // API
    require_once('/var/www/html/tmrank/class/tmfcolorparser.inc.php'); // Nickname parser
    require_once('/var/www/html/tmrank/functions/php/general_functions.php');
    require_once('/var/www/html/tmrank/functions/php/database_functions.php'); 

    /////////////////////////////////
    ///
    ///  WORLD FUNCTIONS
    ///

    /**
     * Get current top 10 based on Ladder Points and
     * load to an object.
     *
     * Submitting a valid login will show the position of
     * the player via $offset on request.
     *
     * Cached data should be updated every midnight.
     *
     * @param string $login TMF player login
     *
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
            $worldInfo = new stdClass();
            $varname = getVariableName($worldInfo);

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
                try 
                {
                    if(validateLogin($login) == 0)
                    {
                        // Get the current rank of the player for later
                        $zones_rank = $zones->getMultiplayerRankingForEnvironment($login, $environments[0]);

                        // Convert ranking to offset replacing last number for 0
                        $offset = substr_replace($zones_rank->ranks[0]->rank, '0', -1);
                    }
                } 
                catch (Exception $e) 
                {
                    $_SESSION['errorMessage'] = $e->getMessage();
                }

            }


            //////////////////////////////////////////////////////////////////////////
            //                             Getting data                             //
            //////////////////////////////////////////////////////////////////////////

            if($_SERVER['REQUEST_METHOD'] == 'POST')
            {
                // General ranking data
                $worldInfo = $world->getPlayerRanking($path, $environments[0], $offset);

                for ($x = 0; $x < 10; $x++)
                {

                    ////////////////////////////////////////////
                    //           World top 10 data            //
                    ////////////////////////////////////////////

                    // Declare class
                    ${$varname . $environments[0]}[] = new stdClass();

                    // Current rank
                    ${$varname . $environments[0]}[$x]->rank =
                        $worldInfo->players[$x]->rank;

                    // Nickname parsed to HTML
                    ${$varname . $environments[0]}[$x]->nickname =
                        $colorparser->toHTML($worldInfo->players[$x]->player->nickname);

                    // Nation
                    $temp = explode('|', $worldInfo->players[$x]->player->path); // Explode path

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
                        number_format($worldInfo->players[$x]->points) . ' LP';

                }

                // Return data player
                return ${$varname . $environments[0]};

            }
            else
            {

                // Declare array for data return
                $worldInfoAll = new stdClass();

                // Getting all the data
                for ($i = 0; $i < count($environments); $i++)
                {
                    $worldInfo = $world->getPlayerRanking($path , $environments[$i], $offset);

                    for ($x = 0; $x < 10; $x++)
                    {
                        ////////////////////////////////////////////
                        //           World top 10 data            //
                        ////////////////////////////////////////////

                        // Declare class
                        ${$varname . $environments[$i]}[] = new stdClass();

                        // Current rank
                        ${$varname . $environments[$i]}[$x]->rank =
                            $worldInfo->players[$x]->rank;

                        // Nickname parsed to HTML

                        ${$varname . $environments[$i]}[$x]->nickname =
                            $colorparser->toHTML($worldInfo->players[$x]->player->nickname);

                        // Nation
                        $temp = explode('|', $worldInfo->players[$x]->player->path); // Explode path

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
                            number_format($worldInfo->players[$x]->points) . ' LP';
                    }

                    $worldInfoAll->leaderboard[$environments[$i]] = ${$varname . $environments[$i]};
                }

                return $worldInfoAll;

            }

        }
        catch (\TrackMania\WebServices\Exception $e)
        {
            // var_dump($e->getHTTPStatusCode(), $e->getHTTPStatusMessage(), $e->getMessage());
            $_SESSION['errorMessage'] = $e->getMessage();;

            if (strcmp($_SESSION['errorMessage'], 'Unkown player') == 0) {
                $_SESSION['errorMessage'] = 'Player not found';
            }
        }
    }

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