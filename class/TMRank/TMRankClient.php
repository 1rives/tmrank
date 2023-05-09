<?php 

/**
 * Guzzle HTTP client for the Trackmania Web Services API.
 *
 * @author noiszia
 */
namespace TMRank;

require 'vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Promise\Utils;
use GuzzleHttp\Psr7\Message;
use GuzzleHttp\Exception\ClientException;

/**
 * HTTP client used to make asynchronous requests on the TrackMania Web Services API.
 * 
 * Created as an alternative to 'Trackmania Web Services SDK for PHP'.
 * 
 * Bear in mind, to use the client an API account is needed, get yours here: http://developers.trackmania.com
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

        // Client configuration
        $guzzleClient = new Client([
            'base_uri' => $apiURL,
            'auth' => [ 
                getenv('TMFWEBSERVICE_USER'), 
                getenv('TMFWEBSERVICE_PASSWORD') 
            ],
            'stream' => false,
            'decode_content' => false,
            'timeout' => 10.0,
        ]);

        try 
        {
            // Create all asynchronous requests
            foreach($requestArray as $requestString) 
            {
                $promises[] = $guzzleClient->getAsync($requestString);
            }

            $requestResults = Utils::unwrap($promises);

            foreach($requestResults as $requestResult) 
            {
                // Array to object
                $requestBodies[] = 
                    json_decode($requestResult->getBody()->getContents());
            }

            return $requestBodies;

        } 
        catch(ClientException $e) 
        {
            echo Message::toString($e->getRequest());
            echo Message::toString($e->getResponse());
        }
  }

}

?>
