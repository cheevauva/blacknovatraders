<?php

$disableAutoLogin = true;

include 'config.php';

BNT\FrontController::call($container, BNT\Controller\SchemaController::class);
