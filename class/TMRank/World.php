<?php 

/**
 * Guzzle HTTP client for the Trackmania Web Services API.
 *
 * @author noiszia
 */
namespace TMRank;
use TMFColorParser;

require 'vendor/autoload.php';
require_once('/var/www/html/tmrank/class/tmfcolorparser.inc.php');

use GuzzleHttp\Client;
use GuzzleHttp\Promise\Utils;
use GuzzleHttp\Psr7\Message;
use GuzzleHttp\Exception\ClientException;


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
     * @return array containing URL paths
     * @throws \GuzzleHttp\Exception\ClientException
     **/
    public function getData($login) 
    {
        if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])){
            return self::getLoginRanking($login);
        }
        else{
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
     * @return object Array containing URL paths
     * @throws \GuzzleHttp\Exception\ClientException
     **/
    protected function getLoginRanking($login) 
    {
        $validate = new \TMRank\Utils();

        if($validate->validateLogin($login) == 0)
        {
            $path = 'World';
            $playerOffset = self::getPlayerOffset($login);
            $envList = $this->environments;

            $playerData = \TMRank\TMRankClient::request([
                sprintf('/tmf/rankings/multiplayer/players/%s/%s/?offset=%s', $path, $envList[0], $playerOffset)
            ]);

            return self::assignPlayerInfo($playerData);
        }
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
    protected function updateWorldRanking() 
    {
        $path = 'World';
        $offset = 0;
        $envList = $this->environments;

        $array = [];

        for ($i = 0; $i < count((array)$envList); $i++)
        {
            // TODO: Create and implement assignWorldInfo();
            $array[] = sprintf('/tmf/rankings/multiplayer/players/%s/%s/?offset=%s', $path, $envList[$i], $offset);
        }

        $worldData = \TMRank\TMRankClient::request($array);

        return self::assignWorldInfo($worldData);
    }

    /**
     * Sanitizes and format the data to an object.
     * 
     * Obtains the 10 players taking in consideration the ranking of the player.
     *
     * @param object $playerData Player public info
     * @return \stdClass Organized player data
     */
    protected function assignPlayerInfo($playerData) 
    {
        $colorparser = new TMFColorParser();

        for ($x = 0; $x < 10; $x++)
        {
            $worldMerge[] = new \stdClass();

            // Get player country via array deferencing
            $playerCountry = explode('|', $playerData[0]->players[$x]->player->path)[1];

            $worldMerge[$x]->rank = $playerData[0]->players[$x]->rank;
            $worldMerge[$x]->nickname = $colorparser->toHTML($playerData[0]->players[$x]->player->nickname);
            $worldMerge[$x]->nation = $playerCountry;
            $worldMerge[$x]->flag = self::getPlayerFlag($playerCountry);
            $worldMerge[$x]->points = number_format($playerData[0]->players[$x]->points) . ' LP';
        }

        return $worldMerge;

    }

    /**
     * Sanitizes and format the data to an object.
     * 
     * Obtains the top 10 players for every environment.
     *
     * @param object $worldData Top 10 players for every environment
     * @return \stdClass Organized world data
     */
    protected function assignWorldInfo($worldData) 
    {
        $colorparser = new TMFColorParser();
        $worldInfoAll = new \stdClass();
        $envList = $this->environments;

        for ($i = 0;$i < count((array)$envList); $i++) 
        { 
            for ($x = 0; $x < 10; $x++)
            {
                ${$envList[$i]}[] = new \stdClass();

                // Get player country via array deferencing
                $playerCountry = explode('|', $worldData[0]->players[$x]->player->path)[1];

                ${$envList[$i]}[$x]->rank = $worldData[$i]->players[$x]->rank;
                ${$envList[$i]}[$x]->nickname = $colorparser->toHTML($worldData[$i]->players[$x]->player->nickname);
                ${$envList[$i]}[$x]->nation = $playerCountry;
                ${$envList[$i]}[$x]->flag = self::getPlayerFlag($playerCountry);
                ${$envList[$i]}[$x]->points = number_format($worldData[$i]->players[$x]->points) . ' LP';
                
            }
            $worldInfoAll->{strtolower($envList[$i])} = ${$envList[$i]};
        }

        return $worldInfoAll;
    
    }

     /**
     * Returns the player position in the Merge ladder
     *
     * Makes a request to get the page position of the player,
     * replacing the last value to 0.
     * 
     * @return array Array containing URL paths
     * @throws \GuzzleHttp\Exception\ClientException
     **/
    protected function getPlayerOffset($login)
    {
        $envList = $this->environments;

        try 
        {
            $requestOffset = \TMRank\TMRankClient::request([sprintf('/tmf/players/%s/rankings/multiplayer/%s/', $login, $envList[0])]);
        } 
        catch (\Exception $e) 
        {
            $_SESSION['errorMessage'] = $e->getMessage();
        }
    
        // Replace last number
        $offset = substr_replace($requestOffset[0]->ranks[0]->rank, '0', -1);

        return $offset;

    }

    /**
     * Returns flag name of the player country
     *
     * If the flag image doesn't exist, returns "default"
     * 
     * @param string $playerCountry Player country
     *
     * @return string Flag abbreviation
     */
    protected function getPlayerFlag($playerCountry)
    {
        $defaultFlag = 'default';

        $utils = new \TMRank\Utils();
        $flag = $utils->getFlagAbbreviation($playerCountry);

        // TODO: Possible useless function, remove if possible
        if (!file_exists('../../assets/img/flag/' . $flag . '.png')) 
        {
            $flag = $defaultFlag;
        }    

        return $flag;
    }
    
}

?>