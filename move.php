<?php

use BNT\Sector\Exception\SectorFightException;
use BNT\Sector\Exception\SectorChooseMoveException;
use BNT\Sector\DAO\SectorByIdDAO;

include 'config.php';

if (checklogin()) {
    die();
}

$title = $l_move_title;

try {
    $sector = fromRequest('sector', 0);

    if ($playerinfo['turns'] < 1) {
        throw new \Exception($l_move_turn);
    }

    $sectorinfo = SectorByIdDAO::call($container, $playerinfo['sector'])->sector;
    $links = linksByStart($playerinfo['sector']);

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
        redirectTo('index.php');
    } else {
        include "header.php";
        echo '<pre>' . implode('<br/>', $messages) . '</pre>';
        include 'footer.php';
    }
} catch (SectorChooseMoveException $ex) {
    include 'tpls/move_form.tpl.php';
} catch (\Exception $ex) {
    include "header.php";
    echo $ex->getMessage() . '<pre>' . $ex->getTraceAsString() . '</pre>';
    include 'footer.php';
}
