<?php

include("config.php");
include("languages/$lang");

connectdb();

if (checklogin()) {
    die();
}

try {
    $zone = fromRequest('zone', new \Exception('zone'));
    $curzone = zoneById($zone);

    if (!$curzone) {
        throw new \Exception($l_zi_nexist);
    }
    if ($curzone['corp_zone'] == 'N') {
        $ownerinfo = $playerinfo;
    } else {
        $ownerinfo = teamById($curzone['owner']);
    }

    if (($curzone['corp_zone'] == 'N' && $curzone['owner'] != $ownerinfo['ship_id']) || ($curzone['corp_zone'] == 'Y' && $curzone['owner'] != $ownerinfo['id'] && $curzone['owner'] == $ownerinfo['creator'])) {
        throw new \Exception($l_ze_notowner);
    }

    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            include 'tpls/zoneedit.tpl.php';
            break;
        case 'POST':
            zoneUpdate([
                'zone_id' => $zone,
                'zone_name' => fromPost('name', new \Exception('name')),
                'allow_beacon' => fromPost('beacons', new \Exception('beacons')),
                'allow_attack' => fromPost('attacks', new \Exception('attacks')),
                'allow_warpedit' => fromPost('attacks', new \Exception('warpedits')),
                'allow_planet' => fromPost('planets', new \Exception('planets')),
                'allow_trade' => fromPost('trades', new \Exception('trades')),
                'allow_defenses' => fromPost('defenses', new \Exception('defenses')),
            ]);
            header('Location: zoneinfo.php?zone=' . $zone);
            break;
    }
} catch (\Exception $ex) {
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            include("header.php");
            echo $ex->getMessage();
            include("footer.php");
            break;
        case 'POST':
            echo json_encode([
                'error' => $ex->getMessage(),
                'code' => $ex->getCode(),
            ], JSON_UNESCAPED_UNICODE);
            break;
    }
}
