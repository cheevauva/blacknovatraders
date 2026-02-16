<?php

use BNT\Controller\LoginController;

$disableRegisterGlobalFix = true;

include 'config.php';

$loginController = LoginController::new($container);
$loginController->serve();
