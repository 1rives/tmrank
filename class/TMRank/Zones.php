<?php 

/**
 * Guzzle HTTP client for the Trackmania Web Services API.
 *
 * @author noiszia
 */
namespace TMRank;

require_once('/var/www/html/tmrank/class/tmfcolorparser.inc.php');

/**
 * Access to public zones ranking data
 */
class Zones extends TMRankClient {


    /**
     * Get the zones data from the API.
     *
     * Obtains the zones world ranking
     * 
     * @return array containing URL paths
     * @throws \GuzzleHttp\Exception\ClientException
     **/
    public function getData() 
    {
        return self::updateZonesRanking();
    }

    /**
     * Get the zones data from the API.
     *
     * Passes URLs for every top 10 in the World per environment
     * for database updates
     * 
     * @return object Unprocessed data
     * @throws \GuzzleHttp\Exception\ClientException
     **/
    protected function updateZonesRanking() 
    {
        $path = 'World';
        $offset = 0;

        $array = [];

        // Since the results are always 92, ten cycles is enough
        for ($i = 0; $i < 10; $i++)
        {
            $array[] = sprintf('/tmf/rankings/multiplayer/zones/%s/?offset=%s', $path, $offset);
            $offset += 10;
        }

        $zonesData = \TMRank\TMRankClient::request($array);

        return self::assignZonesInfo($zonesData);
    }

    /**
     * Sanitizes and format the data to an object.
     * 
     * Obtains all the zones in the ladder.
     *
     * @param object $zonesData Player public info
     * @return \stdClass Organized player data
     */
    protected function assignZonesInfo($zonesData) 
    {

        // TODO: Implement the complete function in zone_functions

        $utils = new \TMRank\Utils();

        $zoneRank = 1;
        $pos = 0;

        $saveStart = 0; // Used for saving data
        $saveEnd = 10; // Used for saving data

        // Initialize
        $zonesinfoAll = new stdClass();


        for ($i = 0; $i < 10; $i++)
        {
            $zoneFlag = $utils->getFlag($zoneCountry);
            $zonesLadder[] = new \stdClass();


            // Get player country via array deferencing
            $zoneCountry = explode('|', $zonesData[0]->players[$i]->player->path)[1];

            $zonesLadder[$i]->rank = $zoneRank;
            $zonesLadder[$i]->name = $zonesData[$i]->zones[$pos]->zone->name;
            $zonesLadder[$i]->flag = getZoneFlag($zoneCountry);
            $zonesLadder[$i]->points =$zonesData[$i]->zones[$pos]->points . ' LP';


            // Save obtained data
            $zonesinfoAll->ladder[$i] = $zonesLadder[$i];

            $zoneRank++;
            $pos++;

        }

        return $zonesLadder;

    }


    
}

?>