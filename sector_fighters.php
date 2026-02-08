<?php

use BNT\Sector\Exception\SectorFightShipDestroyedException;

preg_match("/sector_fighters.php/i", $_SERVER['PHP_SELF']) ? die('You can not access this file directly!') : null;

if (!isset($total_sector_fighters)) {
    throw new \Exception('total_sector_fighters is required');
}

if (!isset($playerinfo)) {
    throw new \Exception('playerinfo is required');
}

if (!isset($sector)) {
    throw new \Exception('sector is required');
}

$messages = [];
$messages[] = $l_sf_attacking;

$targetfighters = $total_sector_fighters;
$playerbeams = NUM_BEAMS($playerinfo['beams']);

if ($calledfrom == 'rsmove.php') {
    $playerinfo['ship_energy'] += $energyscooped;
}

if ($playerbeams > $playerinfo['ship_energy']) {
    $playerbeams = $playerinfo['ship_energy'];
}
$playerinfo['ship_energy'] = $playerinfo['ship_energy'] - $playerbeams;
$playershields = NUM_SHIELDS($playerinfo['shields']);

if ($playershields > $playerinfo['ship_energy']) {
    $playershields = $playerinfo['ship_energy'];
}
//                    $playerinfo['ship_energy']=$playerinfo['ship_energy']-$playershields;
$playertorpnum = round(mypw($level_factor, $playerinfo['torp_launchers'])) * 2;
if ($playertorpnum > $playerinfo['torps']) {
    $playertorpnum = $playerinfo['torps'];
}

$playertorpdmg = $torp_dmg_rate * $playertorpnum;
$playerarmor = $playerinfo['armor_pts'];
$playerfighters = $playerinfo['ship_fighters'];

if ($targetfighters > 0 && $playerbeams > 0) {
    if ($playerbeams > round($targetfighters / 2)) {
        $temp = round($targetfighters / 2);
        $lost = $targetfighters - $temp;
        $messages[] = str_replace("[lost]", $lost, $l_sf_destfight);
        $targetfighters = $temp;
        $playerbeams = $playerbeams - $lost;
    } else {
        $targetfighters = $targetfighters - $playerbeams;
        $messages[] = str_replace("[lost]", $playerbeams, $l_sf_destfightb);
        $playerbeams = 0;
    }
}

$messages[] = $l_sf_torphit;

if ($targetfighters > 0 && $playertorpdmg > 0) {
    if ($playertorpdmg > round($targetfighters / 2)) {
        $temp = round($targetfighters / 2);
        $lost = $targetfighters - $temp;
        $messages[] = str_replace("[lost]", $lost, $l_sf_destfightt);
        $targetfighters = $temp;
        $playertorpdmg = $playertorpdmg - $lost;
    } else {
        $targetfighters = $targetfighters - $playertorpdmg;
        $messages[] = str_replace("[lost]", $playertorpdmg, $l_sf_destfightt);
        $playertorpdmg = 0;
    }
}
$messages[] = $l_sf_fighthit;

if ($playerfighters > 0 && $targetfighters > 0) {
    if ($playerfighters > $targetfighters) {
        $messages[] = $l_sf_destfightall;
        $temptargfighters = 0;
    } else {
        $messages[] = str_replace("[lost]", $playerfighters, $l_sf_destfightt2);
        $temptargfighters = $targetfighters - $playerfighters;
    }
    if ($targetfighters > $playerfighters) {
        $messages[] = $l_sf_lostfight;
        $tempplayfighters = 0;
    } else {
        $messages[] = str_replace("[lost]", $targetfighters, $l_sf_lostfight2);
        $tempplayfighters = $playerfighters - $targetfighters;
    }
    $playerfighters = $tempplayfighters;
    $targetfighters = $temptargfighters;
}

if ($targetfighters > 0) {
    if ($targetfighters > $playerarmor) {
        $playerarmor = 0;
        $messages[] = $l_sf_armorbreach;
    } else {
        $playerarmor = $playerarmor - $targetfighters;
        $messages[] = str_replace("[lost]", $targetfighters, $l_sf_armorbreach2);
    }
}

$fighterslost = $total_sector_fighters - $targetfighters;
destroy_fighters($sector, $fighterslost);
message_defence_owner($sector, strtr($l_sf_sendlog, [
    "[player]" => $playerinfo['character_name'],
    "[lost]" => $fighterslost,
    "[sector]" => $sector
]));
playerlog($playerinfo['ship_id'], \BNT\Log\LogTypeConstants::LOG_DEFS_DESTROYED_F, "$fighterslost|$sector");
$armor_lost = $playerinfo['armor_pts'] - $playerarmor;
$fighters_lost = $playerinfo['ship_fighters'] - $playerfighters;
$energy = $playerinfo['ship_energy'];

$update4b = $db->adoExecute("UPDATE ships SET ship_energy=$energy,ship_fighters=ship_fighters-$fighters_lost, armor_pts=armor_pts-$armor_lost, torps=torps-$playertorpnum WHERE ship_id=$playerinfo[ship_id]");

$messages[] = strtr($l_sf_lreport, [
    "[armor]" => $armor_lost,
    "[fighters]" => $fighters_lost,
    "[torps]" => $playertorpnum
]);

if ($playerarmor < 1) {
    $messages[] = $l_sf_shipdestroyed;
    playerlog($playerinfo[ship_id], \BNT\Log\LogTypeConstants::LOG_DEFS_KABOOM, "$sector|{$playerinfo['dev_escapepod']}");
    message_defence_owner($sector, strtr($l_sf_sendlog2, [
        "[player]" => $playerinfo['character_name'],
        "[sector]" => $sector
    ]));
    if ($playerinfo[dev_escapepod] == "Y") {
        $rating = round($playerinfo['rating'] / 2);
        $messages[] = $l_sf_escape;
        $db->adoExecute("UPDATE ships SET hull=0,engines=0,power=0,sensors=0,computer=0,beams=0,torp_launchers=0,torps=0,armor=0,armor_pts=100,cloak=0,shields=0,sector=0,ship_organics=0,ship_ore=0,ship_goods=0,ship_energy=$start_energy,ship_colonists=0,ship_fighters=100,dev_warpedit=0,dev_genesis=0,dev_beacon=0,dev_emerwarp=0,dev_escapepod='N',dev_fuelscoop='N',dev_minedeflector=0,on_planet='N',rating='$rating',cleared_defences=' ',dev_lssd='N' WHERE ship_id=$playerinfo[ship_id]");
        cancel_bounty($playerinfo['ship_id']);
    } else {
        cancel_bounty($playerinfo['ship_id']);
        db_kill_player($playerinfo['ship_id']);
    }
    
    throw new SectorFightShipDestroyedException(implode("\n", $messages));
}
