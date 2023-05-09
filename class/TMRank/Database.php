<?php 

/**
 * Guzzle HTTP client for the Trackmania Web Services API.
 *
 * @author noiszia
 */
namespace TMRank;

require_once('/var/www/html/tmrank/class/tmfcolorparser.inc.php');

/**
 * General access to Redis database
 */
class Database extends TMRankClient
{
    /** 
     * Save data to redis.
     *
     * @param string $dataToSave Processed data to save
     * @param string $key Name of key for the data
     *
     * @throws RedisException
     */
    public function saveCacheData($dataToSave, $key)
    {
        $redisHost = $_ENV['REDIS_HOST'];
        $redisPort = $_ENV['REDIS_PORT'];

        $redis = new Redis();
        $redis->connect($redisHost, $redisPort);

        $redis->set($key, encodeCacheData($dataToSave));
        $keyTimeout = getTimeUntilMidnight();
        $redis->expireAt($key, $keyTimeout);

        $redis->close();
    }

    /**
     * Get data from redis.
     *
     * @param string $key Name of key for the data
     *
     * @return stdClass Data obtained from redis
     * @throws RedisException
     */
    public function getCacheData($key)
    {
        $redisHost = $_ENV['REDIS_HOST'];
        $redisPort = $_ENV['REDIS_PORT'];

        $redis = new Redis();
        $redis->connect($redisHost, $redisPort);

        $databaseData = decodeCacheData($redis->get($key));

        $redis->close();

        // For objects
        // if(strpos($data, 'stdClass'))
        //      $data = (object) $data;

        return $databaseData;

    }

    /**
     * Get data length from redis.
     *
     * Use to ensure that the 
     * key is not empty.
     * 
     * @param string $key Name of key for the data
     *
     * @return int Length of key content
     * @throws RedisException 
     */
    public function getCacheDataLength($key)
    {
        $redisHost = $_ENV['REDIS_HOST'];
        $redisPort = $_ENV['REDIS_PORT'];

        $redis = new Redis();
        $redis->connect($redisHost, $redisPort);

        $contentLengthOfKey = $redis->strLen($key);

        $redis->close();

        return $contentLengthOfKey;
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
        $redisHost = $_ENV['REDIS_HOST'];
        $redisPort = $_ENV['REDIS_PORT'];

        $result = "The key '$key' ";

        $redis = new Redis();
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

    /**
     * Generates the time left in seconds for
     * the cache to expire.
     *
     * @return int Unix timestamp
     */
    protected function getTimeUntilMidnight()
    {
        $expirationTime = 'tomorrow midnight';

        $timeUntilMidnight = strtotime($expirationTime);

        return $timeUntilMidnight;
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

