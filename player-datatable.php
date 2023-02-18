<?php
if (!isset($playerinfo)) {
    header("Location: player.php");
}

?>

<div class="container">
    <div class="row">
        <div class="col text-center">
            <h4 class="pb-2 py-3">
                <hr>
                <strong>Player information</strong>
            </h4>
            <p>Nickname: <?php echo $playerinfo->nickname; ?></p>
            <p>Account type: <?php echo  $playerinfo->account; ?></p>
            <p>Zone: <?php echo  $playerinfo->nation; ?></p>
        </div>
    </div>

    <div class="row">
        <div class="col text-center">
            <h4 class="pb-2 py-3">
                <hr>
                <strong>General ladder</strong></h4>
            <p><?php echo $playerinfo->multiPoints; ?> Ladder Points</p>
            <p>World ranking: <?php echo $playerinfo->multiWorld; ?></p>
            <p>Zone ranking: <?php echo $playerinfo->multiZone; ?></p>
        </div>
        <div class="col text-center">
            <h4 class="pb-2 py-3">
                <hr>
                <strong>Solo ladder</strong></h4>
            <p><?php echo $playerinfo->soloPoints; ?></p>
            <p><?php echo $playerinfo->soloWorld; ?></p>
        </div>
    </div>