<?php

use BNT\Log\LogTypeConstants;
use BNT\Log\DAO\LogPlayerDAO;

function xenobetoship($ship_id)
{
    global $container;

    $xenobeToShip = BNT\Xenobe\Servant\XenobeToShipServant::new($container);
    $xenobeToShip->ship = $ship_id;
    $xenobeToShip->serve();
}

function xenobetosecdef($playerinfo, $sector)
{
    global $container;

    $xenobeToSecDef = BNT\Xenobe\Servant\XenobeToSecDefServant::new($container);
    $xenobeToSecDef->playerinfo = $playerinfo;
    $xenobeToSecDef->sector = $sector;
    $xenobeToSecDef->serve();
}

function xenobemove($playerinfo)
{
    global $container;
    
    $xenobeMove = BNT\Xenobe\Servant\XenobeMoveServant::new($container);
    $xenobeMove->playerinfo = $playerinfo;
    $xenobeMove->serve();

    return $xenobeMove->targetSector;
}

function xenoberegen($playerinfo)
{
    global $container;

    $regen = \BNT\Xenobe\Servant\XenobeRegenServant::new($container);
    $regen->playerinfo = $playerinfo;
    $regen->serve();
}

function xenobetrade()
{
    global $playerinfo;
    global $inventory_factor;
    global $ore_price;
    global $ore_delta;
    global $ore_limit;
    global $goods_price;
    global $goods_delta;
    global $goods_limit;
    global $organics_price;
    global $organics_delta;
    global $organics_limit;
    global $xenobeisdead;
    global $container;

    $ore_price = 11;
    $organics_price = 5;
    $goods_price = 15;

    $sectorinfo = db()->fetch("SELECT * FROM universe WHERE sector_id = :sector_id", [
        'sector_id' => $playerinfo['sector']
    ]);

    $zonerow = db()->fetch("SELECT zone_id,allow_attack,allow_trade FROM zones WHERE zone_id = :zone_id", [
        'zone_id' => $sectorinfo['zone_id']
    ]);

    if ($zonerow['allow_trade'] == "N") {
        return;
    }

    if ($sectorinfo['port_type'] == "none") {
        return;
    }
    if ($sectorinfo['port_type'] == "energy") {
        return;
    }

    if ($playerinfo['ship_ore'] < 0) {
        $playerinfo['ship_ore'] = $shipore = 0;
    }
    if ($playerinfo['ship_organics'] < 0) {
        $playerinfo['ship_organics'] = $shiporganics = 0;
    }
    if ($playerinfo['ship_goods'] < 0) {
        $playerinfo['ship_goods'] = $shipgoods = 0;
    }
    if ($playerinfo['credits'] < 0) {
        $playerinfo['credits'] = $shipcredits = 0;
    }
    if ($sectorinfo['port_ore'] <= 0) {
        return;
    }
    if ($sectorinfo['port_organics'] <= 0) {
        return;
    }
    if ($sectorinfo['port_goods'] <= 0) {
        return;
    }

    if ($playerinfo['ship_ore'] > 0) {
        $shipore = $playerinfo['ship_ore'];
    }
    if ($playerinfo['ship_organics'] > 0) {
        $shiporganics = $playerinfo['ship_organics'];
    }
    if ($playerinfo['ship_goods'] > 0) {
        $shipgoods = $playerinfo['ship_goods'];
    }
    if ($playerinfo['credits'] > 0) {
        $shipcredits = $playerinfo['credits'];
    }
    if (!$playerinfo['credits'] > 0 && !$playerinfo['ship_ore'] > 0 && !$playerinfo['ship_goods'] > 0 && !$playerinfo['ship_organics'] > 0) {
        return;
    }

    if ($sectorinfo['port_type'] == "ore" && $shipore > 0) {
        return;
    }
    if ($sectorinfo['port_type'] == "organics" && $shiporganics > 0) {
        return;
    }
    if ($sectorinfo['port_type'] == "goods" && $shipgoods > 0) {
        return;
    }

    if ($sectorinfo['port_type'] == "ore") {
        $ore_price = $ore_price - $ore_delta * $sectorinfo['port_ore'] / $ore_limit * $inventory_factor;
        $organics_price = $organics_price + $organics_delta * $sectorinfo['port_organics'] / $organics_limit * $inventory_factor;
        $goods_price = $goods_price + $goods_delta * $sectorinfo['port_goods'] / $goods_limit * $inventory_factor;

        $amount_organics = $playerinfo['ship_organics'];
        $amount_goods = $playerinfo['ship_goods'];
        $amount_ore = NUM_HOLDS($playerinfo['hull']);
        $amount_ore = min($amount_ore, $sectorinfo['port_ore']);
        $amount_ore = min($amount_ore, floor(($playerinfo['credits'] + $amount_organics * $organics_price + $amount_goods * $goods_price) / $ore_price));

        $total_cost = round(($amount_ore * $ore_price) - ($amount_organics * $organics_price + $amount_goods * $goods_price));
        $newcredits = max(0, $playerinfo['credits'] - $total_cost);
        $newore = $playerinfo['ship_ore'] + $amount_ore;
        $neworganics = max(0, $playerinfo['ship_organics'] - $amount_organics);
        $newgoods = max(0, $playerinfo['ship_goods'] - $amount_goods);
        db()->q("UPDATE ships SET rating = rating + 1, credits = :newcredits, ship_ore = :newore, ship_organics = :neworganics, ship_goods = :newgoods where ship_id = :ship_id", [
            'newcredits' => $newcredits,
            'newore' => $newore,
            'neworganics' => $neworganics,
            'newgoods' => $newgoods,
            'ship_id' => $playerinfo['ship_id']
        ]);
        db()->q("UPDATE universe SET port_ore = port_ore - :amount_ore, port_organics = port_organics + :amount_organics, port_goods = port_goods + :amount_goods where sector_id = :sector_id", [
            'amount_ore' => $amount_ore,
            'amount_organics' => $amount_organics,
            'amount_goods' => $amount_goods,
            'sector_id' => $sectorinfo['sector_id']
        ]);
        LogPlayerDAO::call($container, $playerinfo['ship_id'], LogTypeConstants::LOG_RAW, sprintf("Xenobe Trade Results: Sold %s Organics Sold %s Goods Bought %s Ore Cost %s", $amount_organics, $amount_goods, $amount_ore, $total_cost));
    }
    if ($sectorinfo['port_type'] == "organics") {
        $organics_price = $organics_price - $organics_delta * $sectorinfo['port_organics'] / $organics_limit * $inventory_factor;
        $ore_price = $ore_price + $ore_delta * $sectorinfo['port_ore'] / $ore_limit * $inventory_factor;
        $goods_price = $goods_price + $goods_delta * $sectorinfo['port_goods'] / $goods_limit * $inventory_factor;

        $amount_ore = $playerinfo['ship_ore'];
        $amount_goods = $playerinfo['ship_goods'];
        $amount_organics = NUM_HOLDS($playerinfo['hull']);
        $amount_organics = min($amount_organics, $sectorinfo['port_organics']);
        $amount_organics = min($amount_organics, floor(($playerinfo['credits'] + $amount_ore * $ore_price + $amount_goods * $goods_price) / $organics_price));

        $total_cost = round(($amount_organics * $organics_price) - ($amount_ore * $ore_price + $amount_goods * $goods_price));
        $newcredits = max(0, $playerinfo['credits'] - $total_cost);
        $newore = max(0, $playerinfo['ship_ore'] - $amount_ore);
        $neworganics = $playerinfo['ship_organics'] + $amount_organics;
        $newgoods = max(0, $playerinfo['ship_goods'] - $amount_goods);
        db()->q("UPDATE ships SET rating = rating + 1, credits = :newcredits, ship_ore = :newore, ship_organics = :neworganics, ship_goods = :newgoods where ship_id = :ship_id", [
            'newcredits' => $newcredits,
            'newore' => $newore,
            'neworganics' => $neworganics,
            'newgoods' => $newgoods,
            'ship_id' => $playerinfo['ship_id']
        ]);
        db()->q("UPDATE universe SET port_ore = port_ore + :amount_ore, port_organics = port_organics - :amount_organics, port_goods = port_goods + :amount_goods where sector_id = :sector_id", [
            'amount_ore' => $amount_ore,
            'amount_organics' => $amount_organics,
            'amount_goods' => $amount_goods,
            'sector_id' => $sectorinfo['sector_id']
        ]);
        LogPlayerDAO::call($container, $playerinfo['ship_id'], LogTypeConstants::LOG_RAW, sprintf("Xenobe Trade Results: Sold %s Goods Sold %s Ore Bought %s Organics Cost %s", $amount_goods, $amount_ore, $amount_organics, $total_cost));
    }
    if ($sectorinfo['port_type'] == "goods") {
        $goods_price = $goods_price - $goods_delta * $sectorinfo['port_goods'] / $goods_limit * $inventory_factor;
        $ore_price = $ore_price + $ore_delta * $sectorinfo['port_ore'] / $ore_limit * $inventory_factor;
        $organics_price = $organics_price + $organics_delta * $sectorinfo['port_organics'] / $organics_limit * $inventory_factor;

        $amount_ore = $playerinfo['ship_ore'];
        $amount_organics = $playerinfo['ship_organics'];
        $amount_goods = NUM_HOLDS($playerinfo['hull']);
        $amount_goods = min($amount_goods, $sectorinfo['port_goods']);
        $amount_goods = min($amount_goods, floor(($playerinfo['credits'] + $amount_ore * $ore_price + $amount_organics * $organics_price) / $goods_price));

        $total_cost = round(($amount_goods * $goods_price) - ($amount_organics * $organics_price + $amount_ore * $ore_price));
        $newcredits = max(0, $playerinfo['credits'] - $total_cost);
        $newore = max(0, $playerinfo['ship_ore'] - $amount_ore);
        $neworganics = max(0, $playerinfo['ship_organics'] - $amount_organics);
        $newgoods = $playerinfo['ship_goods'] + $amount_goods;
        db()->q("UPDATE ships SET rating = rating + 1, credits = :newcredits, ship_ore = :newore, ship_organics = :neworganics, ship_goods = :newgoods where ship_id = :ship_id", [
            'newcredits' => $newcredits,
            'newore' => $newore,
            'neworganics' => $neworganics,
            'newgoods' => $newgoods,
            'ship_id' => $playerinfo['ship_id']
        ]);
        db()->q("UPDATE universe SET port_ore = port_ore + :amount_ore, port_organics = port_organics + :amount_organics, port_goods = port_goods - :amount_goods where sector_id = :sector_id", [
            'amount_ore' => $amount_ore,
            'amount_organics' => $amount_organics,
            'amount_goods' => $amount_goods,
            'sector_id' => $sectorinfo['sector_id']
        ]);
        LogPlayerDAO::call($container, $playerinfo['ship_id'], LogTypeConstants::LOG_RAW, sprintf("Xenobe Trade Results: Sold %s Ore Sold %s Organics Bought %s Goods Cost %s", $amount_ore, $amount_organics, $amount_goods, $total_cost));
    }
}

