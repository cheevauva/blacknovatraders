<?php

declare(strict_types=1);

use BNT\Planet\Servant\PlanetBuildBaseServant;

require_once './config.php';

connectdb();

if (isNotAuthorized()) {
    die();
}

$ship = ship();

if (isset($_GET['buildp']) AND isset($_GET['builds'])) {
    $buildBase = new PlanetBuildBaseServant;
    $buildBase->ship = $ship;
    $buildBase->planet_id = intval($_GET['buildp']);
    $buildBase->sector_id = intval($_GET['builds']);
    $buildBase->doIt = false;
    $buildBase->serve();
    echo '<pre>';
    print_r($buildBase);
}

