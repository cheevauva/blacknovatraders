<?php

include 'config.php';

$title = $l_att_title;
include("header.php");

if (checklogin()) {
    die();
}

$ship_id = intval(fromPost('ship_id', new \Exception('ship_id')));
$targetinfo = shipById($ship_id);

bigtitle();

$playerscore = gen_score($playerinfo['ship_id']);
$targetscore = gen_score($targetinfo['ship_id']);
$playerscore = $playerscore * $playerscore;
$targetscore = $targetscore * $targetscore;

if ($targetinfo['sector'] != $playerinfo['sector']) {
    throw new \Exception($l_att_notarg);
}

if ($targetinfo['on_planet'] == 'Y') {
    throw new \Exception($l_att_notarg);
} 

if ($playerinfo['turns'] < 1) {
    throw new \Exception($l_att_noturn);
}

$success = (10 - $targetinfo['cloak'] + $playerinfo['sensors']) * 5;

if ($success < 5) {
    $success = 5;
}
if ($success > 95) {
    $success = 95;
}

$flee = (10 - $targetinfo['engines'] + $playerinfo['engines']) * 5;
$roll = rand(1, 100);
$roll2 = rand(1, 100);

$targetSector = sectoryById($targetinfo['sector']);
$targetZone = zoneById($targetSector['zone_id']);

$zoneinfo = $targetZone;

if ($zoneinfo['allow_attack'] == 'N') {
    throw new \Exception($l_att_noatt);
}

if ($flee < $roll2) {
    $messages[] = $l_att_flee;
    shipTurn($playerinfo['ship_id'], 1);
    playerlog($targetinfo['ship_id'], LOG_ATTACK_OUTMAN, $playerinfo['character_name']);
    goto attackEnd;
}

if ($roll > $success) {
    $messages[] = $l_planet_noscan;
    shipTurn($playerinfo['ship_id'], 1);
    playerlog($targetinfo['ship_id'], LOG_ATTACK_OUTSCAN, $playerinfo['character_name']);
    goto attackEnd;
}

$shipavg = shipScore($targetship);

if ($shipavg > $ewd_maxhullsize) {
    $chance = ($shipavg - $ewd_maxhullsize) * 10;
} else {
    $chance = 0;
}

$random_value = rand(1, 100);

if ($targetinfo['dev_emerwarp'] > 0 && $random_value > $chance) {
    $rating_change = round($targetinfo['rating'] * .1);
    $dest_sector = rand(1, $sector_max);

    $playerinfo['turns'] -= 1;
    $playerinfo['turns_used'] += 1;
    $playerinfo['rating'] -= $rating_change;

    shipUpdate($playerinfo['ship_id'], $playerinfo);
    playerlog($targetinfo['ship_id'], LOG_ATTACK_EWD, $playerinfo['character_name']);

    $targetinfo['sector'] = $dest_sector;
    $targetinfo['dev_emerwarp'] -= 1;
    $targetinfo['cleared_defences'] = '';

    shipUpdate($targetinfo['ship_id'], $targetinfo);

    log_move($targetinfo['ship_id'], $dest_sector);
    $messages[] = $l_att_ewd;
    goto attackEnd;
}

if (($targetscore / $playerscore < $bounty_ratio || $targetinfo['turns_used'] < $bounty_minturns) && (preg_match("/(\@xenobe)$/", $targetinfo['email']) !== false)) { // bounty-free Xenobe attacking allowed.
    //changed xen check to a regexp cause a player could put @xen or whatever in his email address
    // so (\@xenobe) is an exact match and the $ symbol means "this is the *end* of the string
    //so our custom @xenobe names will match, nothing else will
    // Check to see if there is Federation bounty on the player. If there is, people can attack regardless.
    $btyamount = db()->fetch("SELECT SUM(amount) AS btytotal FROM bounty WHERE bounty_on = :ship_id AND placed_by = 0", [
        'ship_id' => $targetinfo['ship_id']
    ]);
    if ($btyamount <= 0) {
        $bounty = round($playerscore * $bounty_maxvalue);
        bountyCreate([
            'bounty_on' => $playerinfo['ship_id'],
            'placed_by' => 0,
            'amount' => $bounty,
        ]);
        playerlog($playerinfo['ship_id'], LOG_BOUNTY_FEDBOUNTY, $bounty);
        $messages[] = $l_by_fedbounty2;
    }
}

