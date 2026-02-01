<?php

include("config.php");
include("languages/$lang");

connectdb();

if (checklogin()) {
    die();
}

$title = $l_move_title;


try {
    $sector = fromGet('sector',  new \Exception('sector'));
    
    if ($playerinfo['turns'] < 1) {
        throw new \Exception($l_move_turn);
    }

    $sectorinfo = sectoryById($playerinfo['sector']);
    $links = linksBySector($playerinfo['sector']);

    $flag = false;

    foreach ($links as $link) {
        if ($link['link_dest'] == $sector && $link['link_start'] == $playerinfo['sector']) {
            $flag = true;
        }
    }

    if (empty($flag)) {
        shipResetClearedDefences($playerinfo['ship_id']);
        throw new \Exception($l_move_failed);
    }

    ob_start();
    $ok = 1;
    $calledfrom = "move.php";
    
    include("check_fighters.php");

    if ($ok > 0) {
        shipMoveToSector($playerinfo['ship_id'], $sector);
        log_move($playerinfo['ship_id'], $sector);
    }
    
    include("check_mines.php");
    
    if ($ok == 1) {
        ob_clean();
        header('Location: index.php');
        die;
    } else {
        TEXT_GOTOMAIN();
    }
} catch (\Exception $ex) {
    include("header.php");
    echo $ex->getMessage();
    include("footer.php");
    die;
}


