<?php

use BNT\Bounty\Servant\BountryPayByShipServant;
use BNT\Bounty\Exception\BountyNotEnoughException;
use BNT\Bounty\Exception\BountyNotExistsException;

include("config.php");

loadlanguage($lang);
$title = $l_title_port;

connectdb();

if (isNotAuthorized()) {
    die();
}

$ship = ship();

include 'header.php';

try {
    BountryPayByShipServant::call($ship);
    echo $l_port_bountypaid;
} catch (BountyNotEnoughException $ex) {
    echo str_replace('[amount]', NUMBER($ex->amount), $ex->getMessage());
    TEXT_GOTOMAIN();
} catch (BountyNotExistsException $ex) {
    echo $ex->getMessage();
    TEXT_GOTOMAIN();
}

include 'footer.php';
