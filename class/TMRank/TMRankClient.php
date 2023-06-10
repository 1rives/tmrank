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
     * Redis key used for the current API account
     * 
     * @var string
     */
    protected $apiAccountNumberKey = 'TMRank.accountNumber';

     /**
	 * Redis key prefix used for the API username
	 * 
	 * @var string
	 */
    protected $prefixUsernameKey = 'TMFWEBSERVICE_USER_';

    /**
     * Redis key prefix used for the API password
     * 
     * @var string
     */
    protected $prefixPasswordKey = 'TMFWEBSERVICE_PASSWORD_';

    /**
     * Default account for API requests. 
     * Used altogether with the API Username/Password prefix.
     * 
     * @var int
     */
    protected $defaultAccountNumber = 1;


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
        $usernameKey = $this->apiUsernameKey;
        $passwordKey = $this->apiPasswordKey;

        // TODO: Create function to check if the current account exists

        try 
        {
            $apiURL = $this->apiURL;

            // Thanks to limit of 360 requests per hour, multiple users needs to be created to change
            // the used credentials when it has reached its limit. 
            // Every user has a numeral prefix at the end (From 1 to 10) with the same password.
            $apiCredentials = self::getAPICredentials($usernameKey, $passwordKey);
            
            if(!$apiCredentials)
                throw new Exception("No account");

            $apiUsername = $apiCredentials[0];
            $apiPassword = $apiCredentials[1];
           
            // Client configuration
            $guzzleClient = new Client([
                'base_uri' => $apiURL,
                'auth' => [ 
                    $apiUsername, 
                    $apiPassword 
                ],
                'stream' => false,
                'decode_content' => false,
                'timeout' => 10.0,
            ]);

            // Makes every request individually
            $promises = self::getRequestData($requestArray, $guzzleClient);

            $promisesData = Utils::unwrap($promises);

            // TODO: Change AJAX to getJSON
            return self::convertJSONToObject($promisesData);

        } 
        catch(BadResponseException $e) 
        {
            $response = $e->getResponse()->getBody()->getContents();

            // Fix misspell on the API
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

            // Main function argument (request)
            $argument = func_get_args()[0];

            // Defining error messages
            $noAccountError = 'No account';
            $requestLimitError = 'Rate limit reached';
        
            switch(true) 
            {
                case str_contains($response, $noAccountError):
                    // No account
                    // Maybe this isn't necessary at all...
                    break;

                    case str_contains($response, $requestLimitError):
                        // Updates the current account to an available one
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
     * Get the API Username and Password
     *
     * Obtain the currently used credentials for the TMFWebServices.
     * Returns fals if there's no account set.
     *
     * @param string $usernameKey Key for the API username credential
     * @param string $passwordKey Key for the API password credential
     * 
     * @return mixed Array with current API username and password, false for not found.
     **/
    protected function getCurrentAPICredentials($usernameKey, $passwordKey)
    {
        $database = new Database;

        return array(
            $database->getCacheData($usernameKey), 
            $database->getCacheData($passwordKey)
        );
    }

    /**
     * Sets the API credentials
     *
     * Sets the first API account for requests.
     * Selects the account according to the default number prefix.
     * 
     * @param string $usernameKey Key for the API username credential
     * @param string $passwordKey Key for the API password credential
     * @param int $defaultAccountKey Key for the API default account number
     **/
    protected function setDefaultAPICredentials($usernameKey, $passwordKey, $accountNumberKey)
    {
        $database = new Database;

        $usernamePrefix = $this->prefixUsernameKey;
        $passwordPrefix = $this->prefixPasswordKey;
        $accountNumber = $this->defaultAccountNumber;

        // Get the default API account credentials
        $username = getenv($usernamePrefix . $accountNumber);
        $password = getenv($passwordPrefix . $accountNumber);

        $database->saveCacheData($usernameKey, $username);
        $database->saveCacheData($passwordKey, $password);
        $database->saveCacheData($accountNumberKey, $accountNumber);
    }

    /**
     * Updates the API account
     *
     * Changes the credentials to the next account available
     * 
     * @param string $usernameKey Key for the API username credential
     * @param string $passwordKey Key for the API password credential
     * @param string $nextAccountPrefix Current API account prefix
     **/
    protected function updateAPICredentials($usernameKey, $passwordKey, $accountNumberKey)
    {
        $database = new Database;

        $usernamePrefix = $this->prefixUsernameKey;
        $passwordPrefix = $this->prefixPasswordKey;
        $accountNumber = $database->getCacheData($accountNumberKey);

        // TODO: Add validation for non-existant account ($accountNumberKey)
        $newAccountNumber = $accountNumber++;

        $database->saveCacheData($usernameKey, $usernamePrefix . $newAccountNumber);
        $database->saveCacheData($passwordKey, $passwordPrefix . $newAccountNumber);
        $database->saveCacheData($accountNumberKey, $newAccountNumber);
    }



    //  /**
    //  * Change the API credentials
    //  *
    //  * Since the current account can't be used until the next hour, an account
    //  * change is made.
    //  * 
    //  * @param string $usernameKey Key for the API username credential
    //  * @param string $passwordKey Key for the API password credential
    //  **/
    // protected function updateAPICredentials($usernameKey, $passwordKey)
    // {
    //     $database = new Database;

    //     $usernamePrefix = $this->apiUsernameKey;
    //     $passwordPrefix = $this->apiPasswordKey;

    //     // Not an username/password
    //     // if(!str_contains($usernameKey, '.') && !str_contains($passwordKey, '.'))
    //     //     return false;

    //     $currentAccount = $database->getCacheData($usernameKey);

    //     // Obtain the current number of account used
    //     // Number is the same for username and password
    //     $accountNumber = explode($currentAccount, '_')[2]++;

    //     self::checkAvailableAPIAccounts($usernamePrefix . "{$accountNumber}");

    //     $newUsername = sprintf($usernamePrefix . "%n", intval($accountNumber));
    //     $newPassword = sprintf($passwordPrefix . "%n", intval($accountNumber));

    //     $database->saveCacheData($usernameKey, $newUsername);
    //     $database->saveCacheData($passwordKey, $newPassword);
    // }

    // /**
    //  * Checks for available accounts
    //  * 
    //  * Throws an exception if there's no more accounts until the next hour
    //  *
    //  * @param string $apiUsername Complete API username account with number prefix 
    //  * @param string $apiPassword API password for the account with number prefix 

    //  * @throws Exception
    //  **/
    // protected function checkAvailableAPIAccounts($apiUsername, $apiPassword)
    // {
    //     try 
    //     {
    //         if
    //         if(self::isAPIAccountsUnavailable($apiUsername))
    //         {
    //             throw new Exception('There was an error processing the request, try later');
    //         }
        
           
    //         $usernameKey = $this->apiUsernameKey;
    //         $passwordKey = $this->apiPasswordKey;

    //         $apiCredentials = self::getAPICredentials($usernameKey, $passwordKey);

    //         $apiUsername = $apiCredentials[0];
    //         $apiPassword = $apiCredentials[1];

    //         if(!$apiUsername && !$apiPassword) 
    //         {
    //             $argument = func_get_args()[0];

    //             self::setDefaultAPICredentials($usernameKey, $passwordKey);
    //             self::request($argument);
    //         }
    //     } 
    //     catch (Exception $e) 
    //     {
    //         echo $e->getMessage();
    //     }
    // }

    /**
     * Checks for available API accounts to make requests
     *
     * Not more accounts available means that all the accounts requests are used.
     * 
     * @param string $apiUsername Complete API username account with number prefix 

     * @return bool True for exceeded requests on all accounts, false for more requests available
     **/
    protected function isAPIAccountsUnavailable($apiUsername)
    {
        return (!$apiUsername);
    }

}


?>
