<?php

use BNT\Controller\ShipsController;

$disableRegisterGlobalFix = false;

include 'config.php';

if (checkuser()) {
    die;
}

$ships = ShipsController::new($container);
$ships->serve();

