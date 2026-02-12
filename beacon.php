<?php

use BNT\Ship\DAO\ShipByIdDAO;
use BNT\Zone\DAO\ZoneByIdDAO;

$disableRegisterGlobalFix = true;

include 'config.php';

if (checklogin()) {
    die();
}

$sectorinfo = sectoryById($playerinfo['sector']);

try {
    if ($playerinfo['dev_beacon'] < 1) {
        throw new \Exception($l_beacon_donthave);
    }

    $zoneinfo = zoneById($sectorinfo['zone_id']);

    if ($zoneinfo['allow_beacon'] == 'N') {
        throw new \Exception($l_beacon_notpermitted);
    }

    if ($zoneinfo['allow_beacon'] == 'L') {
        $zoneById = ZoneByIdDAO::new($this->container);
        $zoneById->id = $sectorinfo['zone_id'];
        $zoneById->serve();
        
        $zoneowner_info = $zoneById->zone;
        $zoneteam = ShipByIdDAO::call($container, $zoneowner_info['owner'])->ship;

        if ($zoneowner_info['owner'] != $playerinfo['ship_id']) {
            if (($zoneteam['team'] != $playerinfo['team']) || ($playerinfo['team'] == 0)) {
                throw new \Exception($l_beacon_notpermitted);
            }
        }
    }

    switch (requestMethod()) {
        case 'GET':
            include 'tpls/beacon.tpl.php';
            break;
        case 'POST':
            sectorUpdate($playerinfo['sector'], [
                'beacon' => fromPost('beacon_text', new \Exception('beacon_text')),
            ]);
            BNT\ShipFunc::shipDevBeaconSub($playerinfo['ship_id'], 1);
            redirectTo('beacon.php');
            break;
    }
} catch (\Exception $ex) {
    switch (requestMethod()) {
        case 'GET':
            include 'tpls/beacon.tpl.php';
            break;
        case 'POST':
            echo responseJsonByException($ex);
            break;
    }
}


