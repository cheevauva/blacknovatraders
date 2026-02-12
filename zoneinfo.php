<?php

use BNT\Ship\DAO\ShipByIdDAO;

$disableRegisterGlobalFix = true;
include 'config.php';

if (checklogin()) {
    die();
}

$zoneinfo = zoneById($zone);

if ($zoneinfo['zone_id'] < 5) {
    $zoneinfo['zone_name'] = $l_zname[$zoneinfo['zone_id']];
}

if ($zoneinfo['zone_id'] == 2) {
    $ownername = $l_zi_feds;
} elseif ($zoneinfo['zone_id'] == 3) {
    $ownername = $l_zi_traders;
} elseif ($zoneinfo['zone_id'] == 1) {
    $ownername = $l_zi_nobody;
} elseif ($zoneinfo['zone_id'] == 4) {
    $ownername = $l_zi_war;
} else {
    if ($zoneinfo['corp_zone'] == 'N') {
        $ownerinfo = ShipByIdDAO::call($container, $zoneinfo['owner'])->ship;
        $ownername = $ownerinfo['character_name'];
    } else {
        $ownerinfo = teamById($zoneinfo['owner']);
        $ownername = $ownerinfo['team_name'];
    }
}

if ($zoneinfo['allow_beacon'] == 'Y') {
    $beacon = $l_zi_allow;
} elseif ($zoneinfo['allow_beacon'] == 'N') {
    $beacon = $l_zi_notallow;
} else {
    $beacon = $l_zi_limit;
}

$attack = ($zoneinfo['allow_attack'] == 'Y') ? $l_zi_allow : $l_zi_notallow;

if ($zoneinfo['allow_defenses'] == 'Y') {
    $defense = $l_zi_allow;
} elseif ($zoneinfo['allow_defenses'] == 'N') {
    $defense = $l_zi_notallow;
} else {
    $defense = $l_zi_limit;
}

if ($zoneinfo['allow_warpedit'] == 'Y') {
    $warpedit = $l_zi_allow;
} elseif ($zoneinfo['allow_warpedit'] == 'N') {
    $warpedit = $l_zi_notallow;
} else {
    $warpedit = $l_zi_limit;
}

if ($zoneinfo['allow_planet'] == 'Y') {
    $planet = $l_zi_allow;
} elseif ($zoneinfo['allow_planet'] == 'N') {
    $planet = $l_zi_notallow;
} else {
    $planet = $l_zi_limit;
}

if ($zoneinfo['allow_trade'] == 'Y') {
    $trade = $l_zi_allow;
} elseif ($zoneinfo['allow_trade'] == 'N') {
    $trade = $l_zi_notallow;
} else {
    $trade = $l_zi_limit;
}

if ($zoneinfo['max_hull'] == 0) {
    $hull = $l_zi_ul;
} else {
    $hull = $zoneinfo['max_hull'];
}

$isAllowChangeZone = ($zoneinfo['corp_zone'] == 'N' && $zoneinfo['owner'] == $playerinfo['ship_id']) || ($zoneinfo['corp_zone'] == 'Y' && $zoneinfo['owner'] == $playerinfo['team'] && $playerinfo['ship_id'] == $ownerinfo['creator']);

include 'tpls/zoneinfo.tpl.php';

