<?php

    require_once('./class/autoload.php'); // API
    require_once('./class/tmfcolorparser.inc.php'); // Nickname parser

    /**
     * Shows table with the top 10 players on the world
     * depending on the selected environment
     *
     * @param string $login Player login
     * @param object $data World data
     * @param string $environment Trackmania environment
     *
     * @return void
     */
    function showWorldTable($login, $data, $environment)
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


        if(isset($login))
        {
            // First part
            echo "<div class='tab-pane fade show active' id='merge' role='tabpanel' aria-labelledby='merge-leaderboard'>
                        <table class='table table-bordered table-hover'>
                            <thead>
                                <tr>
                                    <th scope='col'>$rank_text</th>
                                    <th scope='col'>$nickname_text</th>
                                    <th scope='col'>$nation_text</th>
                                    <th scope='col'>$ladderpoints_text</th>
                                </tr>
                            </thead>";

            // Content
            for ($x = 0; $x < 10; $x++)
            {
                // Data structure differs for player submitted
                $player_rank = ordinalSuffix(number_format($data[$x]->rank , 0, ',', '.'));
                $player_nickname = $data[$x]->nickname;
                $player_country = $data[$x]->nation;
                $player_ladderpoints = $data[$x]->points;

                echo "<tbody>
                            <tr>
                                <th scope='row'>$player_rank</th>
                                <td>$player_nickname</td>
                                <td>$player_country</td>
                                <td>$player_ladderpoints</td>
                            </tr>
                        </tbody>";
            }

            // End of table
            echo '</table>
                </div>';
        }
        else
        {
            for ($i = 0; $i < count($environments); $i++)
            {
                if ($i == 0)
                    $active_tab = " show active";
                else
                    $active_tab = "";

                // echo $environments[$i];

                // First part
                echo "<div class='tab-pane fade". $active_tab . "' id='".strtolower($environments[$i])."' role='tabpanel' aria-labelledby='".strtolower($environments[$i])."-leaderboard'>
                        <table class='table table-bordered table-hover'>
                            <thead>
                                <tr>
                                    <th scope='col'>$rank_text</th>
                                    <th scope='col'>$nickname_text</th>
                                    <th scope='col'>$nation_text</th>
                                    <th scope='col'>$ladderpoints_text</th>
                                </tr>
                            </thead>";

                // Content
                for ($x = 0; $x < 10; $x++)
                {
                    $player_rank = ordinalSuffix(number_format($data->leaderboard[$environments[$i]][$x]->rank , 0, ',', '.'));
                    $player_nickname = $data->leaderboard[$environments[$i]][$x]->nickname;
                    $player_country = $data->leaderboard[$environments[$i]][$x]->nation;
                    $player_ladderpoints = $data->leaderboard[$environments[$i]][$x]->points;

                    echo "<tbody>
                            <tr>
                                <th scope='row'>$player_rank</th>
                                <td>$player_nickname</td>
                                <td>$player_country</td>
                                <td>$player_ladderpoints</td>
                            </tr>
                        </tbody>";
                }

                // End of table
                echo '</table>
                </div>';
            }
        }
    }

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
    function loadPlayerInfo($login)
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

                try
                {
                    $colorparser = new \TMFColorParser();
                    $api = new \TrackMania\WebServices\Players($apiuser, $apipw);

                    // Data from API
                    $api_user = $api->get($login); // Player info
                    $api_multirank = $api->getMultiplayerRanking($login); // World ranking (Player)

                    // Variable to return
                    $playerinfo = new stdClass();

                    /////////////////////////////////////
                    // Player basic information
                    /////////////////////////////////////

                    // Nickname parsed to HTML
                    $playerinfo->nickname =
                        $colorparser->toHTML($api_user->nickname);

                    // Account type (bool)
                    ($api_user->united) ? $playerinfo->account = "United" : $playerinfo->account = "Forever"; // False

                    // Player path/nation
                    $temp = str_replace('World|', '', $api_user->path);
                    $playerinfo->nation = str_replace('|', ', ', $temp);

                    /////////////////////////////////////
                    // Player online ladder
                    /////////////////////////////////////

                    // Online Ladder Points - Merge
                    $playerinfo->multiPoints = number_format($api_multirank->points);

                    // Having 0 Ladder Points means an unranked player
                    if($playerinfo->multiPoints == 0)
                    {
                        $playerinfo->multiWorld = 'Unranked';
                        $playerinfo->multiZone = 'Unranked';
                    }
                    else
                    {
                        $playerinfo->multiWorld = number_format($api_multirank->ranks[0]->rank);
                        $playerinfo->multiZone = number_format($api_multirank->ranks[1]->rank);
                    }


                    /////////////////////////////////////
                    // Player solo ladder (Only United)
                    /////////////////////////////////////

                    // Free accounts doesn't have solo ladder
                    if($api_user->united)
                    {
                        $api_solorank = $api->getSoloRanking($login);

                        // Campaign Ladder Points
                        $playerinfo->soloPoints = 'Skill Points: '.number_format($api_solorank->points);
                        $playerinfo->soloWorld = 'World ranking: '.number_format($api_solorank->ranks[0]->rank);
                    }
                    else
                    {
                        // Campaign Ladder Points
                        $playerinfo->soloPoints = '';
                        $playerinfo->soloWorld = "Not available on $playerinfo->account account";
                    }

                    return $playerinfo;
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
    function loadWorldInfo($login)
    {
        try
        {
            // API Credentials
            $apiuser = $_ENV['TMFWEBSERVICE_USER'];
            $apipw = $_ENV['TMFWEBSERVICE_PASSWORD'];

            // Variables
            $offset = '0'; // Request offset
            $flag = 'default'; // Placeholder flag

            // Initialize
            $colorparser = new \TMFColorParser(); // Color parser
            $worldinfo = new stdClass();

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
                    $player = new \TrackMania\WebServices\Players($apiuser, $apipw);
                    $player_rank = $player->getMultiplayerRankingForEnvironment($login,'Merge');

                    // Convert ranking to offset replacing last number for 0
                    $offset = substr_replace($player_rank->ranks[0]->rank, '0', -1);
                }

            }


            // Connection for World data
            $world = new \TrackMania\WebServices\MultiplayerRankings($apiuser, $apipw);
            $varname = 'worldinfo';

            //////////////////////////////////////////////////////////////////////////
            //                             Getting data                             //
            //////////////////////////////////////////////////////////////////////////

            if($_SERVER['REQUEST_METHOD'] == 'POST')
            {
                // General ranking data
                $worldinfo = $world->getPlayerRanking('World', $environments[0], $offset);

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
                    $worldinfo = $world->getPlayerRanking('World', $environments[$i], $offset);

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
            for ($i = 0; $i < count($environments); $i++)
            {
                echo " - $environments[$i]";
                echo "<br><br>";

                for ($x = 0; $x < 10; $x++)
                {

                    print_r(${$varname . $environments[$i]}[$x]);
                    echo '<br>';

                }

            }

        }
        catch (\TrackMania\WebServices\Exception $e)
        {
            var_dump($e->getHTTPStatusCode(), $e->getHTTPStatusMessage(), $e->getMessage());
            $_SESSION['errorMessage'] = $e->getMessage();;
        }
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
     * Simple function that replaces 0 for "unranked"
     *
     * Used to show unranked players correctly
     *
     * @param int $points Current player ladder points
     * @param int $data TMF player login
     * @param int $type TMF player login
     *
     * @author Rives <rives@outlook.jp>
     * @return string "0" or "unranked"
     */
    function showUnrankedPlayer($points, $value, $type)
    {
        // IDEA DE FUNCION
        //
        // Al tener 0 Ladder/Skill points, mostrar unranked
        // En lo posible no mostrar el ranking mundial y nacional de ser asi.

    }

    /**
     * Adds ordinal suffix to player rank
     *
     * @param $number Player ranking
     *
     * @return false|string
     */
    function ordinalSuffix($number)
    {
        $ends = array('th','st','nd','rd','th','th','th','th','th','th');
        if ((($number % 100) >= 11) && (($number%100) <= 13))
            return $number. 'th';
        else
            return $number. $ends[$number % 10];
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
            'Cameroon' => 'CAR',  // actually CMR
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
            'Romania' => 'ROM',  // actually ROU
            'Russia' => 'RUS',
            'Rwanda' => 'RWA',
            'Samoa' => 'SAM',
            'San Marino' => 'SMR',
            'Saudi Arabia' => 'KSA',
            'Senegal' => 'SEN',
            'Serbia' => 'SCG',  // actually SRB
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
            $nation = 'OTH';
        }
        return $nation;
    }
