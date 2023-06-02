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

        // Thanks to limit of 360 requests per hour, multiple users has been created to change
        // when the current user has reached its limit. 
        // Every user has a numeral prefix at the end (From 1 to 10) with the same password.
        // TODO: Change API user when out of requests

        // Client configuration
        $guzzleClient = new Client([
            'base_uri' => $apiURL,
            'auth' => [ 
                getenv('TMFWEBSERVICE_USER_1'), 
                getenv('TMFWEBSERVICE_PASSWORD') 
            ],
            'stream' => false,
            'decode_content' => false,
            'timeout' => 10.0,
        ]);

        try 
        {
            $promises = self::getData($requestArray, $guzzleClient);

            $promisesData = Utils::unwrap($promises);

            return self::convertJSONToObject($promisesData);

        } 
        catch(BadResponseException $e) 
        {
            $response = $e->getResponse()->getBody()->getContents();

            // Misspelled error
            if(str_contains($response, 'Unkown player'))
            {
                $response = 'Player does not exist';
            }

            echo json_encode($response);
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
    protected function getData($requests, $guzzle)
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



}


?>
