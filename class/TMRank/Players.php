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

    public function getAll($login) {

        $player_infoURI = sprintf('/tmf/players/%s/', $login);
        $player_multirankURI = sprintf('/tmf/players/%s/rankings/multiplayer/', $login);
        $player_solorankURI = sprintf('/tmf/players/%s/rankings/solo/', $login);

        return $this->requestData([ 
            $player_infoURI,
            $player_multirankURI,
            $player_solorankURI
        ]);
    }

}

?>