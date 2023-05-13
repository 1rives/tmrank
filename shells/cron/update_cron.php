<?php

    session_start();

    require '/var/www/html/tmrank/class/autoload.php';

    use TMRank\World;
    use TMRank\Zones;
    use TMRank\Utils;
    use TMRank\Database;

    /**
     * The only way to update the database reliably is through
     * this script with a CRONJOB.
     * 
     * Allowed inputs:
     * 
     * 1 - Only runs at a specific hour and minute.
     *     Ex.: 21:30
     *
     * 2 - Minutes from first script hour except the minuted used.
     *     Ex.: 21:00 - 21:29 and 21:31 - 21:59
     * 
     * 3 - Every hour at every minute minus 1st' script hour.
     *     Ex.: 22:00 to 20:59 the next day
     **/
    
    $availableRequests = [ 1, 2, 3 ];

    if(in_array($_GET['request'], $availableRequests))
    {
        // Declare multiples instances
        $utils = new Utils();
        $db = new Database();

        // Current classes that needs to update or check 
        // the database daily
        $classesToUpdate = array(
            getenv('REDIS_VARIABLE_WORLD'),
            getenv('REDIS_VARIABLE_ZONES')
        );

        // Delete all stored players since by the time
        // this executes, the data is outdated
        // TODO: Create function to delete Redis keys via prefix
        //$db->

        foreach ($classesToUpdate as $className)
        {
            // DEBUG: Shows data about every database update
            //saveCacheData('Type of request: ' . $_GET['request'] . ' at '. date('d/m/y H:i.s'), $request . 'request');

            // For readability purposes
            $redisKey = $className;

            // Declare an instance of the current class
            $classInstance = new ('TMRank\\' . $className)();
        
            // Assign the "update" function of the current class
            $classFunction = 'update' . $className . 'Ranking';

            if($_GET['request'] == 3 || $_GET['request'] == 2)
            {
                if(!$db->getCacheDataLength($redisKey))
                {
                    
                    if(method_exists($classInstance, $classFunction)) 
                    {     
                        $dataForUpdate = call_user_func(array($classInstance, $classFunction));
                        
                        // TODO: Fix "Call to undefined function TMRank\encodeCacheData() in /var/www/html/tmrank/class/TMRank/Database.php:35"
                        $db->saveCacheData($dataForUpdate, $redisKey);
                    }
                }
            }
            if($_GET['request'] == 1)
            {
                if(method_exists($classInstance, $classFunction))
                {
                    $dataForUpdate = call_user_func(array($classInstance, $classFunction));

                    // TODO: Fix "Call to undefined function TMRank\encodeCacheData() in /var/www/html/tmrank/class/TMRank/Database.php:35"
                    $db->saveCacheData($dataForUpdate, $redisKey);
                }

            }
           
        }
    }

