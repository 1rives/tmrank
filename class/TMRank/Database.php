<?php 

/**
 * Guzzle HTTP client for the Trackmania Web Services API.
 *
 * @author noiszia
 * @link https://github.com/1rives
 */
namespace TMRank;

use TMRank\Utils;
use TMRank\Players;
use TMRank\World;
use TMRank\Zones;

/**
 * General access to Redis database
 */
class Database extends TMRankClient
{
    /** 
     * Check existing login data on Redis.
     * 
     * Checks if data exists in the database with the class prefix and login, if is
     * empty then return
     *
     * Example of full key: Players.username777
     *
     * TODO: Check if this function is actually needed (getCacheDataLength...)
     * 
     * @param string $loginKey Player login for Redis key
     * @param string $classPrefix Class name for key prefix, ex.: "World" or "Players"
     *
     * @return bool true for success, false for existing data
     * @throws \RedisException
     */
    public function checkIfLoginExists($loginKey, $classPrefix)
    {
        $key = $classPrefix . '.' . $loginKey;

        return (!self::getCacheDataLength($key)) ? false : true;
    }

    /**
     * Processes AJAX request
     *
     * Checks for existing data in Redis and return it, if database is
     * empty make a new request from the API
     *
     * @param string $login Sanitized TMF player login
     * @param string $redisKey Used Redis key for request
     * @param string $class Used class for request
     * 
     * @return void AJAX Request
     **/
    public function returnAJAXRequest($login, $redisKey)
    {
        // Declare instances for the different classes
        $players = new Players();
        $world = new World();
        $zones = new Zones();

        // Declare a Utils instance
        $utils = new Utils();

        // Request data if empty
        if(!self::getCacheDataLength($redisKey))
        {
            // Get current class name
            $classInstance = $utils->getCurrentFileName();

            // Request with class name
            $newData = $$classInstance->getData($login);

            if($newData) {
                //self::saveCacheData($newData, $redisKey);
            }   
        
            echo json_encode($newData);
            exit();
        }
        
        // Return db data
        echo json_encode(self::getCacheData($redisKey));
    }

    /**
     * Obtain Redis key for class
     *
     * Depending on the used class, return the complete Redis key
     * to use.
     * 
     * An empty player login always return a general request (ex.: World ladder)
     *
     * @param string TMF player login
     * 
     * @return string Redis key name
     **/
    public function getCurrentRequestRedisKey($login)
    {
        // Declare a Utils instance
        $utils = new Utils;
    
        $classOfRequestMade = $utils->getCurrentFileName();
        $classPrefix = getenv("REDIS_VARIABLE_" . strtoupper($classOfRequestMade));

        // Creates the Redis key depending if it's a general request
        // or a login-specific one
        !isset($login) ? 
            $redisKey = $classPrefix . '.ladder' :
            $redisKey = $classPrefix . '.' . $utils->sanitizeLogin($login);

        return $redisKey;
    }

    /** 
     * Save cache data to redis.
     * 
     * The saved cache will expire at midnight.
     * 
     * If $key doesn't have a dot, then is not cache and the 
     * data persists by default.
     *
     * @param string $dataToSave Processed data to save
     * @param string $key Name of key for the data
     * 
     * @return bool True for successful data update
     * @throws \RedisException
     */
    public function saveCacheData($data, $key)
    {
        try 
        {
            // Declare a Utils instance
            $utils = new Utils();

            $redisHost = $_ENV['REDIS_HOST'];
            $redisPort = $_ENV['REDIS_PORT'];

            $redis = new \Redis();
            $redis->connect($redisHost, $redisPort);

            $redis->set($key, self::encodeCacheData($data));

            // Default daily caching
            if(strpos($key, '.') !== FALSE) {
                $keyTimeout = $utils->getTimeUntilMidnight();
                $redis->expireAt($key, $keyTimeout);
            }
            // Hourly caching, used mostly for API credentials
            if(strpos($key, 'TMRank.') !== FALSE) {
                $keyTimeout = $utils->getTimeUntilNextHour();
                $redis->expireAt($key, $keyTimeout);
            }

            $redis->close();

            return true;
        } 
        catch (\RedisException $e) 
        {
            return $e->getMessage();
        }
        finally
        {
            $redis->close();
        }
    }

