<?php

use BNT\Controller\CreateUniverseController;

$disableAutoLogin = true;
$disableRegisterGlobalFix = true;

include 'config.php';

$title = "Create Universe";

$createUniverseCtrl = CreateUniverseController::new($container);
$createUniverseCtrl->serve();

