<?php

declare(strict_types=1);

use BNT\Planet\Servant\PlanetBuildBaseServant;

require_once './config.php';

connectdb();

if (isNotAuthorized()) {
    die();
}

$ship = ship();

echo '<pre>';
if (isset($_GET['buildp']) AND isset($_GET['builds'])) {
    $buildBase = new PlanetBuildBaseServant;
    $buildBase->ship = $ship;
    $buildBase->planet_id = $buildp;
    $buildBase->sector_id = $builds;
    $buildBase->serve();
}

print_r($_POST);
