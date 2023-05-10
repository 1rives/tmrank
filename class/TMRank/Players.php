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
	 * Every Trackmania Forever environment including
     * merge: a general ranking taking in consideration all envs.
	 * 
	 * @var string
	 */
    protected $environments = array(
        'Merge',
        'Stadium',
        'Desert',
        'Island',
        'Rally',
        'Coast',
        'Bay',
        'Snow'
    );

    /**
     * Get the player data from the API.
     *
     * Passes all the available player data.
     *
     * @param string $login Player login
     * 
     * @return \stdClass Array containing URL paths
     * @throws \GuzzleHttp\Exception\ClientException
     **/
    public function getData($login) 
    {
        // Get the environments list
        $envList = $this->environments;

        for($i = 0; $i < count((array)$envList); $i++) 
        { 
            $array[] = sprintf('/tmf/players/%s/rankings/multiplayer/%s/', $login, $envList[$i]);
        }

        // Player public & solo data
        $array[] = sprintf('/tmf/players/%s/', $login);
        $array[] = sprintf('/tmf/players/%s/rankings/solo/', $login);
        
        return self::assignPlayerInfo(
            $this->request($array)  
        );
    }

    /**
     * Sanitizes and format the data to an object.
     * 
     * After requesting all the player's data from the API, processes the result to a 
     * new object.
     * 
     * @param object $rawData Player public info, multiplayer and solo rankings
     *
     * @return \stdClass Organized player data
     */
    protected function assignPlayerInfo($rawData) 
    {
        // Create a player object
        $outputData = new \stdClass;

        self::assignPlayerData($rawData[8], $outputData);
        self::assignMultiplayerData($rawData, $outputData);
        self::assignSoloData($rawData, $outputData);

        return $outputData;
    }

    /**
     * Assign public player data to the player information
     * 
     * @param array $rawData
     * @param object $outputData
     * @return void
     */
    protected function assignPlayerData($rawData, $outputData)
    {
        // Create a color parser instance
        $colorParser = new \TMFColorParser();

        $outputData->nickname = $colorParser->toHTML($rawData->nickname);
        $outputData->accountType = ($rawData->united) ? 'United' : 'Forever' ;
        $outputData->nation = str_replace('|',', ', str_replace('World|', '', $rawData->path));
        
    }

    /**
     * Assign all environments data to the player information
     *
     * United accounts have access to all environments, meanwhile Forever
     * accounts only can access Merge and Stadium.
     * 
     * TODO: Refactor this function to filter the environments on getAll()
     * TODO: Return "Unranked" when the ranking is 0
     * 
     * @param object $rawData
     * @param object $outputData
     * 
     * @return void
     */
    protected function assignMultiplayerData($rawData, $outputData)
    {
        // Get the environments list
        $envList = $this->environments;

        // Sets loop for account type
        $rawData[8]->united ? 
            $count = count((array)$envList) : 
            $count = 2;

        for ($i = 0; $i < $count; $i++)
        {
            $outputData->{strtolower($envList[$i]).'Points'} = number_format($rawData[$i]->points);
            $outputData->{strtolower($envList[$i]).'WorldRanking'} = number_format($rawData[$i]->ranks[0]->rank);
            $outputData->{strtolower($envList[$i]).'ZoneRanking'} = number_format($rawData[$i]->ranks[1]->rank);
        }
        
    }

    /**
     * Assign solo points and rank to the player information
     *
     * Checks the account type and assign the corresponding data
     * 
     * @param object $rawData
     * @param object $outputData
     * 
     * @return void
     */
    protected function assignSoloData($rawData, $outputData)
    {
        $accountType = $rawData[8]->united;
        $points = $rawData[9]->points;
        $ranking = $rawData[9]->ranks[0]->rank;

        if($accountType == 1)
        {
            // Account is not ranked
            if ($points == 0) 
            {
                $outputData->soloPoints = $outputData->soloWorld = "Unranked";
            } 
            // Account is ranked
            else 
            {
                $outputData->soloPoints = number_format($points);
                $outputData->soloWorld = number_format($ranking);
            }
        }
        else
        {
            $outputData->soloPoints = $outputData->soloWorld = "Not an United account";
        }
        
    }

}

?>