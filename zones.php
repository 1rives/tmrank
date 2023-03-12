<?php

    session_start();



    include_once('functions/php/functions.php'); // General functions

    if(!isset($_POST['submit']))
    {
        unset($_SESSION['errorMessage']);
    }


    if (isset($_POST['submit']) && isset($_POST['login']))
    {
        $login = $_POST['login'];
    }
    
    // Redis key name to get data 
    $php_script_name = explode(".", basename($_SERVER['PHP_SELF']));
    $ladder_name = strtoupper($php_script_name[0]); 
    $redis_name = $_ENV["REDIS_VARIABLE_$ladder_name"]; 

    // Get data for showing
    // if(isset($login)){
    //     $world = getWorldInfo($login);
    // }
    // else{
    //     $world = getCacheObject($redis_name);
    // }
    
    //$test = getZonesInfo();
    //print_r($test);


    ////////////////////////////////
    // DATA TEST
    
    //header('Content-Type: application/json');
    
    //$apiuser = $_ENV['TMFWEBSERVICE_FETCHER_USER'];
    //$apipw = $_ENV['TMFWEBSERVICE_FETCHER_PASSWORD'];

    //$zones = new \TrackMania\WebServices\MultiplayerRankings($apiuser, $apipw);
    //$results = $zones->getZoneRanking('world', 0, 10);

    

    ////////////////////////////////////

    $zones = getZonesInfo();
    
    //sizeof($sas->ladder);
    
    //exit;

    

    // For showing data
    $player_environment = "Merge"; 

?>

<!doctype html>
<html lang="en" data-bs-theme='dark'>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <meta name="generator" content="Hugo 0.84.0">
    <title>Search a player</title>

    <link rel="canonical" href="https://getbootstrap.com/docs/5.0/examples/pricing/">

    <!-- Bootstrap core CSS -->

    <link href='assets/bootstrap/css/bootstrap.min.css' rel='stylesheet'>
    <link href='https://cdn.datatables.net/1.13.3/css/jquery.dataTables.min.css' rel='stylesheet'>
    
    <link href='assets/css_old/main.css' rel='stylesheet'>
    

    <script src="assets/jquery/jquery-3.3.1.min.js"></script>
    <script src="assets/datatables/DataTables-1.10.18/js/jquery.dataTables.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#datatable').DataTable();

        } );
    </script>

</head>


<svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
    <symbol id="check" viewBox="0 0 16 16">
        <title>Check</title>
        <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z"/>
    </symbol>
</svg>


<?php include_once "templates/navbar.php" ?>

<body>

<div class="container py-3">
        
    <div class="pricing-header p-3 pb-md-4 mx-auto text-center">
        <h1 class="display-4 fw-normal">Zone ranking</h1>
    </div>

    <br>
    <main class='w-100 mx-at py-3'>
        <?php showZonesTable($zones); ?>
    </main>
</div>




<script src='assets/bootstrap/js/bootstrap.bundle.min.js'></script>

</body>
<?php include_once "templates/footer.php" ?>
</html>