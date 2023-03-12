<?php

    include_once('../functions/php/functions.php');

    $redis_world = $_ENV['REDIS_VARIABLE_WORLD'];
    $redis_zones = $_ENV['REDIS_VARIABLE_ZONES'];

    // Update
    if($_GET['request'] == 1 || $_GET['request'] == 2 || $_GET['request'] == 3)
    {
        // All available requests
        $redis_array = array(
            $redis_world,
            $redis_zones
        );


        foreach ($redis_array as $request)
        {
            // Function name
            $redis_request = 'get' . ucwords($request) . 'Info';
            
            
            
            // Check
            if($_GET['request'] == 2 || $_GET['request'] == 3)
            {
                var_dumpis_callable($redis_request);
                // redis_request no funciona, deberia de traer una funcion
                if(!getCacheDataLength($request))
                    var_dump(saveCacheData($redis_request()));
                exit;
            }

            // Update
            if($_GET['request'] == 1)
            {

                $redis_request();
            }
        }
    }
    // World
    // $test = date('h:i:s');
    //
    // saveCacheData($test, 'test');
    // saveCacheData($_GET['request'], 'param');

?>