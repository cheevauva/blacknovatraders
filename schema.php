<?php

use BNT\Controller\SchemaController;

$disableAutoLogin = true;
$disableRegisterGlobalFix = true;

include 'config.php';

$schemaController = SchemaController::new($container);
$schemaController->serve();

