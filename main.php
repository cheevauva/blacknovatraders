<?php

include("config.php");
include("languages/$lang");

connectdb();

if (checklogin()) {
    die();
}

if (!empty(trim($playerinfo['cleared_defences']))) {
    header('Location: ' . $playerinfo['cleared_defences']);
    die;
}

if ($playerinfo['on_planet'] == "Y") {
    $res2 = $db->Execute("SELECT planet_id, owner FROM planets WHERE planet_id=$playerinfo[planet_id]");
    if ($res2->RecordCount() != 0) {
        echo "<A HREF=planet.php?planet_id=$playerinfo[planet_id]>$l_clickme</A> $l_toplanetmenu    <BR>";
        echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0;URL=planet.php?planet_id=$playerinfo[planet_id]&id=" . $playerinfo[ship_id] . "\">";
        die();
    } else {
        $db->Execute("UPDATE ships SET on_planet='N' WHERE ship_id=$playerinfo[ship_id]");
        echo "<BR>$l_nonexistant_pl<BR><BR>";
    }
}

$sectorinfo = sectoryById($playerinfo['sector']);
$links = linksBySector($playerinfo['sector']);
$planets = planetsBySector($playerinfo['sector']);
$defences = defencesBySector($playerinfo['sector']);
$zoneinfo = zoneById($sectorinfo['zone_id']);
$traderoutes = traderoutesBySectorAndShip($playerinfo['sector'], $playerinfo['ship_id']);
$shipsInSector = getShipsInSector($playerinfo['sector'], $playerinfo['ship_id']);

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
        //unset($shipsInSector[$idx]);
    }
}

include 'tpls/main.tpl.php';
