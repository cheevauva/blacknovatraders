<?php

use BNT\Request;
use BNT\EntryPoint\Servant\EntryPointZoneinfoServant;

$disableRegisterGlobalFix = true;

require_once 'config.php';

$zone = (int) fromGET('zone', new \Exception('zone'));

$entryPointZoneInfo = EntryPointZoneinfoServant::new($container);
$entryPointZoneInfo->playerinfo = $playerinfo;
$entryPointZoneInfo->zone = $zone;
$entryPointZoneInfo->serve();

if (!$entryPointZoneInfo->zoneinfo) {
    include 'tpls/zoneinfo.tpl.php';
    return;
}

$zoneinfo = $entryPointZoneInfo->zoneinfo;
$isAllowChangeZone = $entryPointZoneInfo->isAllowChangeZone;
$ownername = $entryPointZoneInfo->ownername;

include 'tpls/zoneinfo.tpl.php';
