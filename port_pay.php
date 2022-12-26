<?php

use BNT\Bounty\Servant\BountryPayByShipServant;

require_once './config.php';

connectdb();

if (isNotAuthorized()) {
    die();
}

$ship = ship();

BountryPayByShipServant::call($ship);

echo twig()->render('port/port_bounty_pay.twig');