function xenobehunter()
{
    global $playerinfo;
    global $targetlink;
    global $xenobeisdead;
    global $container;

    $rowcount = db()->fetch("SELECT COUNT(*) AS num_players FROM ships WHERE ship_destroyed='N' and email NOT LIKE '%@xenobe' and ship_id > 1");
    $topnum = min(10, $rowcount['num_players']);

    if ($topnum < 1) {
        return;
    }

    $res = db()->fetchAll("SELECT * FROM ships WHERE ship_destroyed='N' and email NOT LIKE '%@xenobe' and ship_id > 1 ORDER BY score DESC LIMIT " . intval($topnum));

    $i = 1;
    $targetnum = rand(1, $topnum);
    $targetinfo = null;
    foreach ($res as $row) {
        if ($i == $targetnum) {
            $targetinfo = $row;
        }
        $i++;
    }

    if (!$targetinfo) {
        LogPlayerDAO::call($container, $playerinfo['ship_id'], LogTypeConstants::LOG_RAW, "Hunt Failed: No Target ");
        return;
    }

    $sectrow = db()->fetch("SELECT sector_id,zone_id FROM universe WHERE sector_id = :sector_id", [
        'sector_id' => $targetinfo['sector']
    ]);
    $zonerow = db()->fetch("SELECT zone_id,allow_attack FROM zones WHERE zone_id = :zone_id", [
        'zone_id' => $sectrow['zone_id']
    ]);

    if ($zonerow['allow_attack'] == "Y") {
        $stamp = date("Y-m-d H-i-s");
        $move_result = db()->q("UPDATE ships SET turns_used = turns_used + 1, sector = :sector where ship_id = :ship_id", [
            'sector' => $targetinfo['sector'],
            'ship_id' => $playerinfo['ship_id']
        ]);
        LogPlayerDAO::call($container, $playerinfo['ship_id'], LogTypeConstants::LOG_RAW, sprintf("Xenobe used a wormhole to warp to sector %s where he is hunting player %s.", $targetinfo['sector'], $targetinfo['character_name']));
        if (!$move_result) {
            $error = db()->ErrorMsg();
            LogPlayerDAO::call($container, $playerinfo['ship_id'], LogTypeConstants::LOG_RAW, sprintf("Move failed with error: %s ", $error));
            return;
        }

        $defences = [];
        $resultf = db()->fetchAll("SELECT * FROM sector_defence WHERE sector_id = :sector_id and defence_type = 'F' ORDER BY quantity DESC", [
            'sector_id' => $targetinfo['sector']
        ]);
        $i = 0;
        $total_sector_fighters = 0;
        if (!empty($resultf)) {
            foreach ($resultf as $row) {
                $defences[$i] = $row;
                $total_sector_fighters += $defences[$i]['quantity'];
                $i++;
            }
        }
        $resultm = db()->fetchAll("SELECT * FROM sector_defence WHERE sector_id = :sector_id and defence_type = 'M'", [
            'sector_id' => $targetinfo['sector']
        ]);
        $i = 0;
        $total_sector_mines = 0;
        if (!empty($resultm)) {
            foreach ($resultm as $row) {
                $defences[$i] = $row;
                $total_sector_mines += $defences[$i]['quantity'];
                $i++;
            }
        }

        if ($total_sector_fighters > 0 || $total_sector_mines > 0 || ($total_sector_fighters > 0 && $total_sector_mines > 0)) {
            $targetlink = $targetinfo['sector'];
            xenobetosecdef();
        }
        if ($xenobeisdead > 0) {
            return;
        }

        LogPlayerDAO::call($container, $playerinfo['ship_id'], LogTypeConstants::LOG_RAW, sprintf("Xenobe launching an attack on %s.", $targetinfo['character_name']));

        if ($targetinfo['planet_id'] > 0) {
            xenobetoplanet($targetinfo['planet_id']);
        } else {
            xenobetoship($targetinfo['ship_id']);
        }
    } else {
        LogPlayerDAO::call($container, $playerinfo['ship_id'], LogTypeConstants::LOG_RAW, sprintf("Xenobe hunt failed, target %s was in a no attack zone (sector %s).", $targetinfo['character_name'], $targetinfo['sector']));
    }
}

