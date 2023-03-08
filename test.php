<?php

    session_start();

    include_once "functions/php/functions.php";

    $_SESSION['sas'] = loadWorldInfo($login);

?>
