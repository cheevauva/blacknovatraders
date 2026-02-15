<?php

include 'config.php';

$info = array();

$info["GAMENAME"] = $game_name;
$info["GAMEID"] = md5($game_name . $bnt_ls_key);

$start_date = db()->fetch("SELECT UNIX_TIMESTAMP(time) as x FROM movement_log WHERE event_id = 1");
$info["START-DATE"] = $start_date['x'];
$info["G-DURATION"] = -1;

$all_ships = db()->fetch("SELECT count(*) as x FROM ships");
$info["P-ALL"] = $all_ships['x'];

$active_ships = db()->fetch("SELECT count(*) as x FROM ships WHERE ship_destroyed = 'N'");
$info["P-ACTIVE"] = $active_ships['x'];

$human_ships = db()->fetch("SELECT count(*) as x FROM ships WHERE ship_destroyed = 'N' AND email NOT LIKE '%@xenobe'");
$info["P-HUMAN"] = $human_ships['x'];

$online_players = db()->fetch("SELECT COUNT(*) as x FROM ships WHERE (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(last_login)) / 60 <= 5 and email NOT LIKE '%@xenobe'");
$info["P-ONLINE"] = $online_players['x'];

$avg_xenobe = db()->fetch("SELECT AVG(hull) AS a1, AVG(engines) AS a2, AVG(power) AS a3, AVG(computer) AS a4, AVG(sensors) AS a5, AVG(beams) AS a6, AVG(torp_launchers) AS a7, AVG(shields) AS a8, AVG(armor) AS a9, AVG(cloak) AS a10 FROM ships WHERE ship_destroyed='N' and email LIKE '%@xenobe'");
$dyn_xenobe_lvl = $avg_xenobe['a1'] + $avg_xenobe['a2'] + $avg_xenobe['a3'] + $avg_xenobe['a4'] + $avg_xenobe['a5'] + $avg_xenobe['a6'] + $avg_xenobe['a7'] + $avg_xenobe['a8'] + $avg_xenobe['a9'] + $avg_xenobe['a10'];
$dyn_xenobe_lvl = $dyn_xenobe_lvl / 10;
$info["P-AI-LVL"] = $dyn_xenobe_lvl;

$top_players = db()->fetchAll("SELECT character_name, score FROM ships WHERE ship_destroyed = 'N' ORDER BY score DESC LIMIT 3");
$rank = 1;
foreach ($top_players as $player) {
    $info["P-TOP{$rank}-NAME"] = $player['character_name'];
    $info["P-TOP{$rank}-SCORE"] = $player['score'];
    $rank++;
}

$info["G-TURNS-START"] = $start_turns;
$info["G-TURNS-MAX"] = $max_turns;

$info["G-SCHED-TICKS"] = $sched_ticks;
$info["G-SCHED-TYPE"] = $sched_type;
$info["G-SPEED-TURNS"] = $sched_turns;
$info["G-SPEED-PORTS"] = $sched_ports;
$info["G-SPEED-PLANETS"] = $sched_planets;
$info["G-SPEED-IGB"] = $sched_igb;

$info["G-SIZE-SECTOR"] = $sector_max;
$info["G-SIZE-UNIVERSE"] = $universe_size;
$info["G-SIZE-PLANETS"] = $max_planets_sector;
$info["G-SIZE-PLANETS-TO-OWN"] = $min_bases_to_own;

$info["G-COLONIST-LIMIT"] = $colonist_limit;
$info["G-DOOMSDAY-VALUE"] = $doomsday_value;

$info["G-MONEY-IGB"] = $ibank_interest;
$info["G-MONEY-PLANET"] = round($interest_rate - 1, 4);

$info["G-PORT-LIMIT-ORE"] = $ore_limit;
$info["G-PORT-RATE-ORE"] = $ore_delta;
$info["G-PORT-DELTA-ORE"] = $ore_delta;

$info["G-PORT-LIMIT-ORGANICS"] = $organics_limit;
$info["G-PORT-RATE-ORGANICS"] = $organics_rate;
$info["G-PORT-DELTA-ORGANICS"] = $organics_delta;

$info["G-PORT-LIMIT-GOODS"] = $goods_limit;
$info["G-PORT-RATE-GOODS"] = $goods_rate;
$info["G-PORT-DELTA-GOODS"] = $goods_delta;

$info["G-PORT-LIMIT-ENERGY"] = $energy_limit;
$info["G-PORT-RATE-ENERGY"] = $energy_rate;
$info["G-PORT-DELTA-ENERGY"] = $energy_delta;

$info["G-SOFA"] = ($sofa_on === true ? "1" : "0");
$info["G-KSM"] = ($ksm_allowed ? "1" : "0");

$info["S-CLOSED"] = ($server_closed ? "1" : "0");
$info["S-CLOSED-ACCOUNTS"] = ($account_creation_closed ? "1" : "0");

$info["ALLOW_FULLSCAN"] = ($allow_fullscan ? "1" : "0");
$info["ALLOW_NAVCOMP"] = ($allow_navcomp ? "1" : "0");
$info["ALLOW_IBANK"] = ($allow_ibank ? "1" : "0");
$info["ALLOW_GENESIS_DESTROY"] = ($allow_genesis_destroy ? "1" : "0");

$info["INVENTORY_FACTOR"] = $inventory_factor;
$info["UPGRADE_COST"] = $upgrade_cost;
$info["UPGRADE_FACTOR"] = $upgrade_factor;
$info["LEVEL_FACTOR"] = $level_factor;

$info["DEV_GENESIS_PRICE"] = $dev_genesis_price;
$info["DEV_BEACON_PRICE"] = $dev_beacon_price;
$info["DEV_EMERWARP_PRICE"] = $dev_emerwarp_price;
$info["DEV_WARPEDIT_PRICE"] = $dev_warpedit_price;
$info["DEV_MINEDEFLECTOR_PRICE"] = $dev_minedeflector_price;
$info["DEV_ESCAPEPOD_PRICE"] = $dev_escapepod_price;
$info["DEV_FUELSCOOP_PRICE"] = $dev_fuelscoop_price;
$info["DEV_LSSD_PRICE"] = $dev_lssd_price;

$info["FIGHTER_PRICE"] = $fighter_price;
$info["TORPEDO_PRICE"] = $torpedo_price;
$info["ARMOUR_PRICE"] = $armour_price;
$info["COLONIST_PRICE"] = $colonist_price;

$info["BASEDEFENSE"] = $basedefense;

$info["COLONIST_PRODUCTION_RATE"] = $colonist_production_rate;
$info["COLONIST_REPRODUCTION_RATE"] = $colonist_reproduction_rate;
$info["ORGANICS_CONSUMPTION"] = $organics_consumption;
$info["STARVATION_DEATH_RATE"] = $starvation_death_rate;

$info["CORP_PLANET_TRANSFERS"] = ($corp_planet_transfers ? "1" : "0");
$info["MAX_TEAM_MEMBERS"] = $max_team_members;

$info["SERVERTIMEZONE"] = $servertimezone;

$info["ADMIN_MAIL"] = $admin_mail;
$info["LINK_FORUMS"] = $link_forums;

// Output as plain text with line breaks
header('Content-Type: text/plain');

foreach ($info as $key => $value) {
    echo $key . ":" . $value . "\n";
}