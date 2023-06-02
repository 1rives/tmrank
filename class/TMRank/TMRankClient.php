<?php 

/**
 * Guzzle HTTP client for the Trackmania Web Services API.
 *
 * @author noiszia
 * @link https://github.com/1rives
 */
namespace TMRank;

require '/var/www/html/tmrank/vendor/autoload.php';

use Exception;
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

        try 
        {
            // Thanks to limit of 360 requests per hour, multiple users needs to be created to change
            // the used credentials when it has reached its limit. 
            // Every user has a numeral prefix at the end (From 1 to 10) with the same password.
            $apiCredentials = self::getAPICredentials($usernameKey, $passwordKey);
            
            if(!$apiCredentials)
                throw new Exception("No account", 400);

            // $usernameKey = $apiCredentials[0];
            // $passwordKey = $apiCredentials[1];


            // echo $usernameKey;

           
            // // Client configuration
            // $guzzleClient = new Client([
            //     'base_uri' => $apiURL,
            //     'auth' => [ 
            //         $usernameKey, 
            //         $passwordKey 
            //     ],
            //     'stream' => false,
            //     'decode_content' => false,
            //     'timeout' => 10.0,
            // ]);

        
            // $promises = self::getRequestData($requestArray, $guzzleClient);

            // $promisesData = Utils::unwrap($promises);

            // return self::convertJSONToObject($promisesData);
            

        } 
        catch(BadResponseException $e) 
        {
            $response = $e->getResponse()->getBody()->getContents();

            // Misspell on the API
            $misspellError = 'Unkown player';

            if(str_contains($response, $misspellError))
            {
                $response = 'Player does not exist';
            }
      
            echo json_encode($response);
        }
        catch(Exception $ex)
        {
            $response = $ex->getMessage();

            // Defining error messages
            $requestLimitError = 'Rate limit reached';
            $noAccountError = 'No account';

            switch(true) {
                case str_contains($response, $noAccountError):
                    self::setAPICredentials($apiCredentials[0], $apiCredentials[1]);
                    self::request($requestArray);
                    break;

                    case str_contains($response, $requestLimitError):
                        self::changeAPICredentials($apiCredentials[0], $apiCredentials[1]);
                        self::request($requestArray);
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
     * Sets the API credentials
     *
     * Sets the first account at the environment values as the API credentials.
     * 
     * @param string $usernameKey Key for the API username credential
     * @param string $passwordKey Key for the API password credential
     **/
    protected function setAPICredentials($usernameKey, $passwordKey)
    {
        $database = new Database;

        // Not an username/password
        // if(!str_contains($usernameKey, '.') && !str_contains($passwordKey, '.'))
        //     return false;

        $usernamePrefix = 'TMFWEBSERVICE_USER_';     // Without number prefix
        $passwordPrefix = 'TMFWEBSERVICE_PASSWORD_'; // Without number prefix

        // TODO: Add specific situations, ex.: Password set but not the username
        $database->saveCacheData($usernameKey, $usernamePrefix . '1');
        $database->saveCacheData($passwordKey, $passwordPrefix . '1');
        
  

    }

     /**
     * Change the API credentials
     *
     * Since the current account can't be used until the next hour, an account
     * change is made.
     * 
     * @param string $usernameKey Key for the API username credential
     * @param string $passwordKey Key for the API password credential
     **/
    protected function changeAPICredentials($usernameKey, $passwordKey)
    {
        $database = new Database;

        // Not an username/password
        // if(!str_contains($usernameKey, '.') && !str_contains($passwordKey, '.'))
        //     return false;

        $usernamePrefix = 'TMFWEBSERVICE_USER_';     // Without number prefix
        $passwordPrefix = 'TMFWEBSERVICE_PASSWORD_'; // Without number prefix

        $currentAccount = $database->getCacheData($usernameKey);

        // Obtain the current number of account used
        // Number is the same for username and password
        $accountNumber = explode($currentAccount, '_')[2]++;

        self::checkAvailableAPIAccounts($usernamePrefix . "{$accountNumber}");

        $newUsername = sprintf($usernamePrefix . "%n", intval($accountNumber));
        $newPassword = sprintf($passwordPrefix . "%n", intval($accountNumber));

        $database->saveCacheData($usernameKey, $newUsername);
        $database->saveCacheData($passwordKey, $newPassword);
    }

    /**
     * Checks for available accounts
     * 
     * Throws an exception if there's no more accounts until the next hour
     *
     * @param string $apiUsername Complete API username account with number prefix 

     * @throws Exception
     **/
    public function checkAvailableAPIAccounts($apiUsername)
    {
        try 
        {
            if(!getenv($apiUsername))
            {
                throw new Exception('There was an error processing the request, try later');
            }
        } 
        catch (Exception $e) 
        {
            echo $e->getMessage();
        }
    }

}


?>
