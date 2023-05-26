<?php 

/**
 * Guzzle HTTP client for the Trackmania Web Services API.
 *
 * @author noiszia
 * @link https://github.com/1rives
 */
namespace TMRank;

use GuzzleHttp\Psr7\Message;
use GuzzleHttp\Exception\ClientException;
use TMRank\Utils;
use TMRank\Database;
use TMRank\TMFColorParser;

/**
 * Access to public world ranking data
 */
class World extends TMRankClient {

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
     * Get the world data from the API.
     *
     * Chooses between getting data through a player login or
     * the top 10 for every environment
     *      
     * @param string $login Player login
     * 
     * @return object
     * @throws \GuzzleHttp\Exception\ClientException
     **/
    public function getData($login) 
    {
        if($_SERVER['REQUEST_METHOD'] == 'GET' && !empty($_GET['login']))
        {
            return self::getLoginRanking($login);
        }
        else
        {
            return self::updateWorldRanking();
        }
    }
    
    
    /**
     * Get the world data from the API.
     *
     * Passes a player ranking in the 'merge' environment
     *
     * @param string $login Player login
     * 
     * @return \stdClass

     * 
     * @throws \GuzzleHttp\Exception\ClientException
     **/
    protected function getLoginRanking($login) 
    {
        // Get the environments list
        $envList = $this->environments;

        // Default options
        $path = 'World';

        // TODO: Refactor or change function, function makes request two times slower
        $offset = self::getPlayerOffset($login);

        if($offset) {
            $playerData = \TMRank\TMRankClient::request([
                sprintf('/tmf/rankings/multiplayer/players/%s/%s/?offset=%s', $path, $envList[0], $offset)
            ]);

            return self::getProcessedPlayerData($playerData);
        }

        // Returns 0 since the player hasn't played ever
        return $offset;
        
    }

    /**
     * Get the global world data from the API.
     *
     * Passes URLs for every top 10 in the World per environment
     * for database updates
     * 
     * @return object Unprocessed data
     * @throws \GuzzleHttp\Exception\ClientException
     **/
    public function updateWorldRanking() 
    {
        // Get the environments list
        $envList = $this->environments;

        // Default options
        $path = 'World';
        $offset = 0;

        for ($i = 0; $i < count((array)$envList); $i++)
        {
            $array[] = sprintf('/tmf/rankings/multiplayer/players/%s/%s/?offset=%s', $path, $envList[$i], $offset);
        }

        $worldData = \TMRank\TMRankClient::request($array);

        return self::getProcessedWorldData($worldData);
    }

    /**
     * Sanitizes and format the data to an object.
     * 
     * Obtains the 10 players taking in consideration the ranking of the player.
     *
     * @param object $rawPlayerData Player ranking data
     * @return (array)\stdClass Organized player data
     */
    protected function getProcessedPlayerData($rawPlayerData) 
    {
        return self::assignPlayerData($rawPlayerData);
    }

    /**
     * Sanitizes and format the data to an object.
     * 
     * Processes an object with all environments, each one has 10 positions.
     *
     * @param object $worldData Top 10 players for every environment
     * @return \stdClass Organized world data
     */
    protected function getProcessedWorldData($rawWorldData) 
    {
        return self::assignWorldData($rawWorldData);
    }

    /**
     * Sanitizes and format the data to an object.
     * 
     * Obtains the 10 players taking in consideration the ranking of the player.
     *
     * @param object $rawPlayerData Player public info
     * @return (array)stdClass Organized player data
     */
    protected function assignPlayerData($rawPlayerData) 
    {
        // Create a color parser instance
        $colorParser = new TMFColorParser();

        // Create a utils instance
        $utils = new Utils();

        for ($x = 0; $x < 10; $x++)
        {
            // Get player country via array deferencing
            $playerCountry = explode('|', $rawPlayerData[0]->players[$x]->player->path)[1];

            $playerOutputData[] = new \stdClass();

            $playerOutputData[$x]->rank = number_format($rawPlayerData[0]->players[$x]->rank);
            $playerOutputData[$x]->nickname = $colorParser->toHTML($rawPlayerData[0]->players[$x]->player->nickname);
            $playerOutputData[$x]->nation = $playerCountry;
            $playerOutputData[$x]->flag = $utils->getFlag($playerCountry);
            $playerOutputData[$x]->points = number_format($rawPlayerData[0]->players[$x]->points) . ' LP';
        }

        return $playerOutputData;
    }

    /**
     * Assign all environments data to the world information
     * 
     * Processes the data and creates an object with all environments, each one has 10 positions.
     *
     * @param object $worldData Top 10 players for every environment
     * @return \stdClass Organized world data
     */
    protected function assignWorldData($rawWorldData) 
    {
        // Create a color parser instance
        $colorparser = new TMFColorParser();

        // Create a utils instance
        $utils = new Utils();

        // Create a new object for world data
        $worldOutputData = new \stdClass();

        // Get the environments list
        $envList = $this->environments;

        for ($i = 0;$i < count((array)$envList); $i++) 
        { 
            for ($x = 0; $x < 10; $x++)
            {
                ${$envList[$i]}[] = new \stdClass();

                // Get player country via array deferencing
                $playerCountry = explode('|', $rawWorldData[$i]->players[$x]->player->path)[1];

                ${$envList[$i]}[$x]->rank = number_format($rawWorldData[$i]->players[$x]->rank);
                ${$envList[$i]}[$x]->nickname = $colorparser->toHTML($rawWorldData[$i]->players[$x]->player->nickname);
                ${$envList[$i]}[$x]->nation = $playerCountry;
                ${$envList[$i]}[$x]->flag = $utils->getFlag($playerCountry);
                ${$envList[$i]}[$x]->points = number_format($rawWorldData[$i]->players[$x]->points) . ' LP';
                
            }
            // Add processed data to object
            // named after the environment
            $worldOutputData->{strtolower($envList[$i])} = ${$envList[$i]};
        }

        return $worldOutputData;
    
    }

     /**
     * Returns the player position in the world Merge ladder
     *
     * Makes a request to get the page position of the player,
     * replacing the last value to 0.
     * 
     * Ex.: Rank 86 = Offset 80
     * 
     * @return array Array containing URL paths
     * @throws \GuzzleHttp\Exception\ClientException
     **/
    protected function getPlayerOffset($login)
    {
        $mergeEnv = $this->environments[0];

        $utils = new Utils;

        try 
        {
            $requestOffset = \TMRank\TMRankClient::request([sprintf('/tmf/players/%s/rankings/multiplayer/%s/', 
                $utils->sanitizeLogin($login), 
                $mergeEnv)]);
        } 
        catch(ClientException $e) 
        {
            $_SESSION['errorMessage'] = Message::toString($e->getResponse());
        }
    
        // Replace last number
        $offset = substr_replace($requestOffset[0]->ranks[0]->rank, '0', -1);

        return $offset;

    }

   
    
}

?>                 