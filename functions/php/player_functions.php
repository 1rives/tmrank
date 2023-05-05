<?php

    require_once('/var/www/html/tmrank/class/autoload.php'); // API
    require_once('/var/www/html/tmrank/class/tmfcolorparser.inc.php'); // Nickname parser
    require_once('/var/www/html/tmrank/functions/php/general_functions.php'); // General functions


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
                    
                    return assignPlayerInfo($dataPlayer, $dataMultirank, $dataSolorank);
                    
                }
                catch (\TrackMania\WebServices\Exception $e)
                {
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
     * Validate and saves the data to an object
     *
     * @param string $login TMF player login
     *
     * @return object Curated player data
     */
    function assignPlayerInfo($player, $multirank, $solorank) 
    {
        $colorParser = new \TMFColorParser(); // Color parser
        $playerInfo = new stdClass();

        // Player info
        $playerInfo->nickname = $colorParser->toHTML($player->nickname);
        $playerInfo->account = $player->united;
        $playerInfo->nation = str_replace('World|', '', $player->path);

        // Multiplayer Ladder Points
        $playerInfo->multiPoints = number_format($multirank->points);
        $playerInfo->multiWorld = number_format($multirank->ranks[0]->rank);
        $playerInfo->multiZone = number_format($multirank->ranks[1]->rank);

        // Campaign Ladder Points
        $playerInfo->soloPoints = 'Skill Points: '.number_format($solorank->points);
        $playerInfo->soloWorld = 'World ranking: '.number_format($solorank->ranks[0]->rank);

        return $playerInfo;
    }

