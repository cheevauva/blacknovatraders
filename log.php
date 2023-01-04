<?php

use BNT\Ship\View\ShipView;
use BNT\Log\View\LogView;
use BNT\Log\DAO\LogRetrieveManyByShipDAO;

require_once './config.php';

connectdb();

if (isNotAuthorized()) {
    die();
}

$playerinfo = ship();

echo twig()->render('log/log.twig', [
    'logs' => LogView::map(LogRetrieveManyByShipDAO::call($playerinfo)),
    'playerinfo' => new ShipView($playerinfo),
]);
