<?php 

/**
 * Guzzle HTTP client for the Trackmania Web Services API.
 *
 * @author noiszia
 */
namespace TMRank;

/**
 * Access to public players data
 */
class Players extends TMRankClient
{
    /**
     * Get the player data from the API.
     *
     * Passes three different URL requests to the client
     *
     * @param string $login Player login
     * 
     * @return Array Array containing URL paths
     * @throws \GuzzleHttp\Exception\ClientException
     **/
    public function getAll($login) 
    {
        $playerInfoURL = sprintf('/tmf/players/%s/', $login);
        $playerMultirankURL = sprintf('/tmf/players/%s/rankings/multiplayer/', $login);
        $playerSolorankURL = sprintf('/tmf/players/%s/rankings/solo/', $login);

        return $this->request([ 
            $playerInfoURL,
            $playerMultirankURL,
            $playerSolorankURL
        ]);
    }
}

?>