if ($targetinfo['dev_emerwarp'] > 0) {
    playerlog($targetinfo['ship_id'], LOG_ATTACK_EWDFAIL, $playerinfo['character_name']);
}

$targetenergy = $targetinfo['ship_energy'];
$playerenergy = $playerinfo['ship_energy'];
$targetbeams = NUM_BEAMS($targetinfo['beams']);

if ($targetbeams > $targetinfo['ship_energy']) {
    $targetbeams = $targetinfo['ship_energy'];
}

$targetinfo['ship_energy'] = $targetinfo['ship_energy'] - $targetbeams;
$playerbeams = NUM_BEAMS($playerinfo['beams']);

if ($playerbeams > $playerinfo['ship_energy']) {
    $playerbeams = $playerinfo['ship_energy'];
}

$playerinfo['ship_energy'] = $playerinfo['ship_energy'] - $playerbeams;
$playershields = NUM_SHIELDS($playerinfo['shields']);

if ($playershields > $playerinfo['ship_energy']) {
    $playershields = $playerinfo['ship_energy'];
}

$playerinfo['ship_energy'] = $playerinfo['ship_energy'] - $playershields;
$targetshields = NUM_SHIELDS($targetinfo['shields']);

if ($targetshields > $targetinfo['ship_energy']) {
    $targetshields = $targetinfo['ship_energy'];
}

$targetinfo['ship_energy'] = $targetinfo['ship_energy'] - $targetshields;

$playertorpnum = round(mypw($level_factor, $playerinfo['torp_launchers'])) * 10;

if ($playertorpnum > $playerinfo['torps']) {
    $playertorpnum = $playerinfo['torps'];
}

$targettorpnum = round(mypw($level_factor, $targetinfo['torp_launchers'])) * 10;

if ($targettorpnum > $targetinfo['torps']) {
    $targettorpnum = $targetinfo['torps'];
}

$playertorpdmg = $torp_dmg_rate * $playertorpnum;
$targettorpdmg = $torp_dmg_rate * $targettorpnum;
$playerarmor = $playerinfo['armor_pts'];
$targetarmor = $targetinfo['armor_pts'];
$playerfighters = $playerinfo['ship_fighters'];
$targetfighters = $targetinfo['ship_fighters'];
$targetdestroyed = 0;
$playerdestroyed = 0;

$messages[] = implode(' ', [$l_att_att, $targetinfo['character_name'], $l_abord, $targetinfo['ship_name']]);
$messages[] = implode(' ', [
    'You',
    sprintf('Beams(lvl): %s(%s)', $playerbeams, $playerinfo['beams']),
    sprintf('Shields(lvl): %s(%s)', $playershields, $playerinfo['shields']),
    sprintf('Energy(Start): %s(%s)', $playerinfo['ship_energy'], $playerenergy),
    sprintf('Torps(lvl): %s(%s)', $playertorpnum, $playerinfo['torp_launchers']),
    sprintf('TorpDmg: %s', $playertorpdmg),
    sprintf('Fighters(lvl): %s', $playerfighters),
    sprintf('Armor(lvl): %s', $playerarmor, $playerinfo['armor']),
    sprintf('Does have Pod? %s', $playerinfo['dev_escapepod']),
]);
$messages[] = implode(' ', [
    'Target',
    sprintf('Beams(lvl): %s(%s)', $targetbeams, $targetinfo['beams']),
    sprintf('Shields(lvl): %s(%s)', $targetshields, $targetinfo['shields']),
    sprintf('Energy(Start): %s(%s)', $targetinfo['ship_energy'], $targetenergy),
    sprintf('Torps(lvl): %s(%s)', $targettorpnum, $targetinfo['torp_launchers']),
    sprintf('TorpDmg: %s', $targettorpdmg),
    sprintf('Fighters(lvl): %s', $targetfighters),
    sprintf('Armor(lvl): %s', $targetarmor, $targetinfo['armor']),
    sprintf('Does have Pod? %s', $targetinfo['dev_escapepod']),
]);
$messages[] = $l_att_beams;

