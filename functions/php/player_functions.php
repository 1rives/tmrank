<?php

    require_once('/var/www/html/tmrank/class/autoload.php'); // API
    require_once('/var/www/html/tmrank/class/tmfcolorparser.inc.php'); // Nickname parser
    require_once('/var/www/html/tmrank/functions/php/general_functions.php'); // General functions

    /////////////////////////////////////
    ///
    ///  PLAYER FUNCTIONS
    ///

    /**
     * Get all player data from API and save it
     * to an object.
     *
     * Uses the validateLogin function to check
     * a correct login format.
     *
     * @param string $login TMF player login
     *
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

