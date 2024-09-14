<?php

require_once './config.php';

connectdb();

if (isNotAuthorized()) {
    die();
}

$ship = ship();

echo twig()->render('device.twig', [
    'ship' => $ship,
]);