function xenobetoplanet($planet_id)
{
    global $playerinfo;
    global $planetinfo;

    global $torp_dmg_rate;
    global $level_factor;
    global $rating_combat_factor;
    global $upgrade_cost;
    global $upgrade_factor;
    global $sector_max;
    global $xenobeisdead;
    global $container;
    global $basedefense;

    $planetinfo = db()->fetch("SELECT * FROM planets WHERE planet_id = :planet_id", [
        'planet_id' => $planet_id
    ]);

    $ownerinfo = db()->fetch("SELECT * FROM ships WHERE ship_id = :ship_id", [
        'ship_id' => $planetinfo['owner']
    ]);

    $base_factor = ($planetinfo['base'] == 'Y') ? $basedefense : 0;

    $targetbeams = NUM_BEAMS($ownerinfo['beams'] + $base_factor);
    if ($targetbeams > $planetinfo['energy']) {
        $targetbeams = $planetinfo['energy'];
    }
    $planetinfo['energy'] -= $targetbeams;

    $targetshields = NUM_SHIELDS($ownerinfo['shields'] + $base_factor);
    if ($targetshields > $planetinfo['energy']) {
        $targetshields = $planetinfo['energy'];
    }
    $planetinfo['energy'] -= $targetshields;

    $torp_launchers = round(mypw($level_factor, ($ownerinfo['torp_launchers']) + $base_factor)) * 10;
    $torps = $planetinfo['torps'];
    $targettorps = $torp_launchers;
    if ($torp_launchers > $torps) {
        $targettorps = $torps;
    }
    $planetinfo['torps'] -= $targettorps;
    $targettorpdmg = $torp_dmg_rate * $targettorps;

    $targetfighters = $planetinfo['fighters'];

    $attackerbeams = NUM_BEAMS($playerinfo['beams']);
    if ($attackerbeams > $playerinfo['ship_energy']) {
        $attackerbeams = $playerinfo['ship_energy'];
    }
    $playerinfo['ship_energy'] -= $attackerbeams;

    $attackershields = NUM_SHIELDS($playerinfo['shields']);
    if ($attackershields > $playerinfo['ship_energy']) {
        $attackershields = $playerinfo['ship_energy'];
    }
    $playerinfo['ship_energy'] -= $attackershields;

    $attackertorps = round(mypw($level_factor, $playerinfo['torp_launchers'])) * 2;
    if ($attackertorps > $playerinfo['torps']) {
        $attackertorps = $playerinfo['torps'];
    }
    $playerinfo['torps'] -= $attackertorps;
    $attackertorpdamage = $torp_dmg_rate * $attackertorps;

    $attackerfighters = $playerinfo['ship_fighters'];
    $attackerarmor = $playerinfo['armor_pts'];

    if ($attackerbeams > 0 && $targetfighters > 0) {
        if ($attackerbeams > $targetfighters) {
            $lost = $targetfighters;
            $targetfighters = 0;
            $attackerbeams = $attackerbeams - $lost;
        } else {
            $targetfighters = $targetfighters - $attackerbeams;
            $attackerbeams = 0;
        }
    }
    if ($attackerfighters > 0 && $targetbeams > 0) {
        if ($targetbeams > round($attackerfighters / 2)) {
            $lost = $attackerfighters - (round($attackerfighters / 2));
            $attackerfighters = $attackerfighters - $lost;
            $targetbeams = $targetbeams - $lost;
        } else {
            $attackerfighters = $attackerfighters - $targetbeams;
            $targetbeams = 0;
        }
    }
    if ($attackerbeams > 0) {
        if ($attackerbeams > $targetshields) {
            $attackerbeams = $attackerbeams - $targetshields;
            $targetshields = 0;
        } else {
            $targetshields = $targetshields - $attackerbeams;
            $attackerbeams = 0;
        }
    }
    if ($targetbeams > 0) {
        if ($targetbeams > $attackershields) {
            $targetbeams = $targetbeams - $attackershields;
            $attackershields = 0;
        } else {
            $attackershields = $attackershields - $targetbeams;
            $targetbeams = 0;
        }
    }
    if ($targetbeams > 0) {
        if ($targetbeams > $attackerarmor) {
            $targetbeams = $targetbeams - $attackerarmor;
            $attackerarmor = 0;
        } else {
            $attackerarmor = $attackerarmor - $targetbeams;
            $targetbeams = 0;
        }
    }
    if ($targetfighters > 0 && $attackertorpdamage > 0) {
        if ($attackertorpdamage > $targetfighters) {
            $lost = $targetfighters;
            $targetfighters = 0;
            $attackertorpdamage = $attackertorpdamage - $lost;
        } else {
            $targetfighters = $targetfighters - $attackertorpdamage;
            $attackertorpdamage = 0;
        }
    }
    if ($attackerfighters > 0 && $targettorpdmg > 0) {
        if ($targettorpdmg > round($attackerfighters / 2)) {
            $lost = $attackerfighters - (round($attackerfighters / 2));
            $attackerfighters = $attackerfighters - $lost;
            $targettorpdmg = $targettorpdmg - $lost;
        } else {
            $attackerfighters = $attackerfighters - $targettorpdmg;
            $targettorpdmg = 0;
        }
    }
    if ($targettorpdmg > 0) {
        if ($targettorpdmg > $attackerarmor) {
            $targettorpdmg = $targettorpdmg - $attackerarmor;
            $attackerarmor = 0;
        } else {
            $attackerarmor = $attackerarmor - $targettorpdmg;
            $targettorpdmg = 0;
        }
    }
    if ($attackerfighters > 0 && $targetfighters > 0) {
        if ($attackerfighters > $targetfighters) {
            $temptargfighters = 0;
        } else {
            $temptargfighters = $targetfighters - $attackerfighters;
        }
        if ($targetfighters > $attackerfighters) {
            $tempplayfighters = 0;
        } else {
            $tempplayfighters = $attackerfighters - $targetfighters;
        }
        $attackerfighters = $tempplayfighters;
        $targetfighters = $temptargfighters;
    }
    if ($targetfighters > 0) {
        if ($targetfighters > $attackerarmor) {
            $attackerarmor = 0;
        } else {
            $attackerarmor = $attackerarmor - $targetfighters;
        }
    }

    if ($attackerfighters < 0) {
        $attackerfighters = 0;
    }
    if ($attackertorps < 0) {
        $attackertorps = 0;
    }
    if ($attackershields < 0) {
        $attackershields = 0;
    }
    if ($attackerbeams < 0) {
        $attackerbeams = 0;
    }
    if ($attackerarmor < 0) {
        $attackerarmor = 0;
    }
    if ($targetfighters < 0) {
        $targetfighters = 0;
    }
    if ($targettorps < 0) {
        $targettorps = 0;
    }
    if ($targetshields < 0) {
        $targetshields = 0;
    }
    if ($targetbeams < 0) {
        $targetbeams = 0;
    }

    if (!$attackerarmor > 0) {
        LogPlayerDAO::call($container, $playerinfo['ship_id'], LogTypeConstants::LOG_RAW, sprintf("Ship destroyed by planetary defenses on planet %s", $planetinfo['name']));
        db_kill_player($playerinfo['ship_id']);
        $xenobeisdead = 1;

        $free_ore = round($playerinfo['ship_ore'] / 2);
        $free_organics = round($playerinfo['ship_organics'] / 2);
        $free_goods = round($playerinfo['ship_goods'] / 2);
        $ship_value = $upgrade_cost * (round(mypw($upgrade_factor, $playerinfo['hull'])) + round(mypw($upgrade_factor, $playerinfo['engines'])) + round(mypw($upgrade_factor, $playerinfo['power'])) + round(mypw($upgrade_factor, $playerinfo['computer'])) + round(mypw($upgrade_factor, $playerinfo['sensors'])) + round(mypw($upgrade_factor, $playerinfo['beams'])) + round(mypw($upgrade_factor, $playerinfo['torp_launchers'])) + round(mypw($upgrade_factor, $playerinfo['shields'])) + round(mypw($upgrade_factor, $playerinfo['armor'])) + round(mypw($upgrade_factor, $playerinfo['cloak'])));
        $ship_salvage_rate = rand(10, 20);
        $ship_salvage = $ship_value * $ship_salvage_rate / 100;
        $fighters_lost = $planetinfo['fighters'] - $targetfighters;

        LogPlayerDAO::call($container, $planetinfo['owner'], LogTypeConstants::LOG_PLANET_NOT_DEFEATED, sprintf("%s|%s|Xenobe %s|%s|%s|%s|%s|%s",
        $planetinfo['name'],
        $playerinfo['sector'],
        $playerinfo['character_name'],
        $free_ore,
        $free_organics,
        $free_goods,
        $ship_salvage_rate,
        $ship_salvage));

        db()->q("UPDATE planets SET energy = :energy, fighters = fighters - :fighters_lost, torps = torps - :targettorps, ore = ore + :free_ore, goods = goods + :free_goods, organics = organics + :free_organics, credits = credits + :ship_salvage WHERE planet_id = :planet_id", [
            'energy' => $planetinfo['energy'],
            'fighters_lost' => $fighters_lost,
            'targettorps' => $targettorps,
            'free_ore' => $free_ore,
            'free_goods' => $free_goods,
            'free_organics' => $free_organics,
            'ship_salvage' => $ship_salvage,
            'planet_id' => $planetinfo['planet_id']
        ]);
    } else {
        $armor_lost = $playerinfo['armor_pts'] - $attackerarmor;
        $fighters_lost = $playerinfo['ship_fighters'] - $attackerfighters;
        LogPlayerDAO::call($container, $playerinfo['ship_id'], LogTypeConstants::LOG_RAW, sprintf("Made it past defenses on planet %s", $planetinfo['name']));

        db()->q("UPDATE ships SET ship_energy = :ship_energy, ship_fighters = ship_fighters - :fighters_lost, torps = torps - :attackertorps, armor_pts = armor_pts - :armor_lost WHERE ship_id = :ship_id", [
            'ship_energy' => $playerinfo['ship_energy'],
            'fighters_lost' => $fighters_lost,
            'attackertorps' => $attackertorps,
            'armor_lost' => $armor_lost,
            'ship_id' => $playerinfo['ship_id']
        ]);
        $playerinfo['ship_fighters'] = $attackerfighters;
        $playerinfo['torps'] = $attackertorps;
        $playerinfo['armor_pts'] = $attackerarmor;

        db()->q("UPDATE planets SET energy = :energy, fighters = :targetfighters, torps = torps - :targettorps WHERE planet_id = :planet_id", [
            'energy' => $planetinfo['energy'],
            'targetfighters' => $targetfighters,
            'targettorps' => $targettorps,
            'planet_id' => $planetinfo['planet_id']
        ]);
        $planetinfo['fighters'] = $targetfighters;
        $planetinfo['torps'] = $targettorps;

        $resultps = db()->fetchAll("SELECT ship_id,ship_name FROM ships WHERE planet_id = :planet_id AND on_planet = 'Y'", [
            'planet_id' => $planetinfo['planet_id']
        ]);
        $shipsonplanet = count($resultps);
        if ($shipsonplanet > 0) {
            foreach ($resultps as $onplanet) {
                if ($xenobeisdead >= 1)
                    break;
                xenobetoship($onplanet['ship_id']);
            }
        }
        $resultps = db()->fetchAll("SELECT ship_id,ship_name FROM ships WHERE planet_id = :planet_id AND on_planet = 'Y'", [
            'planet_id' => $planetinfo['planet_id']
        ]);
        $shipsonplanet = count($resultps);
        if ($shipsonplanet == 0 && $xenobeisdead < 1) {
            LogPlayerDAO::call($container, $playerinfo['ship_id'], LogTypeConstants::LOG_RAW, sprintf("Defeated all ships on planet %s", $planetinfo['name']));
            LogPlayerDAO::call($container, $planetinfo['owner'], LogTypeConstants::LOG_PLANET_DEFEATED, sprintf("%s|%s|%s",
            $planetinfo['name'],
            $playerinfo['sector'],
            $playerinfo['character_name']));

            db()->q("UPDATE planets SET fighters = 0, torps = 0, base = 'N', owner = 0, corp = 0 WHERE planet_id = :planet_id", [
                'planet_id' => $planetinfo['planet_id']
            ]);
            calc_ownership($planetinfo['sector_id']);
        } else {
            LogPlayerDAO::call($container, $playerinfo['ship_id'], LogTypeConstants::LOG_RAW, sprintf("We were KILLED by ships defending planet %s", $planetinfo['name']));
            LogPlayerDAO::call($container, $planetinfo['owner'], LogTypeConstants::LOG_PLANET_NOT_DEFEATED, sprintf("%s|%s|Xenobe %s|0|0|0|0|0",
            $planetinfo['name'],
            $playerinfo['sector'],
            $playerinfo['character_name']));
        }
    }
}