    /**
     * Get data from redis.
     *
     * @param string $key Name of key for the data
     *
     * @return mixed Data obtained from redis
     * @throws \RedisException
     */
    public function getCacheData($key)
    {
        try
        {
            $redisHost = $_ENV['REDIS_HOST'];
            $redisPort = $_ENV['REDIS_PORT'];

            $redis = new \Redis();
            $redis->connect($redisHost, $redisPort);

            $databaseData = self::decodeCacheData($redis->get($key));

            $redis->close();

            // For objects
            // if(strpos($data, 'stdClass'))
            //      $data = (object) $data;

            return $databaseData;
        }
        catch (\RedisException $e) 
        {
            return $e->getMessage();
        }
        finally
        {
            $redis->close();
        }
    }
    

    /**
     * Get data length from redis.
     *
     * Use to ensure that the key is not empty.
     * 
     * @param string $key Name of key for the data
     *
     * @return int Length of key content
     * @throws \RedisException 
     */
    public function getCacheDataLength($key)
    {
        try
        {
            $redisHost = $_ENV['REDIS_HOST'];
            $redisPort = $_ENV['REDIS_PORT'];

            $redis = new \Redis();
            $redis->connect($redisHost, $redisPort);

            $contentLengthOfKey = $redis->strLen($key);

            $redis->close();

            return $contentLengthOfKey;
        }
        catch (\RedisException $e) 
        {
            return $e->getMessage();
        }
        finally
        {
            $redis->close();
        }
    }

    /**
     * ONLY FOR DEVELOPMENT PURPOSES
     * 
     * Used to delete content inside of a key
     * with the purpose of testing cronjobs
     * 
     * @param string $key Name of key for the data
     *
     * @return string Result 
     */
    public function deleteCacheData($key)
    {
        try
        {
            $redisHost = $_ENV['REDIS_HOST'];
            $redisPort = $_ENV['REDIS_PORT'];

            $result = "The key '$key' ";

            $redis = new \Redis();
            $redis->connect($redisHost, $redisPort);

            if($redis->exists($key))
            {
                $redis->del($key);

                $result += "has been successfully deleted."; 
            }
            else 
            {
                $result += "hasn't been found or doesn't have any content."; 
            }

            $redis->close();

            return $result;

        }
        catch (\RedisException $e) 
        {
            return $e->getMessage();
        }
        finally
        {
            $redis->close();
        }

    }

     /**
     * ONLY FOR DEVELOPMENT PURPOSES
     * 
     * Delete all data from database
     *
     * @return bool True for success
     */
    public function deleteAllCache()
    {
        try
        {
            $redisHost = $_ENV['REDIS_HOST'];
            $redisPort = $_ENV['REDIS_PORT'];

            $redis = new \Redis();
            $redis->connect($redisHost, $redisPort);

            $redis->flushAll();

            $redis->close();

            return true;

        }
        catch (\RedisException $e) 
        {
            return $e->getMessage();
        }
        finally
        {
            $redis->close();
        }

    }
    

    /**
     * Encodes data for caching
     *
     * @param unknown_type $dataToSave Already processed data
     *
     * @return unknown_Type Encoded data
     */
    protected function encodeCacheData($dataToSave)
    {
        return serialize(json_decode(json_encode($dataToSave), true));
    }

    /**
     * Decodes data for usage
     *
     * @param unknown_type $login Encoded data from db
     *
     * @return unknown_Type Data
     */
    protected function decodeCacheData($dataFromDatabase)
    {
        return unserialize(json_decode(json_encode($dataFromDatabase)));
    }


}

?>

