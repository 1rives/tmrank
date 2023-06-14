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
use GuzzleHttp\Exception\ConnectException;
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
	 * Redis key prefix used for the API username.
     * The account number should be added at the end of the string.
	 * 
	 * @var string
	 */
    protected $prefixUsernameKey = 'TMFWEBSERVICE_USER_';

    /**
     * Redis key prefix used for the API password.
     * The account number should be added at the end of the string.
     * 
     * @var string
     */
    protected $prefixPasswordKey = 'TMFWEBSERVICE_PASSWORD_';

    /**
     * Default account for API requests. 
     * Used altogether with the API Username/Password prefix.
     * 
     * Take in consideration that this number will only go up in value 
     * on the database when changing to a new account.
     * 
     * @var int
     */
    protected $defaultAccountNumber = 1;

    /**
     * Executes HTTP request/s to the public API
     *
     * Makes an asynchronous request using Guzzle promises
     *
     * Thanks to limit of 360 requests per hour, multiple users needs to be created 
     * in order to change the current account when no more requests can be made. 
     * 
     * @param array $requestArray Single or multiples request paths 
     * 
     * @return mixed Unserialized API response
     * 
     * @throws Exception 
     * @throws \GuzzleHttp\Exception\BadResponseException 
     **/
    protected function request(array $requestArray) 
    {
        $apiURL = $this->apiURL;
        $usernameKey = $this->apiUsernameKey;
        $passwordKey = $this->apiPasswordKey;
        $accountNumberKey = $this->apiAccountNumberKey;

        // Returns error for no requests available
        // TODO: Add validation exceptions for existing Players, 
        // World (login or general) and Zones data
        if(self::areAPIAccountsUnavailable()) 
        {
            echo json_encode("The limit for requests has been reached, please try later");
            exit;
        }

        if(!self::isAPIAccountSet($usernameKey, $passwordKey))
        {
            self::setDefaultAPICredentials($usernameKey, $passwordKey, $accountNumberKey);
        }

        try 
        {
            $apiCredentials = self::getCurrentAPICredentials($usernameKey, $passwordKey);

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
                'allow_redirects' => true,
            ]);

            $promises = self::getRequestData($requestArray, $guzzleClient);

            $promisesData = Utils::unwrap($promises);

            // TODO: Change AJAX to getJSON (jQuery)
            return self::convertJSONToObject($promisesData);

        } 
        catch(Exception | BadResponseException $e) 
        {
            $response = $e->getMessage();
            $guzzleResponse = $e->getResponse()->getBody()->getContents();


            // Defining error messages
            $misspellError = 'Unkown player';
            $requestLimitError = 'Rate limit reached';
        
            // TODO: Improve exception handling and showing errors
            switch(true) 
            {
                case str_contains($guzzleResponse, $requestLimitError):
                    // Update credentials and make again the request
                    // TODO: Change credentials and make request
                    self::updateAPICredentials($usernameKey, $passwordKey, $accountNumberKey);
                    echo "Please refresh the page and search again";
                    exit; 

                    case str_contains($response, $misspellError):
                        echo json_encode('Player does not exist');
                        exit;
                        
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
     * @param array $requests Request path or paths to execute
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
     * TODO: Refactor the AJAX request to getJSON and remove this
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
     *
     * @param string $usernameKey Key for the API username credential
     * @param string $passwordKey Key for the API password credential
     * 
     * @return array Current API username (0) and password (1)
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
     * Get the current API account number
     *
     * Obtain the account number prefix for the TMFWebServices.
     *
     * @param string $usernameKey Key for the API username credential
     * @param string $passwordKey Key for the API password credential
     * 
     * @return array Current API username (0) and password (1)
     **/
    protected function getCurrentAPIAccountNumber($accountNumberKey)
    {
        $database = new Database;

        return $database->getCacheData($accountNumberKey);
    }

    /**
     * Sets the API credentials
     *
     * Sets the first API account for requests.
     * Selects the account according to the default number prefix.
     * 
     * @param string $usernameKey Key for the API username credential
     * @param string $passwordKey Key for the API password credential
     * @param string $accountNumberKey Key for the API default account number
     **/
    protected function setDefaultAPICredentials($usernameKey, $passwordKey, $accountNumberKey)
    {
        $database = new Database;

        $usernamePrefix = $this->prefixUsernameKey;
        $passwordPrefix = $this->prefixPasswordKey;

        $accountNumber = $this->defaultAccountNumber;
        $accountUsername = $usernamePrefix . $accountNumber;
        $accountPassword = $passwordPrefix . $accountNumber;

        $database->saveCacheData(getenv($accountUsername), $usernameKey);
        $database->saveCacheData(getenv($accountPassword), $passwordKey);
        $database->saveCacheData($accountNumber, $accountNumberKey);
    }

    /**
     * Updates the API account
     *
     * Changes the credentials to the next account available based on the 
     * current account number saved on the database.
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

        // TODO: Add validation for non-existant account ($accountNumberKey)
        $accountNumber = $database->getCacheData($accountNumberKey);

        // New account prefix
        $newAccountNumber = $accountNumber + 1;

        $accountUsername = $usernamePrefix . $newAccountNumber;
        $accountPassword = $passwordPrefix . $newAccountNumber;

        $database->saveCacheData(getenv($accountUsername), $usernameKey);
        $database->saveCacheData(getenv($accountPassword), $passwordKey);
        $database->saveCacheData($newAccountNumber, $accountNumberKey);
    }

    /**
     * Checks for available API accounts to make requests
     *
     * Checks with the current account number if an account exists.
     * Not more accounts available means that all the accounts requests are used.
     * 
     * @return bool True for account found
     **/
    protected function areAPIAccountsUnavailable()
    {   
        $usernamePrefix = $this->prefixUsernameKey;

        $newAccountNumber = self::getCurrentAPIAccountNumber($this->apiAccountNumberKey)+1;

        return getenv($usernamePrefix . $newAccountNumber) ?
            false : true;
    }

    /**
     * Checks for currently used API account
     *
     * Checks for existing credentials on database.
     * 
     * @return bool True for existing account
     **/
    protected function isAPIAccountSet($usernameKey, $passwordKey)
    {   
        $apiAccount = self::getCurrentAPICredentials($usernameKey, $passwordKey);

        return ($apiAccount[0] && $apiAccount[1]) ?
            true : false;
    }

}


?>