if ($targetfighters > 0 && $playerbeams > 0) {
    if ($playerbeams > round($targetfighters / 2)) {
        $temp = round($targetfighters / 2);
        $lost = $targetfighters - $temp;
        $messages[] = implode(' ', [$targetinfo['character_name'], $l_att_lost, $lost, $l_fighters]);
        $targetfighters = $temp;
        $playerbeams = $playerbeams - $lost;
    } else {
        $targetfighters = $targetfighters - $playerbeams;
        $messages[] = implode(' ', [$targetinfo['character_name'], $l_att_lost, $playerbeams, $l_fighters]);
        $playerbeams = 0;
    }
}
if ($playerfighters > 0 && $targetbeams > 0) {
    if ($targetbeams > round($playerfighters / 2)) {
        $temp = round($playerfighters / 2);
        $lost = $playerfighters - $temp;
        $messages[] = implode(' ', [$l_att_ylost, $lost, $l_fighters]);
        $playerfighters = $temp;
        $targetbeams = $targetbeams - $lost;
    } else {
        $playerfighters = $playerfighters - $targetbeams;
        $messages[] = implode(' ', [$l_att_ylost, $targetbeams, $l_fighters]);
        $targetbeams = 0;
    }
}

if ($playerbeams > 0) {
    if ($playerbeams > $targetshields) {
        $playerbeams = $playerbeams - $targetshields;
        $targetshields = 0;
        $messages[] = implode(' ', [$targetinfo['character_name'], $l_att_sdown]);
    } else {
        $messages[] = implode(' ', [$targetinfo['character_name'], $l_att_shits, $playerbeams, $l_att_dmg]);
        $targetshields = $targetshields - $playerbeams;
        $playerbeams = 0;
    }
}

if ($targetbeams > 0) {
    if ($targetbeams > $playershields) {
        $targetbeams = $targetbeams - $playershields;
        $playershields = 0;
        $messages[] = $l_att_ydown;
    } else {
        $messages[] = implode(' ', [$l_att_yhits, $targetbeams, $l_att_dmg]);
        $playershields = $playershields - $targetbeams;
        $targetbeams = 0;
    }
}

if ($playerbeams > 0) {
    if ($playerbeams > $targetarmor) {
        $targetarmor = 0;
        $messages[] = implode(' ', [$targetinfo['character_name'], $l_att_sarm]);
    } else {
        $targetarmor = $targetarmor - $playerbeams;
        $messages[] = implode(' ', [$targetinfo['character_name'], $l_att_ashit, $playerbeams, $l_att_dmg]);
    }
}

if ($targetbeams > 0) {
    if ($targetbeams > $playerarmor) {
        $playerarmor = 0;
        $messages[] = $l_att_yarm;
    } else {
        $playerarmor = $playerarmor - $targetbeams;
        $messages[] = implode(' ', [$l_att_ayhit, $targetbeams, $l_att_dmg]);
    }
}

$messages[] = $l_att_torps;

if ($targetfighters > 0 && $playertorpdmg > 0) {
    if ($playertorpdmg > round($targetfighters / 2)) {
        $temp = round($targetfighters / 2);
        $lost = $targetfighters - $temp;
        $messages[] = implode(' ', [$targetinfo['character_name'], $l_att_lost, $lost, $l_fighters]);
        $targetfighters = $temp;
        $playertorpdmg = $playertorpdmg - $lost;
    } else {
        $targetfighters = $targetfighters - $playertorpdmg;
        $messages[] = implode(' ', [$targetinfo['character_name'], $l_att_lost, $playertorpdmg, $l_fighters]);
        $playertorpdmg = 0;
    }
}

if ($playerfighters > 0 && $targettorpdmg > 0) {
    if ($targettorpdmg > round($playerfighters / 2)) {
        $temp = round($playerfighters / 2);
        $lost = $playerfighters - $temp;
        $messages[] = implode(' ', [$l_att_ylost, $lost, $l_fighters]);
        $messages[] = implode(' ', [$temp, $playerfighters, $targettorpdmg]);
        $playerfighters = $temp;
        $targettorpdmg = $targettorpdmg - $lost;
    } else {
        $playerfighters = $playerfighters - $targettorpdmg;
        $messages[] = implode(' ', [$l_att_ylost, $targettorpdmg, $l_fighters]);
        $targettorpdmg = 0;
    }
}

if ($playertorpdmg > 0) {
    if ($playertorpdmg > $targetarmor) {
        $targetarmor = 0;
        $messages[] = implode(' ', [$targetinfo['character_name'], $l_att_sarm]);
    } else {
        $targetarmor = $targetarmor - $playertorpdmg;
        $messages[] = implode(' ', [$targetinfo['character_name'], $l_att_ashit, $playertorpdmg, $l_att_dmg]);
    }
}

