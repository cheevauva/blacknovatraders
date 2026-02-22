<?php

include 'config.php';

$new = BNT\Controller\NewController::new($container);
$new->accountCreationClosed = $account_creation_closed;
$new->gamedomain = $gamedomain;
$new->gamepath = $gamepath;

BNT\FrontController::call($container, $new);
