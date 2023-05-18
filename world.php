<?php

session_start();

// Disable errors
//error_reporting(E_ERROR);

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

    <?php include_once('template/world/hero.php'); ?>

    <div class="container is-max-widescreen"> 
        <div class="box">

            <?php include_once('template/login_form.php'); ?>
                
            <section class="section pt-0 pb-3">
                <div class="tabs is-centered is-boxed pb-0 mb-0">
                    <ul>
                        <li class="is-active">
                            <a>
                                <!-- <span class="icon is-small"><i class="fas fa-image" aria-hidden="true"></i></span> -->
                                <span>Merge</span>
                            </a>
                        </li>
                        <li>
                            <a>
                                <span>Stadium</span>
                            </a>
                        </li>
                        <li>
                            <a>
                                <span>Desert</span>
                            </a>
                        </li>
                        <li>
                            <a>
                                <span>Island</span>
                            </a>
                        </li>
                        <li>
                            <a>
                                <span>Coast</span>
                            </a>
                        </li>
                        <li>
                            <a>
                                <span>Rally</span>
                            </a>
                        </li>
                        <li>
                            <a>
                                <span>Bay</span>
                            </a>
                        </li>
                        <li>
                            <a>
                                <span>Snow</span>
                            </a>
                        </li>
                    </ul>
                </div>
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
                paging: false,
                ordering: false,
                info: false,
                searching: false,

                deferLoading: true,
                stateSave: true,
                responsive: true,
            });
        });
    </script>
</body>
</html>