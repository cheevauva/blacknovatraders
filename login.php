<?php

include 'config.php';

$login = BNT\Controller\LoginController::new($container);
$login->gamedomain = $gamedomain;
$login->gamepath = $gamepath;
$login->serverClosed = $server_closed;

BNT\FrontController::call($container, $login);
