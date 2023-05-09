<?php 

/**
 * Guzzle HTTP client for the Trackmania Web Services API.
 *
 * @author noiszia
 */
namespace TMRank;

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
     * @return array Array containing URL paths
     * @throws \GuzzleHttp\Exception\ClientException
     **/
    public function getData($login) 
    {
        if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])){
            return self::getLoginRanking($login);
        }
        else{
            return self::getWorldRanking();
        }
    }
    
    /**
     * Get the world data from the API.
     *
     * Passes a player ranking in the 'merge' environment
     *
     * @param string $login Player login
     * 
     * @return array Array containing URL paths
     * @throws \GuzzleHttp\Exception\ClientException
     **/
    protected function getLoginRanking($login) 
    {
        // START THIS !!!!
    }

    /**
     * Get the global world data from the API.
     *
     * Passes URLs for every top 10 in the World per environment
     * 
     * @return array Array containing URL paths
     * @throws \GuzzleHttp\Exception\ClientException
     **/
    protected function getWorldRanking() 
    {

        $path = 'World';
        $offset = 0;
        $envList = $this->environments;

        $array = [];

        for ($i = 0; $i < count((array)$envList); $i++)
        {
            $array[] = sprintf('/tmf/rankings/multiplayer/players/%s/%s/?offset=%s', $path, $envList[$i], $offset);
        }

        return \TMRank\TMRankClient::request($array);
    }

    protected function getPlayerOffset($login)
    {
        // FIX THIS !!!!
        $envList = $this->environments;

        $array = [];

        if(isset($login))
        {
            try 
            {
                if(validateLogin($login) == 0)
                {
                    // Get the current rank of the player for later
                    $zones_rank = $zones->getMultiplayerRankingForEnvironment($login, $environments[0]);

                    // Convert ranking to offset replacing last number for 0
                    $offset = substr_replace($zones_rank->ranks[0]->rank, '0', -1);
                }
            } 
            catch (Exception $e) 
            {
                $_SESSION['errorMessage'] = $e->getMessage();
            }

        }

        return $array;

    }
}

?>