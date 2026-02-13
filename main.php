<?php

use BNT\EntryPoint\Servant\EntryPointMainServant;
use BNT\EntryPoint\Exception\EntryPointMainShipOnPlanetException;

include 'config.php';

if (checklogin()) {
    die();
}

if (!empty(trim($playerinfo['cleared_defences']))) {
    redirectTo($playerinfo['cleared_defences']);
    return;
}

try {
    $entryPointMain = EntryPointMainServant::new($container);
    $entryPointMain->playerinfo = $playerinfo;
    $entryPointMain->serve();

    $sectorinfo = $entryPointMain->sector;
    $links = $entryPointMain->links;
    $planets = $entryPointMain->planets;
    $defences = $entryPointMain->sectorDefences;
    $zoneinfo = $entryPointMain->zone;
    $traderoutes = $entryPointMain->traderoutes;
    $shipsInSector = $entryPointMain->ships;

    if (!empty($_GET['demo'])) {
        $traderoutes[] = [];
        $planets[] = [];
        $shipsInSector[] = [];
        $defences[] = [];

        for ($i = 0; $i < 10; $i++) {
            $traderoutes[] = [
                'traderoute_id' => $ii,
            ];
            $planets[] = [
                'name' => 'P' . $i,
                'planet_id' => $i,
                'owner_score' => $i * 3,
                'owner' => $i * 1000,
                'owner_character_name' => 'OCN' . $i * 1000,
            ];
            $shipsInSector[] = [
                'ship_id' => $i,
                'score' => $i * 3,
                'ship_name' => 'S' . $i * 1000,
                'character_name' => 'N' . $i * 1000,
            ];

            $defenceTypes = ['F', 'M'];
            $defenceFmSetting = ['attack', 'toll'];
            $defences[] = [
                'character_name' => 'CN' . $i * 1000,
                'quantity' => rand(0, 100),
                'defence_id' => $i,
                'fm_setting' => $defenceFmSetting[rand(0, 1)],
                'defence_type' => $defenceTypes[rand(0, 1)],
            ];
        }
    }

    foreach ($shipsInSector as $idx => $shipInSector) {
        $success = sensorsCloakSuccess($playerinfo['sensors'], $shipInSector['cloak']);
        $roll = rand(1, 100);

        if ($roll >= $success) {
            unset($shipsInSector[$idx]);
        }
    }

    include 'tpls/main.tpl.php';
} catch (EntryPointMainShipOnPlanetException $ex) {
    redirectTo('planet.php?planet_id=' . $ex->planet);
} catch (\Exception $ex) {
    $title = $l_error;
    include 'tpls/error.tpl.php';
}
