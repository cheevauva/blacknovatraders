<?php

declare(strict_types=1);

require_once './config.php';

connectdb();

if (isNotAuthorized()) {
    die();
}

$ship = ship();

echo twig()->render('help.twig', [
]);
