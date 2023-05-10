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
     * @return object Zones ranking data
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
        $zoneRank = 1; // Used for saving data, zone ranking
        $savePositionStart = 0; // Used for saving data, starting point
        $savePositionEnd = 10; // Used for saving data, end of cycle

        // Initialize
        $zonesInfoAll = new \stdClass();
        $utils = new \TMRank\Utils();

        for ($i = 0; $i < 10; $i++)
        {
            $pos = 0;

            // Defines the position where to save
            for ($x = $savePositionStart; $x < $savePositionEnd; $x++)
            {
                // Returns when no data is found
                if(empty($zonesData[$i]->zones[$pos]->zone->name))
                {
                    
                    return $zonesInfoAll;
                }
            
                $zonesLadder[$x] = new \stdClass();

                // Get player country via array deferencing
                $zoneFlag = $utils->getFlag($zonesData[$i]->zones[$pos]->zone->name);

                $zonesLadder[$x]->rank = $zoneRank;
                $zonesLadder[$x]->name = $zonesData[$i]->zones[$pos]->zone->name;
                $zonesLadder[$x]->flag = $zoneFlag;
                $zonesLadder[$x]->points = $zonesData[$i]->zones[$pos]->points;
                
                $zonesInfoAll->ladder[$x] = $zonesLadder[$x];

                $zoneRank++;
                $pos++;
            }

            $savePositionStart += 10;
            $savePositionEnd += 10;
        }

        return $zonesInfoAll;

    }


    
}

?>