if ($targettorpdmg > 0) {
    if ($targettorpdmg > $playerarmor) {
        $playerarmor = 0;
        $messages[] = $l_att_yarm;
    } else {
        $playerarmor = $playerarmor - $targettorpdmg;
        $messages[] = implode(' ', [$l_att_ayhit, $targettorpdmg, $l_att_dmg]);
    }
}

$messages[] = $l_att_fighters;

if ($playerfighters > 0 && $targetfighters > 0) {
    if ($playerfighters > $targetfighters) {
        $messages[] = implode(' ', [$targetinfo['character_name'], $l_att_lostf]);
        $temptargfighters = 0;
    } else {
        $messages[] = implode(' ', [$targetinfo['character_name'], $l_att_lost, $playerfighters, $l_fighters]);
        $temptargfighters = $targetfighters - $playerfighters;
    }

    if ($targetfighters > $playerfighters) {
        $messages[] = $l_att_ylostf;
        $tempplayfighters = 0;
    } else {
        $messages[] = implode(' ', [$l_att_ylost, $targetfighters, $l_fighters]);
        $tempplayfighters = $playerfighters - $targetfighters;
    }
    $playerfighters = $tempplayfighters;
    $targetfighters = $temptargfighters;
}

if ($playerfighters > 0) {
    if ($playerfighters > $targetarmor) {
        $targetarmor = 0;
        $messages[] = implode(' ', [$targetinfo['character_name'], $l_att_sarm]);
    } else {
        $targetarmor = $targetarmor - $playerfighters;
        $messages[] = implode(' ', [$targetinfo['character_name'], $l_att_ashit, $playerfighters, $l_att_dmg]);
    }
}

if ($targetfighters > 0) {
    if ($targetfighters > $playerarmor) {
        $playerarmor = 0;
        $messages[] = $l_att_yarm;
    } else {
        $playerarmor = $playerarmor - $targetfighters;
        $messages[] = implode(' ', [$l_att_ayhit, $targetfighters, $l_att_dmg]);
    }
}

