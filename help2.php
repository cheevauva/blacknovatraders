<?php

declare(strict_types=1);

require_once './config.php';

connectdb();

if (isNotAuthorized()) {
    die();
}

$ship = ship();

echo twig()->render('help/help_en.twig', [
    'allow_navcomp' => $allow_navcomp,
    'allow_fullscan' => $allow_fullscan,
]);
