<?php

use BNT\Log\Entity\LogLogout;

require_once './config.php';

connectdb();

if (isNotAuthorized()) {
    die();
}

$ship = ship();

$logout = new LogLogout;
$logout->ship_id = $ship->ship_id;
$logout->ip = $ip;
$logout->dispatch();

unset($_SESSION['ship_id']);

header('Location: index.php');
die;
