<?php

    require_once('/var/www/html/tmrank/class/autoload.php'); // API
    require_once('/var/www/html/tmrank/class/tmfcolorparser.inc.php'); // Nickname parser

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
                $_SESSION['errorMessage'] = 'Length of login is not correct';
                $error = 2;
                //throw new Exception('Length of login is not correct');

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
     * @return string Sanitized value
     */
    function sanitizeLogin($login)
    {
        $login = stripslashes($login);
        $login = htmlspecialchars($login);

        $login = preg_replace('/\s+/', ' ', $login); // Replace multiple spaces for one
        $login = preg_replace('/^((?=^)(\s*))|((\s*)(?>$))/si', "", $login); // Trim??...
        $login = strtolower($login); // Lowercase characters
        //preg_replace('/\s+/', '', $login); // Remove all spaces

        return $login;
    }

    /**
     * In case of submitting a player login, disables all buttons except 'General' (Merge)
     * with the CSS property 'disabled'
     *
     * TODO: Replace with JavaScript
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
     * @return int Unix timestamp
     */
    function getTimeUntilMidnight()
    {
        $expirationTime = 'tomorrow midnight';

        $timeUntilMidnight = strtotime($expirationTime);

        return $timeUntilMidnight;
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