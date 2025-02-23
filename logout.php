<?php

use BNT\Log\Event\LogLogoutEvent;
use Psr\EventDispatcher\EventDispatcherInterface;

require_once './config.php';

connectdb();

if (isNotAuthorized()) {
    die();
}

$ship = ship();

$logout = new LogLogoutEvent();
$logout->shipId = $ship->ship_id;
$logout->ip = $ip;
$logout->dispatch($container->get(EventDispatcherInterface::class));

unset($_SESSION['ship_id']);

header('Location: index.php');
die;
