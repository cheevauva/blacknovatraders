<?php

use BNT\Controller\NewController;

include 'config.php';

if (!checkship(false)) {
    redirectTo('index.php');
    return;
}

$newController = NewController::new($container);
$newController->serve();