if ($targetarmor < 1) {
    $messages[] = implode(' ', [$targetinfo['character_name'], $l_att_sdest]);

    if ($targetinfo['dev_escapepod'] == "Y") {
        $messages[] = $l_att_espod;
        
        $targetinfo['rating'] /= 2;
        $targetinfo = shipEscapePod($playerinfo);

        playerlog($targetinfo['ship_id'], LOG_ATTACK_LOSE, [$playerinfo['character_name'], 'Y']);
        collect_bounty($playerinfo['ship_id'], $targetinfo['ship_id']);
    } else {
        playerlog($targetinfo['ship_id'], LOG_ATTACK_LOSE, [$playerinfo['character_name'], 'N']);
        db_kill_player($targetinfo['ship_id']);
        collect_bounty($playerinfo['ship_id'], $targetinfo['ship_id']);
    }

    if ($playerarmor > 0) {
        $rating_change = round($targetinfo['rating'] * $rating_combat_factor);
        //Updating to always get a positive rating increase for xenobe and the credits they are carrying - rjordan
        $salv_credits = 0;

        if (preg_match("/(\@xenobe)$/", $targetinfo['email']) !== false) {
            //*** He's a Xenobe ***
            updateXenobe($targetinfo['email'], [
                'active' => 'N',
            ]);

            if ($rating_change > 0) {
                $rating_change = 0 - $rating_change;
                playerlog($targetinfo['ship_id'], LOG_ATTACK_LOSE, [$playerinfo['character_name'], 'N']);
                collect_bounty($playerinfo['ship_id'], $targetinfo['ship_id']);
                db_kill_player($targetinfo['ship_id']);
            }
            $salv_credits = $targetinfo['credits'];
        }

        $free_ore = round($targetinfo['ship_ore'] / 2);
        $free_organics = round($targetinfo['ship_organics'] / 2);
        $free_goods = round($targetinfo['ship_goods'] / 2);
        $free_holds = NUM_HOLDS($playerinfo['hull']) - $playerinfo['ship_ore'] - $playerinfo['ship_organics'] - $playerinfo['ship_goods'] - $playerinfo['ship_colonists'];

        if ($free_holds > $free_goods) {
            $salv_goods = $free_goods;
            $free_holds = $free_holds - $free_goods;
        } elseif ($free_holds > 0) {
            $salv_goods = $free_holds;
            $free_holds = 0;
        } else {
            $salv_goods = 0;
        }
        if ($free_holds > $free_ore) {
            $salv_ore = $free_ore;
            $free_holds = $free_holds - $free_ore;
        } elseif ($free_holds > 0) {
            $salv_ore = $free_holds;
            $free_holds = 0;
        } else {
            $salv_ore = 0;
        }
        if ($free_holds > $free_organics) {
            $salv_organics = $free_organics;
            $free_holds = $free_holds - $free_organics;
        } elseif ($free_holds > 0) {
            $salv_organics = $free_holds;
            $free_holds = 0;
        } else {
            $salv_organics = 0;
        }

        $ship_value = $upgrade_cost * (round(mypw($upgrade_factor, $targetinfo['hull'])) + round(mypw($upgrade_factor, $targetinfo['engines'])) + round(mypw($upgrade_factor, $targetinfo['power'])) + round(mypw($upgrade_factor, $targetinfo['computer'])) + round(mypw($upgrade_factor, $targetinfo['sensors'])) + round(mypw($upgrade_factor, $targetinfo['beams'])) + round(mypw($upgrade_factor, $targetinfo['torp_launchers'])) + round(mypw($upgrade_factor, $targetinfo['shields'])) + round(mypw($upgrade_factor, $targetinfo['armor'])) + round(mypw($upgrade_factor, $targetinfo['cloak'])));
        $ship_salvage_rate = rand(10, 20);
        $ship_salvage = $ship_value * $ship_salvage_rate / 100 + $salv_credits;  //added credits for xenobe - 0 if normal player - GunSlinger

        $messages[] = strtr($l_att_ysalv, [
            '[salv_ore]' => $salv_ore,
            '[salv_organics]' => $salv_organics,
            '[salv_goods]' => $salv_goods,
            '[ship_salvage_rate]' => $ship_salvage_rate,
            '[ship_salvage]' => $ship_salvage,
            '[rating_change]' => NUMBER(abs($rating_change)),
        ]);

        $armor_lost = $playerinfo['armor_pts'] - $playerarmor;
        $fighters_lost = $playerinfo['ship_fighters'] - $playerfighters;
        $energy = $playerinfo['ship_energy'];

        $playerinfo['ship_ore'] += $salv_ore;
        $playerinfo['ship_organics'] += $salv_organics;
        $playerinfo['ship_goods'] += $salv_goods;
        $playerinfo['credits'] += $ship_salvage;
        $playerinfo['ship_energy'] = $energy;
        $playerinfo['ship_fighters'] -= $fighters_lost;
        $playerinfo['armor_pts'] -= $armor_lost;
        $playerinfo['torps'] -= $playertorpnum;
        $playerinfo['turns'] -= 1;
        $playerinfo['turns_used'] += 1;
        $playerinfo['rating'] -= $rating_change;

        shipUpdate($playerinfo['ship_id'], $playerinfo);

        $messages[] = implode(' ', [$l_att_ylost, $armor_lost, $l_armorpts, $fighters_lost, ',', $l_fighters, $l_att_andused, $playertorpnum, $l_torps]);
    }
} else {
    $messages[] = str_replace("[name]", $targetinfo['character_name'], $l_att_stilship);

    $rating_change = round($targetinfo['rating'] * .1);
    $armor_lost = $targetinfo['armor_pts'] - $targetarmor;
    $fighters_lost = $targetinfo['ship_fighters'] - $targetfighters;

    playerlog($targetinfo['ship_id'], LOG_ATTACKED_WIN, [$playerinfo['character_name'], $armor_lost, $fighters_lost]);

    $targetinfo['ship_fighters'] -= $fighters_lost;
    $targetinfo['armor_pts'] -= $armor_lost;
    $targetinfo['torps'] -= $targettorpnum;

    shipUpdate($targetinfo['ship_id'], $targetinfo);

    $armor_lost = $playerinfo['armor_pts'] - $playerarmor;
    $fighters_lost = $playerinfo['ship_fighters'] - $playerfighters;

    $playerinfo['ship_energy'] = $energy;
    $playerinfo['ship_fighters'] -= $fighters_lost;
    $playerinfo['armor_pts'] -= $armor_lost;
    $playerinfo['torps'] -= $playertorpnum;
    $playerinfo['turns'] -= 1;
    $playerinfo['turns_used'] += 1;
    $playerinfo['rating'] -= $rating_change;

    shipUpdate($playerinfo['ship_id'], $playerinfo);

    $messages[] = implode(' ', [$l_att_ylost, $armor_lost, $l_armorpts, $fighters_lost, $l_fighters, ',', $l_att_andused, $playertorpnum, $l_torps]);
}

