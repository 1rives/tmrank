<?php

    session_start();

    include_once('../functions/php/functions.php');

    $_SESSION['request'] = $_GET['request'];

    // Update
    if($_GET['request'] == 1 || $_GET['request'] == 2 || $_GET['request'] == 3)
    {
        // All available requests
        $redis_array = array(
            $redis_world = $_ENV['REDIS_VARIABLE_WORLD'],
            $redis_zones = $_ENV['REDIS_VARIABLE_ZONES']
        );

        foreach ($redis_array as $request)
        {
            // Generate function name and get data
            $redis_request = 'get' . ucwords($request) . 'Info';

            // Check for empty cache
            if($_GET['request'] == 3 || $_GET['request'] == 2)
            {
                if(!getCacheDataLength($request))
                {
                    if(function_exists($redis_request)) {
                        $redis_data = call_user_func($redis_request, '');

                        saveCacheData($redis_data, $request);
                    }
                }
            }
            if($_GET['request'] == 1)
            {
                if(function_exists($redis_request))
                {
                    $redis_data = call_user_func($redis_request, '');
                    saveCacheData($redis_data, $request);
                }

            }
        }
    }

