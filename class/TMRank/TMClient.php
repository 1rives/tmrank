<?php 

/**
 * Guzzle HTTP client for the Trackmania Web Services API.
 *
 * @author noiszia
 */
namespace TMRank;

use GuzzleHttp\Client;
use GuzzleHttp\Promise;
use GuzzleHttp\Psr7;
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
abstract class TMRankClient {

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
     * @param array $userRequests Single or multiples request paths 
     * 
     * @return mixed Unserialized API response
     * @throws \GuzzleHttp\Exception\ClientException 
     **/
    protected function requestData(array $userRequests) {

        $apiURL = $this->apiURL;

        // Client configuration
        $client = new Client([
            'base_uri' => $apiURL,
            'auth' => [ 
                getenv('TMFWEBSERVICE_USER'), 
                getenv('TMFWEBSERVICE_PASSWORD') 
            ],
            'timeout' => 10.0,
        ]);

        try 
        {
            // Create all asynchronous requests
            foreach($userRequests as $userRequest) 
            {
                $promises[] = $client->getAsync($userRequest);
            }

            $requestResults = Promise\Utils::unwrap($promises);

            foreach($requestResults as $requestResult) 
            {
                $requestBodies[] = $requestResult->getBody();
            }
            
            return $requestBodies;

        } 
        catch(ClientException $e) 
        {
            echo Psr7\Message::toString($e->getRequest());
            echo Psr7\Message::toString($e->getResponse());
        }
  }

}

?>
