<?php

$disableAutoLogin = true;

include 'config.php';

BNT\FrontController::call($container, BNT\Controller\CreateUniverseController::class);

