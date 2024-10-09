<?php

declare(strict_types=1);

use BNT\Planet\Servant\PlanetBuildBaseServant;
use BNT\Planet\Servant\PlanetCollectCreditsServant;

require_once './config.php';

connectdb();

if (isNotAuthorized()) {
    die();
}

$ship = ship();
try {

    if (!empty($_POST['TPCreds']) && is_array($_POST['TPCreds'])) {
        $collectCredits = new PlanetCollectCreditsServant;
        $collectCredits->planetIds = $_POST['TPCreds'];
        $collectCredits->ship = $ship;
        $collectCredits->serve();
    }
    
    if (isset($_GET['buildp']) AND isset($_GET['builds'])) {
        $buildBase = new PlanetBuildBaseServant;
        $buildBase->ship = $ship;
        $buildBase->planet_id = intval($_GET['buildp']);
        $buildBase->sector_id = intval($_GET['builds']);
        $buildBase->doIt = true;
        $buildBase->serve();

        header('Location: planet_report2.php?PRepType=1');
        die;
    }
} catch (\Exception $ex) {
    echo twig()->render('error.twig', [
        'error' => $ex->getMessage(),
    ]);
}