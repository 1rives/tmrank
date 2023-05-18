<?php

session_start();

// Disable errors
//error_reporting(E_ERROR);

// require('class/autoload.php');

// use TMRank\Database;

// $db = new Database;

// $db->deleteAllCache();
// exit;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TMRank</title>
    <?php include_once('template/head_styles.php'); ?>
    <!-- Page-specific styles -->
    <link href="assets/DataTables/datatables.min.css" rel="stylesheet"/>
</head>
<body>

    <?php include_once('template/navbar.php'); ?>

    <?php include_once('template/zones/hero.php'); ?>

    <div class="container is-max-widescreen"> 
        <div class="box">
            <section class="section pt-0 pb-6">
                <table id="example" class="table is-hoverable is-fullwidth">
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>Nickname</th>
                            <th>Ladder Points</th>
                        </tr>
                    </thead>
                    <tbody>
                    <tr>
                    <td>61</td>
                        <td>System Architect</td>
                        <td>Edinburgh</td>
                        
                    </tr>
                    <tr>
                        <td>63</td>
                        <td>Accountant</td>
                        <td>Tokyo</td>
                        
                    </tr>
                    <tr>
                    <td>66</td>
                        <td>Junior Technical Author</td>
                        <td>San Francisco</td>
                        
                    </tr>
                    <tr>
                    <td>22</td>
                        <td>Senior Javascript Developer</td>
                        <td>Edinburgh</td>
                        
                    </tr>
                    <tr>
                        <td>33</td>
                        <td>Accountant</td>
                        <td>Tokyo</td>
                        
                    </tr>
                    <tr>
                    <td>61</td>
                        <td>Integration Specialist</td>
                        <td>New York</td>
                        
                    </tr>
                    <tr>
                        <td>61</td>
                        <td>Sales Assistant</td>
                        <td>San Francisco</td>
                    </tr>
                    <tr>
                    <td>55</td>
                        <td>Integration Specialist</td>
                        <td>Tokyo</td>
                        
                    </tr>
                    <tr>
                    <td>39</td>
                        <td>Javascript Developer</td>
                        <td>San Francisco</td>
                        
                    </tr>
                    <tr>
                    <td>23</td>
                        <td>Software Engineer</td>
                        <td>Edinburgh</td>
                        
                    </tr>
                    <tr>
                    <td>61</td>
                        <td>System Architect</td>
                        <td>Edinburgh</td>
                        
                    </tr>
                    <tr>
                        <td>63</td>
                        <td>Accountant</td>
                        <td>Tokyo</td>
                        
                    </tr>
                    <tr>
                    <td>66</td>
                        <td>Junior Technical Author</td>
                        <td>San Francisco</td>
                        
                    </tr>
                    <tr>
                    <td>22</td>
                        <td>Senior Javascript Developer</td>
                        <td>Edinburgh</td>
                        
                    </tr>
                    <tr>
                        <td>33</td>
                        <td>Accountant</td>
                        <td>Tokyo</td>
                        
                    </tr>
                    <tr>
                    <td>61</td>
                        <td>Integration Specialist</td>
                        <td>New York</td>
                        
                    </tr>
                    <tr>
                        <td>61</td>
                        <td>Sales Assistant</td>
                        <td>San Francisco</td>
                    </tr>
                    <tr>
                    <td>55</td>
                        <td>Integration Specialist</td>
                        <td>Tokyo</td>
                        
                    </tr>
                    <tr>
                    <td>39</td>
                        <td>Javascript Developer</td>
                        <td>San Francisco</td>
                        
                    </tr>
                    <tr>
                    <td>23</td>
                        <td>Software Engineer</td>
                        <td>Edinburgh</td>
                        
                    </tr><tr>
                    <td>61</td>
                        <td>System Architect</td>
                        <td>Edinburgh</td>
                        
                    </tr>
                    <tr>
                        <td>63</td>
                        <td>Accountant</td>
                        <td>Tokyo</td>
                        
                    </tr>
                    <tr>
                    <td>66</td>
                        <td>Junior Technical Author</td>
                        <td>San Francisco</td>
                        
                    </tr>
                    <tr>
                    <td>22</td>
                        <td>Senior Javascript Developer</td>
                        <td>Edinburgh</td>
                        
                    </tr>
                    <tr>
                        <td>33</td>
                        <td>Accountant</td>
                        <td>Tokyo</td>
                        
                    </tr>
                    <tr>
                    <td>61</td>
                        <td>Integration Specialist</td>
                        <td>New York</td>
                        
                    </tr>
                    <tr>
                        <td>61</td>
                        <td>Sales Assistant</td>
                        <td>San Francisco</td>
                    </tr>
                    <tr>
                    <td>55</td>
                        <td>Integration Specialist</td>
                        <td>Tokyo</td>
                        
                    </tr>
                    <tr>
                    <td>39</td>
                        <td>Javascript Developer</td>
                        <td>San Francisco</td>
                        
                    </tr>
                    <tr>
                    <td>23</td>
                        <td>Software Engineer</td>
                        <td>Edinburgh</td>
                        
                    </tr><tr>
                    <td>61</td>
                        <td>System Architect</td>
                        <td>Edinburgh</td>
                        
                    </tr>
                    <tr>
                        <td>63</td>
                        <td>Accountant</td>
                        <td>Tokyo</td>
                        
                    </tr>
                    <tr>
                    <td>66</td>
                        <td>Junior Technical Author</td>
                        <td>San Francisco</td>
                        
                    </tr>
                    <tr>
                    <td>22</td>
                        <td>Senior Javascript Developer</td>
                        <td>Edinburgh</td>
                        
                    </tr>
                    <tr>
                        <td>33</td>
                        <td>Accountant</td>
                        <td>Tokyo</td>
                        
                    </tr>
                    <tr>
                    <td>61</td>
                        <td>Integration Specialist</td>
                        <td>New York</td>
                        
                    </tr>
                    <tr>
                        <td>61</td>
                        <td>Sales Assistant</td>
                        <td>San Francisco</td>
                    </tr>
                    <tr>
                    <td>55</td>
                        <td>Integration Specialist</td>
                        <td>Tokyo</td>
                        
                    </tr>
                    <tr>
                    <td>39</td>
                        <td>Javascript Developer</td>
                        <td>San Francisco</td>
                        
                    </tr>
                    <tr>
                    <td>23</td>
                        <td>Software Engineer</td>
                        <td>Edinburgh</td>
                        
                    </tr>
                </tbody>
                </table>
            </section>
        </div>
    </div>


    <?php include_once('template/footer.php'); ?>

    <?php include_once('template/body_scripts.php'); ?>
    <!-- Additional scripts below this line -->
    <!-- jQuery AJAX -->
    <script src="assets/jquery/jquery-3.3.1.min.js"></script>
    <script src="assets/js/ajax.js"></script>
    <!-- jQuery DataTables -->
    <script src="assets/DataTables/datatables.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#example').DataTable({
                paging: true,
                ordering: false,
                info: true,
                searching: true,

                deferLoading: true,
                stateSave: true,
                responsive: true,
            });
        });
    </script>
</body>
</html>