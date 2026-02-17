<?php

//$Id$
include 'config.php';

$title = $l_gns_title;
include("header.php");

if (checkship()) {
    die();
}

//-------------------------------------------------------------------------------------------------

$sectorinfo = db()->fetch("SELECT * FROM universe WHERE sector_id= :sector", [
    'sector' => $playerinfo['sector']
]);

$planets_count = db()->fetch("SELECT COUNT(*) as count FROM planets WHERE sector_id= :sector", [
    'sector' => $playerinfo['sector']
]);
$num_planets = $planets_count['count'];

// Generate Planetname
$planetname = substr($playerinfo['character_name'], 0, 1) . substr($playerinfo['ship_name'], 0, 1) . "-" . $playerinfo['sector'] . "-" . ($num_planets + 1);

bigtitle();

if ($playerinfo['turns'] < 1) {
    echo "$l_gns_turn";
} elseif ($playerinfo['on_planet'] == 'Y') {
    echo $l_gns_onplanet;
} elseif ($num_planets >= $max_planets_sector) {
    echo $l_gns_full;
} elseif ($playerinfo['dev_genesis'] < 1) {
    echo "$l_gns_nogenesis";
} else {
    $zoneinfo = db()->fetch("SELECT allow_planet, corp_zone, owner FROM zones WHERE zone_id= :zone_id", [
        'zone_id' => $sectorinfo['zone_id']
    ]);

    if ($zoneinfo['allow_planet'] == 'N') {
        echo "$l_gns_forbid";
    } elseif ($zoneinfo['allow_planet'] == 'L') {
        if ($zoneinfo['corp_zone'] == 'N') {
            if ($playerinfo['team'] == 0 && $zoneinfo['owner'] != $playerinfo['ship_id']) {
                echo $l_gns_bforbid;
            } else {
                $ownerinfo = db()->fetch("SELECT team FROM ships WHERE ship_id= :owner", [
                    'owner' => $zoneinfo['owner']
                ]);

                if ($ownerinfo['team'] != $playerinfo['team']) {
                    echo $l_gns_bforbid;
                } else {
                    db()->q("INSERT INTO planets (sector_id, name, ore, organics, goods, energy, colonists, credits, fighters, torps, owner, corp, base, sells, prod_ore, prod_organics, prod_goods, prod_energy, prod_fighters, prod_torp, defeated) VALUES (:sector, :name, 0, 0, 0, 0, 0, 0, 0, 0, :owner, 0, 'N', 'N', :prod_organics, :prod_ore, :prod_goods, :prod_energy, :prod_fighters, :prod_torp, 'N')", [
                        'sector' => $playerinfo['sector'],
                        'name' => $planetname,
                        'owner' => $playerinfo['ship_id'],
                        'prod_ore' => $default_prod_ore,
                        'prod_organics' => $default_prod_organics,
                        'prod_goods' => $default_prod_goods,
                        'prod_energy' => $default_prod_energy,
                        'prod_fighters' => $default_prod_fighters,
                        'prod_torp' => $default_prod_torp
                    ]);

                    db()->q("UPDATE ships SET turns_used=turns_used+1, turns=turns-1, dev_genesis=dev_genesis-1 WHERE ship_id= :ship_id", [
                        'ship_id' => $playerinfo['ship_id']
                    ]);

                    echo $l_gns_pcreate;
                }
            }
        } elseif ($playerinfo['team'] != $zoneinfo['owner']) {
            echo $l_gns_bforbid;
        } else {
            db()->q("INSERT INTO planets (sector_id, name, ore, organics, goods, energy, colonists, credits, fighters, torps, owner, corp, base, sells, prod_ore, prod_organics, prod_goods, prod_energy, prod_fighters, prod_torp, defeated) VALUES (:sector, :name, 0, 0, 0, 0, 0, 0, 0, 0, :owner, 0, 'N', 'N', :prod_organics, :prod_ore, :prod_goods, :prod_energy, :prod_fighters, :prod_torp, 'N')", [
                'sector' => $playerinfo['sector'],
                'name' => $planetname,
                'owner' => $playerinfo['ship_id'],
                'prod_ore' => $default_prod_ore,
                'prod_organics' => $default_prod_organics,
                'prod_goods' => $default_prod_goods,
                'prod_energy' => $default_prod_energy,
                'prod_fighters' => $default_prod_fighters,
                'prod_torp' => $default_prod_torp
            ]);

            db()->q("UPDATE ships SET turns_used=turns_used+1, turns=turns-1, dev_genesis=dev_genesis-1 WHERE ship_id= :ship_id", [
                'ship_id' => $playerinfo['ship_id']
            ]);

            echo $l_gns_pcreate;
        }
    } else {
        db()->q("INSERT INTO planets (sector_id, name, ore, organics, goods, energy, colonists, credits, fighters, torps, owner, corp, base, sells, prod_ore, prod_organics, prod_goods, prod_energy, prod_fighters, prod_torp, defeated) VALUES (:sector, :name, 0, 0, 0, 0, 0, 0, 0, 0, :owner, 0, 'N', 'N', :prod_organics, :prod_ore, :prod_goods, :prod_energy, :prod_fighters, :prod_torp, 'N')", [
            'sector' => $playerinfo['sector'],
            'name' => $planetname,
            'owner' => $playerinfo['ship_id'],
            'prod_ore' => $default_prod_ore,
            'prod_organics' => $default_prod_organics,
            'prod_goods' => $default_prod_goods,
            'prod_energy' => $default_prod_energy,
            'prod_fighters' => $default_prod_fighters,
            'prod_torp' => $default_prod_torp
        ]);

        db()->q("UPDATE ships SET turns_used=turns_used+1, turns=turns-1, dev_genesis=dev_genesis-1 WHERE ship_id= :ship_id", [
            'ship_id' => $playerinfo['ship_id']
        ]);

        echo $l_gns_pcreate;
    }
}

//-------------------------------------------------------------------------------------------------

echo "<BR><BR>";

include("footer.php");