if ($playerarmor < 1) {
    $messages[] = $l_att_yshiplost;
    
    if ($playerinfo['dev_escapepod'] == "Y") {
        $messages[] = $l_att_loosepod;
        
        $playerinfo['rating'] /= 2;
        $playerinfo = shipEscapePod($playerinfo);
        
        collect_bounty($targetinfo['ship_id'], $playerinfo['ship_id']);
    } else {
        $messages[] = 'Didnt have pod?! ' . $playerinfo['dev_escapepod'];
        db_kill_player($playerinfo['ship_id']);
        collect_bounty($targetinfo['ship_id'], $playerinfo['ship_id']);
    }
    
    if ($targetarmor > 0) {
        $free_ore = round($playerinfo[ship_ore] / 2);
        $free_organics = round($playerinfo[ship_organics] / 2);
        $free_goods = round($playerinfo[ship_goods] / 2);
        $free_holds = NUM_HOLDS($targetinfo[hull]) - $targetinfo[ship_ore] - $targetinfo[ship_organics] - $targetinfo[ship_goods] - $targetinfo[ship_colonists];
        if ($free_holds > $free_goods) {
            $salv_goods = $free_goods;
            $free_holds = $free_holds - $free_goods;
        } elseif ($free_holds > 0) {
            $salv_goods = $free_holds;
            $free_holds = 0;
        } else {
            $salv_goods = 0;
        }
        if ($free_holds > $free_ore) {
            $salv_ore = $free_ore;
            $free_holds = $free_holds - $free_ore;
        } elseif ($free_holds > 0) {
            $salv_ore = $free_holds;
            $free_holds = 0;
        } else {
            $salv_ore = 0;
        }
        if ($free_holds > $free_organics) {
            $salv_organics = $free_organics;
            $free_holds = $free_holds - $free_organics;
        } elseif ($free_holds > 0) {
            $salv_organics = $free_holds;
            $free_holds = 0;
        } else {
            $salv_organics = 0;
        }

        $ship_value = $upgrade_cost * (round(mypw($upgrade_factor, $playerinfo[hull])) + round(mypw($upgrade_factor, $playerinfo[engines])) + round(mypw($upgrade_factor, $playerinfo[power])) + round(mypw($upgrade_factor, $playerinfo[computer])) + round(mypw($upgrade_factor, $playerinfo[sensors])) + round(mypw($upgrade_factor, $playerinfo[beams])) + round(mypw($upgrade_factor, $playerinfo[torp_launchers])) + round(mypw($upgrade_factor, $playerinfo[shields])) + round(mypw($upgrade_factor, $playerinfo[armor])) + round(mypw($upgrade_factor, $playerinfo[cloak])));
        $ship_salvage_rate = rand(10, 20);
        $ship_salvage = $ship_value * $ship_salvage_rate / 100 + $salv_credits;  //added credits for xenobe - 0 if normal player - GunSlinger

        $messages[] = strtr($l_att_salv, [
            '[salv_ore]' => $salv_ore,
            '[salv_organics]' => $salv_organics,
            '[salv_goods]' => $salv_goods,
            '[ship_salvage_rate]' => $ship_salvage_rate,
            '[ship_salvage]' => $ship_salvage,
            '[name]' => $targetinfo['character_name'],
        ]);

        $targetinfo['credits'] += $ship_salvage;
        $targetinfo['ship_ore'] += $salv_ore;
        $targetinfo['ship_organics'] += $salv_organics;
        $targetinfo['ship_goods'] += $salv_goods;

        $armor_lost = $targetinfo['armor_pts'] - $targetarmor;
        $fighters_lost = $targetinfo['ship_fighters'] - $targetfighters;
        $energy = $targetinfo['ship_energy'];

        $targetinfo['ship_fighters'] -= $fighters_lost;
        $targetinfo['armor_pts'] -= $armor_lost;
        $targetinfo['torps'] -= $targettorpnum;

        shipUpdate($targetinfo['ship_id'], $targetinfo);
    }
}

attackEnd:

TEXT_GOTOMAIN();

include("footer.php");
