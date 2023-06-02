<?php 

/**
 * Guzzle HTTP client for the Trackmania Web Services API.
 *
 * @author noiszia
 * @link https://github.com/1rives
 */
namespace TMRank;

require '/var/www/html/tmrank/vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Promise\Utils;
use GuzzleHttp\Psr7\Message;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Exception\BadResponseException;

use TMRank\Database;

/**
 * HTTP client used to make asynchronous requests on the TrackMania Web Services API.
 * 
 * Created as an alternative to the 'Trackmania Web Services SDK for PHP' client.
 * 
 * Bear in mind, to use the client an TMF account is needed, get yours here: http://developers.trackmania.com
 * 
 * For more about the API in general, go to: https://forum.maniaplanet.com/viewforum.php?f=206
 */
abstract class TMRankClient 
{
    /**
	 * URL of the public Trackmania public API
	 * 
	 * @var string
	 */
    protected $apiURL = 'http://ws.trackmania.com';

    /**
	 * Redis key used for the API username
	 * 
	 * @var string
	 */
    protected $apiUsernameKey = 'TMRank.username';

    /**
     * Redis key used for the API password
     * 
     * @var string
     */
    protected $apiPasswordKey = 'TMRank.password';

    /**
     * Executes HTTP request to the public API
     *
     * Makes an asynchronous request using Guzzle promises
     *
     * @param array $requestArray Single or multiples request paths 
     * 
     * @return mixed Unserialized API response
     * @throws \GuzzleHttp\Exception\ClientException 
     **/
    protected function request(array $requestArray) 
    {
        $apiURL = $this->apiURL;
        $usernameKey = $this->apiUsernameKey;
        $passwordKey = $this->apiPasswordKey;

        // Thanks to limit of 360 requests per hour, multiple users needs to be created to change
        // the used credentials when it has reached its limit. 
        // Every user has a numeral prefix at the end (From 1 to 10) with the same password.
        $apiCredentials = self::getAPICredentials($usernameKey, $passwordKey);

        // Client configuration
        $guzzleClient = new Client([
            'base_uri' => $apiURL,
            'auth' => [ 
                $apiCredentials[0], 
                $apiCredentials[1] 
            ],
            'stream' => false,
            'decode_content' => false,
            'timeout' => 10.0,
        ]);

        try 
        {
            $promises = self::getRequestData($requestArray, $guzzleClient);

            $promisesData = Utils::unwrap($promises);

            return self::convertJSONToObject($promisesData);

        } 
        catch(BadResponseException $e) 
        {
            $response = $e->getResponse()->getBody()->getContents();

            // Defining error messages
            $requestLimitError = 'Rate limit reached';
            $misspellError = 'Unkown player';

            switch(true) {
                case str_contains($response, $requestLimitError):
                    self::updateAPICredentials($usernameKey, $passwordKey);
                    break;

                    case str_contains($response, $misspellError):
                        echo 'Player does not exist';
                        break;     
                        
                        default:
                            echo json_encode($response);
                            break;
            }
        }
    }

    /**
     * Create all asynchronous requests 
     *
     * Creates a request with promises including the required
     * set of request paths
     *
     * @param array $requests Request path/s to execute
     * @param Client $guzzle Guzzle client instance
     * 
     * @return array HTTP responses
     **/
    protected function getRequestData($requests, $guzzle)
    {
        foreach($requests as $request) 
        {
            $promises[] = $guzzle->getAsync($request);
        }

        return $promises;
    }

    /**
     * Converts JSON to object
     *
     * Extracts all successfully obtained requests in the format 
     * of an array from the HTTP response
     *
     * @param array $promises Request HTTP responses
     * 
     * @return array Decoded data
     **/
    protected function convertJSONToObject($promises)
    {
        foreach($promises as $promise) 
        {
            $requests[] = json_decode($promise->getBody());
        }

        return $requests;
    }

    /**
     * Get the API Username 
     *
     * Obtain the currently used Username credential for the TMFWebServices,
     * updates credentials if empty
     *
     * @param string $usernameKey Key for the API username credential
     * @param string $passwordKey Key for the API password credential
     * 
     * @return array Current API username and password
     **/
    protected function getAPICredentials($usernameKey, $passwordKey)
    {
        $database = new Database;

        $apiUsername = $database->getCacheData($usernameKey);
        $apiPassword = $database->getCacheData($passwordKey);
    
        return array($apiUsername, $apiPassword);
    }

    /**
     * undocumented function summary
     *
     * Undocumented function long description
     *
     * @param string $usernameKey Key for the API username credential
     * @param string $passwordKey Key for the API password credential
     **/
    protected function updateAPICredentials($usernameKey, $passwordKey)
    {
        $database = new Database;

        // Not an username/password
        if(!str_contains($usernameKey, '.') && !str_contains($passwordKey, '.'))
            return false;

        $defaultUsername = 'TMFWEBSERVICE_USER_';     // Without number prefix
        $defaultPassword = 'TMFWEBSERVICE_PASSWORD_'; // Without number prefix

        // TODO: Add specific situations, ex.: Password set but not the username
        if(!$database->getCacheDataLength($usernameKey)) 
        {
            $database->saveCacheData($usernameKey, $defaultUsername . '1');
            $database->saveCacheData($passwordKey, $defaultPassword . '1');
        } 
        else 
        {
            // Obtain the current number of account used
            $username = explode($database->getCacheData($usernameKey), '_');
            $password = explode($database->getCacheData($passwordKey), '_');

            // Gets the new username
            $newUsername = sprintf($defaultUsername . "%n", intval($username)+1);
            $newPassword = sprintf($defaultPassword . "%n", intval($password)+1);

            $database->saveCacheData($usernameKey, $newUsername);
            $database->saveCacheData($passwordKey, $newPassword);

        }
    }
}


?>
