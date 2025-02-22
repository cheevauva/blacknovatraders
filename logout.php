<?php

use BNT\Log\Entity\LogLogout;
use BNT\Log\DAO\LogCreateDAO;

require_once './config.php';

connectdb();

if (isNotAuthorized()) {
    die();
}

$ship = ship();

$logout = new LogLogout;
$logout->ship_id = $ship->ship_id;
$logout->ip = $ip;

LogCreateDAO::call($container, $logout);

unset($_SESSION['ship_id']);

header('Location: index.php');
die;
