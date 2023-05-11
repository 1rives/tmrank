<?php

    session_start();

    include_once('functions/php/general_functions.php'); // General functions

    if (!isset($_POST['submit'])) {
        unset($_SESSION['errorMessage']);
    }

?>


<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <meta name="generator" content="Hugo 0.84.0">
    <title>Home</title>

    <link rel="canonical" href="https://getbootstrap.com/docs/5.0/examples/pricing/">


    <!-- Bootstrap core CSS -->
    <link href="assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>

<svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
    <symbol id="check" viewBox="0 0 16 16">
        <title>Check</title>
        <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z"/>
    </symbol>
</svg>

<?php include_once "templates/navbar.php" ?>

<body>

<div class="pricing-header p-3 pb-md-4 mx-auto text-center">
    <h1 class="display-4 fw-normal">Welcome</h1>

    <main>
    </main>

</div>
<br>


<script src='assets/bootstrap/js/bootstrap.bundle.min.js'></script>
<script src='assets/js/scripts.js'></script>
<script src='https://cdn.datatables.net/1.13.3/js/jquery.dataTables.min.js'></script>

</body>

<?php include_once "templates/footer.php" ?>

</html>
