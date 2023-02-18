<?php
/**
 * TrackMania Web Services SDK for PHP - Examples
 *
 * @copyright   Copyright (c) 2009-2011 NADEO (http://www.nadeo.com)
 * @license     http://www.gnu.org/licenses/lgpl.html LGPL License 3
 * @author      $Author: maximeraoust $:
 * @version     $Revision: 23 $:
 * @date        $Date: 2011-07-21 15:26:21 +0200 (jeu., 21 juil. 2011) $:
 */

require_once('class/autoload.php');

// User and password for API login
$apiuser = $_ENV['TMFWEBSERVICE_USER'];
$apipw = $_ENV['TMFWEBSERVICE_PASSWORD'];

if (isset($_POST['submit']) && isset($_POST['logins'])) {
    $login = $_POST['logins'];
    echo "User: " . $login;

    // Player information
    $info = new \TrackMania\WebServices\Players($apiuser, $apipw);
    $player = $info->get($login);

    // For testing purposes
    // $info = new \TrackMania\WebServices\Foobar();
    // $sos = $info->get();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TMRank</title>
</head>
</body>

<form action="" method="POST">
    <div class="form-group">
        <label for="logins">Login</label>
        <input type="text" class="form-control" name="logins" aria-describedby="emailHelp" placeholder="TMNF User">
    </div>

    <button type="submit" class="btn btn-primary" name="submit">Submit</button>
</form>
</html>
<?php print_r($player); ?>
