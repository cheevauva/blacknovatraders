<?php

include("config.php");
include("languages/$lang");

connectdb();

if (checklogin()) {
    die();
}

$title = $l_move_title;

try {
    $sector = fromRequest('sector', new \Exception('sector'));

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

    $ok = 1;
    $calledfrom = "move.php";

    try {
        include 'check_fighters.php';
    } catch (SectorFightException $ex) {
        include 'sector_fighters.php';
    }

    shipMoveToSector($playerinfo['ship_id'], $sector);
    log_move($playerinfo['ship_id'], $sector);

    include 'check_mines.php';

    if (empty($messages)) {
        header('Location: index.php');
    } else {
        include "header.php";
        echo '<pre>' . implode('<br/>', $messages) . '</pre>';
        include 'footer.php';
    }
} catch (SectorChooseMoveException $ex) {
    include "header.php";
    include 'move_form.tpl.php';
    include 'footer.php';
} catch (\Exception $ex) {
    include "header.php";
    echo $ex->getMessage();
    include 'footer.php';
}


