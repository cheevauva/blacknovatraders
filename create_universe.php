<?php

use BNT\Controller\CreateUniverseController;

$disableAutoLogin = true;
$disableRegisterGlobalFix = true;

include 'config.php';

$createUniverseCtrl = CreateUniverseController::new($container);
$createUniverseCtrl->serve();

