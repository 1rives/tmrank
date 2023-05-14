<?php 

/**
 * Guzzle HTTP client for the Trackmania Web Services API.
 *
 * @author noiszia
 * @link https://github.com/1rives
 */
namespace TMRank;

use TMRank\Utils;

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
    public function updateZonesRanking() 
    {
        $path = 'World';
        $offset = 0;

        // Since the results are always 92, ten cycles is enough
        for ($i = 0; $i < 10; $i++)
        {
            $array[] = sprintf('/tmf/rankings/multiplayer/zones/%s/?offset=%s', $path, $offset);
            $offset += 10;
        }

        $rawZonesData = \TMRank\TMRankClient::request($array);

        return self::getProcessedDataOutput($rawZonesData);
    }

    /**
     * Sanitizes and format the data to an object.
     * 
     * Obtains all the zones in the ladder.
     *
     * @param object $rawZonesData Zones data
     * @return \stdClass Organized zones data
     */
    protected function getProcessedDataOutput($rawZonesData) 
    {
        return self::assignZonesData($rawZonesData);

    }

     /**
     * Assign zones data to the zones information
     * 
     * All the zones available in the API
     *
     * @param object $rawZonesData Zones info
     * @return \stdClass Organized zones data
     */
    protected function assignZonesData($rawZonesData) 
    {
        $zoneRank = 1; // Used for saving data, zone ranking
        $savePositionStart = 0; // Used for saving data, starting point
        $savePositionEnd = 10; // Used for saving data, end of cycle

        // Create a utils instance
        $utils = new Utils();
        
        // Create new Zones object 
        $zonesOutputData = new \stdClass();
        
        for ($i = 0; $i < 10; $i++)
        {
            $pos = 0;

            // Defines the position where to save
            for ($x = $savePositionStart; $x < $savePositionEnd; $x++)
            {
                // Returns when no data is found
                if(empty($rawZonesData[$i]->zones[$pos]->zone->name))
                {
                    return $zonesOutputData;
                }

                $zonesTempData[] = new \stdClass();
                

                // Get player country via array deferencing
                $zoneFlag = $utils->getFlag($rawZonesData[$i]->zones[$pos]->zone->name);

                $zonesTempData[$x]->rank = $zoneRank;
                $zonesTempData[$x]->name = $rawZonesData[$i]->zones[$pos]->zone->name;
                $zonesTempData[$x]->flag = $zoneFlag;
                $zonesTempData[$x]->points = $rawZonesData[$i]->zones[$pos]->points;

                $zonesOutputData->ladder[$x] = $zonesTempData[$x];

                $zoneRank++;
                $pos++;
            }

            $savePositionStart += 10;
            $savePositionEnd += 10;
        }

        return $zonesOutputData;

    }


    
}

?>