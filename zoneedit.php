<?php

use BNT\Zone\DAO\ZoneByIdDAO;
use BNT\Zone\DAO\ZoneUpdateDAO;
use BNT\Team\DAO\TeamByIdDAO;

require_once 'config.php';

if (checkship()) {
    die();
}

try {
    $zone = fromRequest('zone', new \Exception('zone'));
    $curzone = ZoneByIdDAO::call($container, $zone)->zone;

    if (!$curzone) {
        throw new \Exception($l_zi_nexist);
    }
    if ($curzone['corp_zone'] == 'N') {
        $ownerinfo = $playerinfo;
    } else {
        $ownerinfo = TeamByIdDAO::call($container, $curzone['owner'])->team;
    }

    if (($curzone['corp_zone'] == 'N' && $curzone['owner'] != $ownerinfo['ship_id']) || ($curzone['corp_zone'] == 'Y' && $curzone['owner'] != $ownerinfo['id'] && $curzone['owner'] == $ownerinfo['creator'])) {
        throw new \Exception($l_ze_notowner);
    }

    switch (requestMethod()) {
        case 'GET':
            include 'tpls/zoneedit.tpl.php';
            break;
        case 'POST':
            ZoneUpdateDAO::call($container, [
                'zone_name' => fromPOST('name', new \Exception('name')),
                'allow_beacon' => fromPOST('beacons', new \Exception('beacons')),
                'allow_attack' => fromPOST('attacks', new \Exception('attacks')),
                'allow_warpedit' => fromPOST('attacks', new \Exception('warpedits')),
                'allow_planet' => fromPOST('planets', new \Exception('planets')),
                'allow_trade' => fromPOST('trades', new \Exception('trades')),
                'allow_defenses' => fromPOST('defenses', new \Exception('defenses')),
            ], $zone);
            redirectTo('zoneinfo.php?zone=' . $zone);
            break;
    }
} catch (\Exception $ex) {
    switch (requestMethod()) {
        case 'GET':
            include("header.php");
            echo $ex->getMessage();
            include("footer.php");
            break;
        case 'POST':
            echo responseJsonByException($ex);
            break;
    }
}
