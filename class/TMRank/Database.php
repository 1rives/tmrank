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
     * @param string $loginKey Player login for Redis key
     * @param string $classPrefix Class name for key prefix, ex.: "World" or "Players"
     *
     * @return bool true for success, false for existing data
     * @throws \RedisException
     */
    public function checkForLoginData($loginKey, $classPrefix)
    {
        $keyWithLogin = $classPrefix . '.' . $loginKey;

        return (!self::getCacheDataLength($keyWithLogin)) ? false : false;
    }

    /** 
     * Save data to redis.
     *
     * @param string $dataToSave Processed data to save
     * @param string $key Name of key for the data
     * 
     * @return bool True for successful data update
     * @throws \RedisException
     */
    public function saveCacheData($dataToSave, $key)
    {
        try 
        {
            // Declare a Utils instance
            $utils = new Utils();

            $redisHost = $_ENV['REDIS_HOST'];
            $redisPort = $_ENV['REDIS_PORT'];

            $redis = new \Redis();
            $redis->connect($redisHost, $redisPort);

            $redis->set($key, self::encodeCacheData($dataToSave));
            $keyTimeout = $utils->getTimeUntilMidnight();
            $redis->expireAt($key, $keyTimeout);

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
     * @return \stdClass Data obtained from redis
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
     * with the purpouse of testing cronjobs
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

