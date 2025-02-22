<?php

declare(strict_types=1);

require_once './config.php';
loadlanguage($lang);

connectdb();

if (isNotAuthorized()) {
    die();
}

$shipId = intval($_GET['ship_id'] ?? 0);
$ship = ship();
$othership = BNT\Ship\DAO\ShipRetrieveByIdDAO::call($container, $shipId);

echo twig()->render('ship/ship.twig', [
    'ship_id' => $shipId,
    'othership' => $othership,
    'ship' => $ship,
]);
