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
            $_SESSION['errorMessage'] = "";

            if(validateLogin($login) == 0)
            {
                try
                {
                    $apiuser = $_ENV['TMFWEBSERVICE_USER'];
                    $apipw = $_ENV['TMFWEBSERVICE_PASSWORD'];

                    // Requests
                    $player = new \TrackMania\WebServices\Players($apiuser, $apipw);
                    
                    $dataPlayer = $player->get($login); // Player info
                    $dataMultirank = $player->getMultiplayerRanking($login);
                    $dataSolorank = $player->getSoloRanking($login);
                    
                    /////////////////////////////////////
                    // Parse data
                    /////////////////////////////////////

                    // Initialize
                    $colorParser = new \TMFColorParser(); // Color parser
                    $playerInfo = new stdClass();

                    // Player info
                    $playerInfo->nickname = $colorParser->toHTML($dataPlayer->nickname);
                    $playerInfo->account = $dataPlayer->united;
                    $playerInfo->nation = str_replace('World|', '', $dataPlayer->path);

                    // Multiplayer Ladder Points
                    $playerInfo->multiPoints = number_format($dataMultirank->points);
                    $playerInfo->multiWorld = number_format($dataMultirank->ranks[0]->rank);
                    $playerInfo->multiZone = number_format($dataMultirank->ranks[1]->rank);

                    // Campaign Ladder Points
                    $playerInfo->soloPoints = 'Skill Points: '.number_format($dataSolorank->points);
                    $playerInfo->soloWorld = 'World ranking: '.number_format($dataSolorank->ranks[0]->rank);

                    return $playerInfo;
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

