<?php 

/**
 * Guzzle HTTP client for the Trackmania Web Services API.
 *
 * @author noiszia
 */
namespace TMRank;

require_once('/var/www/html/tmrank/class/tmfcolorparser.inc.php');

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
     * @return \stdClass Array containing URL paths
     * @throws \GuzzleHttp\Exception\ClientException
     **/
    public function getData($login) 
    {
        $playerInfoURL = sprintf('/tmf/players/%s/', $login);
        $playerMultirankURL = sprintf('/tmf/players/%s/rankings/multiplayer/', $login);
        $playerSolorankURL = sprintf('/tmf/players/%s/rankings/solo/', $login);

        return self::assignPlayerInfo(
            $this->request([ 
                $playerInfoURL,
                $playerMultirankURL,
                $playerSolorankURL
            ])  
        );
    }

    /**
     * Sanitizes and format the data to an object.
     * 
     * After requesting all the player's data from the API, processes the result for
     * a better accesibility on an object.
     *
     * @param object $playerData Player public info, multiplayer and solo rankings
     *
     * @return \stdClass Organized player data
     */
    protected function assignPlayerInfo($playerData) 
    {
        $colorParser = new \TMFColorParser();
        $playerInfo = new \stdClass;

        // Player info
        $playerInfo->nickname = $colorParser->toHTML($playerData[0]->nickname);
        $playerInfo->account = ($playerData[0]->united) ? 'United account' : 'Forever account' ;
        $playerInfo->nation = str_replace('|',', ', str_replace('World|', '', $playerData[0]->path));

        // Multiplayer Ladder Points
        $playerInfo->multiPoints = number_format($playerData[1]->points);
        $playerInfo->multiWorld = number_format($playerData[1]->ranks[0]->rank);
        $playerInfo->multiZone = number_format($playerData[1]->ranks[1]->rank);

        // Campaign Ladder Points
        // TODO: Check if an Forever account can have solo points/ranking.
        if($playerInfo->soloPoints == 0 && $playerInfo->soloWorld == 0) 
        {
            ($playerData[0]->united == 1) ?
            $playerInfo->soloPoints = $playerInfo->soloWorld = "Account is not currently ranked." :
            $playerInfo->soloPoints = $playerInfo->soloWorld = "Not an United account.";
        } 
        else 
        {
            $playerInfo->soloPoints = number_format($playerData[2]->points);
            $playerInfo->soloWorld = number_format($playerData[2]->ranks[0]->rank);
        }

        return $playerInfo;
    }

}

?>