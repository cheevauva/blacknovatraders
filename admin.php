<?php

include 'config.php';

function CHECKED($yesno)
{
    return(($yesno == 'Y') ? "CHECKED" : "");
}

function YESNO($onoff)
{
    return(($onoff == "ON") ? 'Y' : 'N');
}

$controller = BNT\Controller\BaseController::as(match ($_GET['module'] ?? null) {
    'sector' => BNT\Controller\AdminSectorController::new($container),
    'ship' => BNT\Controller\AdminShipController::new($container),
    'user' => BNT\Controller\AdminUserController::new($container),
    'planet' => BNT\Controller\AdminPlanetController::new($container),
    'zone' => BNT\Controller\AdminZoneController::new($container),
    'config' => BNT\Controller\AdminConfigController::new($container),
    default => BNT\Controller\AdminController::new($container),
});
$controller->serve();

