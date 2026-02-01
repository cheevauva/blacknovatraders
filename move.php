<?php

include("config.php");
include("languages/$lang");

connectdb();

if (checklogin()) {
    die();
}

$title = $l_move_title;
$sector = $_GET['sector'];

try {
    if ($playerinfo['turns'] < 1) {
        throw new \Exception($l_move_turn);
    }

    $sectorinfo = getSectorInfo($playerinfo['sector']);
    $links = getLinks($playerinfo['sector']);

    $flag = false;

    foreach ($links as $link) {
        if ($link['link_dest'] == $sector && $link['link_start'] == $playerinfo['sector']) {
            $flag = true;
        }
    }

    if (empty($flag)) {
        db()->Execute("UPDATE ships SET cleared_defences = '' where ship_id={$playerinfo['ship_id']}");
        throw new \Exception($l_move_failed);
    }

    ob_start();
    $ok = 1;
    $calledfrom = "move.php";
    include("check_fighters.php"); 
    if ($ok > 0) {
        $stamp = date("Y-m-d H-i-s");
        $query = "UPDATE ships SET last_login='$stamp',turns=turns-1, turns_used=turns_used+1, sector=$sector where ship_id=$playerinfo[ship_id]";
        log_move($playerinfo[ship_id], $sector);
        $move_result = $db->Execute("$query");
        if (!$move_result) {
            // is this really STILL needed?
            $error = $db->ErrorMsg();
            mail($admin_mail, "Move Error", "Start Sector: $sectorinfo[sector_id]\nEnd Sector: $sector\nPlayer: $playerinfo[character_name] - $playerinfo[ship_id]\n\nQuery:  $query\n\nSQL error: $error");
        }
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


