<?php 

/**
 * Guzzle HTTP client for the Trackmania Web Services API.
 *
 * @author noiszia
 * @link https://github.com/1rives
 */
namespace TMRank;

use TMRank\Players;

/**
 * General-use functions, used in multiple classes.
 */
class Utils extends TMRankClient
{
    /**
	 * Trackmania Forever countries and their correspondent
     * abbreviation. 
     * 
     * Not every country in the array is present in the game.
	 * 
     * @author https://www.xaseco.org/
	 * @var array
	 */
    protected $nations = array(
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
        'Zimbabwe' => 'ZIM'
    );

     /**
     * Returns flag name of the entered country
     *
     * If the flag image doesn't exist, returns "default"
     * 
     * @param string $country Country full name or abbreviation
     *
     * @return string Flag abbreviation or default
     */
    public function getFlag($country)
    {
        // TODO: Add word validation if needed
        return self::getCountryAbbreviation($country);
    }

    /**
     * Returns the country abbreviated name
     *
     * Used on getFlag()
     * 
     * @author https://www.xaseco.org/
     * 
     * @param string $country Country name
     * 
     * @return string Abbreviated country name
     */
    public function getCountryAbbreviation($country)
    {
        return $this->nations[$country];
    }

    /**
     * Verifies the player login
     *
     * Sanitizes the login then checks if the login is valid.
     *
     * Returns 0 for valid login.
     * 
     * @param string $login TMF player login
     *
     * @return int Result number
     */
    public function validateLogin($login)
    {
        $error = 0;

        $clearLogin = self::sanitizeLogin($login);

        try
        {
            if (empty($clearLogin))
            {
                $error = 1;
                throw new \Exception('Please enter a login');
            }

            // TODO: Research TMF login maximum length
            if (strlen($clearLogin) > 25 || strlen($clearLogin) < 3)
            {
                $error = 2;
                throw new \Exception('Length of login is not correct');
            }

            if (!preg_match('/^[a-zA-Z0-9_]*$/', $clearLogin))
            {
                $error = 3;
                throw new \Exception('Not a valid player login');
            }

            return $error;

        }
        catch (\Exception $e)
        {
            echo $e->getMessage();
            exit;
        }

    }

    /**
     * Sanitize TMF player login
     * 
     * Used in validateLogin()
     *
     * @param string $login TMF player login
     *
     * @return string Sanitized value
     */
    public function sanitizeLogin($login)
    {
        $regexRemoveAllSpaces = "'/\s+/'";

        $sanitizedLogin = stripslashes(htmlspecialchars($login));
        preg_replace($regexRemoveAllSpaces, '', $sanitizedLogin); 
        $sanitizedLogin = strtolower($sanitizedLogin); 
        
        return $sanitizedLogin;
    }

    /**
     * Generates the time left in seconds for
     * the cache to expire.
     *
     * @return int Unix timestamp
     */
    public function getTimeUntilMidnight()
    {
        $expirationTime = 'tomorrow midnight';

        $timeUntilMidnight = strtotime($expirationTime);

        return $timeUntilMidnight;
    }

    /**
     * Get the current name of the page file without the
     * extension
     *
     * @return string File name
     */
    public function getCurrentFileName()
    {
        $currentFileName = basename($_SERVER['PHP_SELF']);
        return strtolower(explode('.', $currentFileName)[1]);
    }
}

?>