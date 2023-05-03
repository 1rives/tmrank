<?php

    require_once('/var/www/html/tmrank/class/autoload.php'); // API
    require_once('/var/www/html/tmrank/class/tmfcolorparser.inc.php'); // Nickname parser
    require_once('/var/www/html/tmrank/functions/php/general_functions.php');
    require_once('/var/www/html/tmrank/functions/php/database_functions.php'); 


    /////////////////////////////////
    ///
    ///  ZONE FUNCTIONS
    ///

    /**
         * Get all zones from the API and save it
         * to an object
         *
         * Cached data should be updated every midnight.
         *
         * @param string $login TMF player login
         *
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
            $api_length = 10; // Request length (MAX 10)
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