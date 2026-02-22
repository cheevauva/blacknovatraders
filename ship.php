<?php

use BNT\Ship\DAO\ShipByIdDAO;

$disableRegisterGlobalFix = false;

include 'config.php';

if (checkship()) {
    die();
}

$ship_id = fromGet('ship_id');
$othership = ShipByIdDAO::call($container, $ship_id)->ship;

include 'tpls/ship.tpl.php';
