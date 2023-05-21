<?php 

/**
 * Guzzle HTTP client for the Trackmania Web Services API.
 *
 * @author noiszia
 * @link https://github.com/1rives
 */
namespace TMRank;

use TMRank\TMFColorParser;
use TMRank\Utils;

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
     * Passes all the available player data including general
     * data, multiplayer ladder and solo ladder.
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

        // Player public data
        $array[] = sprintf('/tmf/players/%s/', $login);

        /// Free account validation
        $accountType = $this->request($array);

        if($accountType[0]->united == 1) 
        {
            $array[] = sprintf('/tmf/players/%s/rankings/solo/', $login);
            $multiplayerLoop = count((array)$envList);
        }
        else
        {
            $multiplayerLoop = 2;
        }
      
        // Multiplayer data
        for($i = 0; $i < $multiplayerLoop; $i++) 
        { 
            $array[] = sprintf('/tmf/players/%s/rankings/multiplayer/%s/', $login, $envList[$i]);
        }

        return self::getProcessedDataOutput(
            $this->request($array)  
        );

        //echo json_encode($response);
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
    protected function getProcessedDataOutput($rawData) 
    {
        // Create a player object
        $outputData = new \stdClass;

        self::assignPlayerData($rawData[0], $outputData);
        self::assignMultiplayerData($rawData, $outputData);

        if($rawData[0]->united) 
            self::assignSoloData($rawData, $outputData);

        return $outputData;
    }

    /**
     * Assign public player data to the player information
     * 
     * Public information of the player available on the API
     * 
     * @param array $rawData
     * @param object $outputData
     * 
     * @return void
     */
    protected function assignPlayerData($rawData, $outputData)
    {
        // Create a color parser instance
        $colorParser = new TMFColorParser();
        
        // Create a utils instance
        $utils = new Utils();

        // Get player country via array deferencing
        $playerCountry = explode('|', $rawData->path)[1];

        // Instead of -login to save data, -id should be used.
        $outputData->login = $rawData->login;
        $outputData->nickname = $colorParser->toHTML($rawData->nickname);
        $outputData->accountType = ($rawData->united) ? 'United' : 'Forever' ;
        $outputData->nation = $playerCountry;
        $outputData->flag = $utils->getFlag($playerCountry);
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

        // Sets cycle values for account type since Forever accounts only have
        // Merge and Stadium rank (Mostly Stadium)
        if($rawData[0]->united)
        {
            // All environments
            $playerEnvironments = count((array)$envList);
            $startIndex = 2;
        }
        else
        {
            // Merge and Stadium
            $playerEnvironments = 2;
            $startIndex = 1;
        }
            
        for ($i = 0; $i < $playerEnvironments; $i++)
        {
            $rawDataIndex = $startIndex + $i;
            $outputData->{strtolower($envList[$i]).'WorldRanking'} = number_format($rawData[$rawDataIndex]->ranks[0]->rank);
            $outputData->{strtolower($envList[$i]).'ZoneRanking'} = number_format($rawData[$rawDataIndex]->ranks[1]->rank);
            $outputData->{strtolower($envList[$i]).'Points'} = number_format($rawData[$rawDataIndex]->points);
        }
        
    }

    /**
     * Assign solo points and rank to the player information
     *
     * Checks the account type and assign the corresponding data, this only
     * should be called when the player's account is United
     * 
     * @param object $rawData
     * @param object $outputData
     * 
     * @return void
     */
    protected function assignSoloData($rawData, $outputData)
    {
        $accountType = $rawData[0]->united;
        $points = $rawData[1]->points;
        $ranking = $rawData[1]->ranks[0]->rank;

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