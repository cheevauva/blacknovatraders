<?php

use BNT\Log\LogTypeConstants;
use BNT\Log\DAO\LogPlayerDAO;

function xenobetoship($ship_id)
{
    global $attackerbeams;
    global $attackerfighters;
    global $attackershields;
    global $attackertorps;
    global $attackerarmor;
    global $attackertorpdamage;
    global $start_energy;
    global $playerinfo;
    global $rating_combat_factor;
    global $upgrade_cost;
    global $upgrade_factor;
    global $sector_max;
    global $xenobeisdead;
    global $container;

    $targetinfo = db()->fetch("SELECT * FROM ships WHERE ship_id = :ship_id", [
        'ship_id' => $ship_id
    ]);

    if (strstr($targetinfo['email'], '@xenobe')) {
        return;
    }

    $sectrow = db()->fetch("SELECT sector_id,zone_id FROM universe WHERE sector_id = :sector_id", [
        'sector_id' => $targetinfo['sector']
    ]);

    $zonerow = db()->fetch("SELECT zone_id,allow_attack FROM zones WHERE zone_id = :zone_id", [
        'zone_id' => $sectrow['zone_id']
    ]);

    if ($zonerow['allow_attack'] == "N") {
        LogPlayerDAO::call($container, $playerinfo['ship_id'], LogTypeConstants::LOG_RAW, "Attack failed, you are in a sector that prohibits attacks.");
        return;
    }

    if ($targetinfo['dev_emerwarp'] > 0) {
        LogPlayerDAO::call($container, $targetinfo['ship_id'], LogTypeConstants::LOG_ATTACK_EWD, sprintf("Xenobe %s", $playerinfo['character_name']));
        $dest_sector = rand(0, $sector_max);
        db()->q("UPDATE ships SET sector = :sector, dev_emerwarp = dev_emerwarp - 1 WHERE ship_id = :ship_id", [
            'sector' => $dest_sector,
            'ship_id' => $targetinfo['ship_id']
        ]);
        return;
    }

    $attackerbeams = NUM_BEAMS($playerinfo['beams']);
    if ($attackerbeams > $playerinfo['ship_energy']) {
        $attackerbeams = $playerinfo['ship_energy'];
    }
    $playerinfo['ship_energy'] = $playerinfo['ship_energy'] - $attackerbeams;
    $attackershields = NUM_SHIELDS($playerinfo['shields']);
    if ($attackershields > $playerinfo['ship_energy']) {
        $attackershields = $playerinfo['ship_energy'];
    }
    $playerinfo['ship_energy'] = $playerinfo['ship_energy'] - $attackershields;
    $attackertorps = round(mypw($level_factor, $playerinfo['torp_launchers'])) * 2;
    if ($attackertorps > $playerinfo['torps']) {
        $attackertorps = $playerinfo['torps'];
    }
    $playerinfo['torps'] = $playerinfo['torps'] - $attackertorps;
    $attackertorpdamage = $torp_dmg_rate * $attackertorps;
    $attackerarmor = $playerinfo['armor_pts'];
    $attackerfighters = $playerinfo['ship_fighters'];
    $playerdestroyed = 0;

    $targetbeams = NUM_BEAMS($targetinfo['beams']);
    if ($targetbeams > $targetinfo['ship_energy']) {
        $targetbeams = $targetinfo['ship_energy'];
    }
    $targetinfo['ship_energy'] = $targetinfo['ship_energy'] - $targetbeams;
    $targetshields = NUM_SHIELDS($targetinfo['shields']);
    if ($targetshields > $targetinfo['ship_energy']) {
        $targetshields = $targetinfo['ship_energy'];
    }
    $targetinfo['ship_energy'] = $targetinfo['ship_energy'] - $targetshields;
    $targettorpnum = round(mypw($level_factor, $targetinfo['torp_launchers'])) * 2;
    if ($targettorpnum > $targetinfo['torps']) {
        $targettorpnum = $targetinfo['torps'];
    }
    $targetinfo['torps'] = $targetinfo['torps'] - $targettorpnum;
    $targettorpdmg = $torp_dmg_rate * $targettorpnum;
    $targetarmor = $targetinfo['armor_pts'];
    $targetfighters = $targetinfo['ship_fighters'];
    $targetdestroyed = 0;

    if ($attackerbeams > 0 && $targetfighters > 0) {
        if ($attackerbeams > round($targetfighters / 2)) {
            $lost = $targetfighters - (round($targetfighters / 2));
            $targetfighters = $targetfighters - $lost;
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
    if ($attackerbeams > 0) {
        if ($attackerbeams > $targetarmor) {
            $attackerbeams = $attackerbeams - $targetarmor;
            $targetarmor = 0;
        } else {
            $targetarmor = $targetarmor - $attackerbeams;
            $attackerbeams = 0;
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
        if ($attackertorpdamage > round($targetfighters / 2)) {
            $lost = $targetfighters - (round($targetfighters / 2));
            $targetfighters = $targetfighters - $lost;
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
    if ($attackertorpdamage > 0) {
        if ($attackertorpdamage > $targetarmor) {
            $attackertorpdamage = $attackertorpdamage - $targetarmor;
            $targetarmor = 0;
        } else {
            $targetarmor = $targetarmor - $attackertorpdamage;
            $attackertorpdamage = 0;
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
    if ($attackerfighters > 0) {
        if ($attackerfighters > $targetarmor) {
            $targetarmor = 0;
        } else {
            $targetarmor = $targetarmor - $attackerfighters;
        }
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
    if ($targettorpnum < 0) {
        $targettorpnum = 0;
    }
    if ($targetshields < 0) {
        $targetshields = 0;
    }
    if ($targetbeams < 0) {
        $targetbeams = 0;
    }
    if ($targetarmor < 0) {
        $targetarmor = 0;
    }

    if (!$targetarmor > 0) {
        if ($targetinfo['dev_escapepod'] == "Y") {
            $rating = round($targetinfo['rating'] / 2);
            db()->q("UPDATE ships SET hull=0, engines=0, power=0, computer=0,sensors=0, beams=0, torp_launchers=0, torps=0, armor=0, armor_pts=100, cloak=0, shields=0, sector=0, ship_ore=0, ship_organics=0, ship_energy=1000, ship_colonists=0, ship_goods=0, ship_fighters=100, ship_damage=0, on_planet='N', planet_id=0, dev_warpedit=0, dev_genesis=0, dev_beacon=0, dev_emerwarp=0, dev_escapepod='N', dev_fuelscoop='N', dev_minedeflector=0, ship_destroyed='N', rating=:rating, dev_lssd='N' WHERE ship_id = :ship_id", [
                'rating' => $rating,
                'ship_id' => $targetinfo['ship_id']
            ]);
            LogPlayerDAO::call($container, $targetinfo['ship_id'], LogTypeConstants::LOG_ATTACK_LOSE, sprintf("Xenobe %s|Y", $playerinfo['character_name']));
        } else {
            LogPlayerDAO::call($container, $targetinfo['ship_id'], LogTypeConstants::LOG_ATTACK_LOSE, sprintf("Xenobe %s|N", $playerinfo['character_name']));
            db_kill_player($targetinfo['ship_id']);
        }
        if ($attackerarmor > 0) {
            $rating_change = round($targetinfo['rating'] * $rating_combat_factor);
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
            $ship_salvage = $ship_value * $ship_salvage_rate / 100;
            LogPlayerDAO::call($container, $playerinfo['ship_id'], LogTypeConstants::LOG_RAW, sprintf("Attack successful, %s was defeated and salvaged for %s credits.", $targetinfo['character_name'], $ship_salvage));
            db()->q("UPDATE ships SET ship_ore=ship_ore+:salv_ore, ship_organics=ship_organics+:salv_organics, ship_goods=ship_goods+:salv_goods, credits=credits+:ship_salvage WHERE ship_id = :ship_id", [
                'salv_ore' => $salv_ore,
                'salv_organics' => $salv_organics,
                'salv_goods' => $salv_goods,
                'ship_salvage' => $ship_salvage,
                'ship_id' => $playerinfo['ship_id']
            ]);
            $armor_lost = $playerinfo['armor_pts'] - $attackerarmor;
            $fighters_lost = $playerinfo['ship_fighters'] - $attackerfighters;
            $energy = $playerinfo['ship_energy'];
            db()->q("UPDATE ships SET ship_energy=:energy,ship_fighters=ship_fighters-:fighters_lost, torps=torps-:attackertorps,armor_pts=armor_pts-:armor_lost, rating=rating-:rating_change WHERE ship_id = :ship_id", [
                'energy' => $energy,
                'fighters_lost' => $fighters_lost,
                'attackertorps' => $attackertorps,
                'armor_lost' => $armor_lost,
                'rating_change' => $rating_change,
                'ship_id' => $playerinfo['ship_id']
            ]);
        }
    }

    if ($targetarmor > 0 && $attackerarmor > 0) {
        $rating_change = round($targetinfo['rating'] * .1);
        $armor_lost = $playerinfo['armor_pts'] - $attackerarmor;
        $fighters_lost = $playerinfo['ship_fighters'] - $attackerfighters;
        $energy = $playerinfo['ship_energy'];
        $target_rating_change = round($targetinfo['rating'] / 2);
        $target_armor_lost = $targetinfo['armor_pts'] - $targetarmor;
        $target_fighters_lost = $targetinfo['ship_fighters'] - $targetfighters;
        $target_energy = $targetinfo['ship_energy'];
        LogPlayerDAO::call($container, $playerinfo['ship_id'], LogTypeConstants::LOG_RAW, sprintf("Attack failed, %s survived.", $targetinfo['character_name']));
        LogPlayerDAO::call($container, $targetinfo['ship_id'], LogTypeConstants::LOG_ATTACK_WIN, sprintf("Xenobe %s|%s|%s", $playerinfo['character_name'], $target_armor_lost, $target_fighters_lost));
        db()->q("UPDATE ships SET ship_energy=:energy,ship_fighters=ship_fighters-:fighters_lost, torps=torps-:attackertorps,armor_pts=armor_pts-:armor_lost, rating=rating-:rating_change WHERE ship_id = :ship_id", [
            'energy' => $energy,
            'fighters_lost' => $fighters_lost,
            'attackertorps' => $attackertorps,
            'armor_lost' => $armor_lost,
            'rating_change' => $rating_change,
            'ship_id' => $playerinfo['ship_id']
        ]);
        db()->q("UPDATE ships SET ship_energy=:target_energy,ship_fighters=ship_fighters-:target_fighters_lost, armor_pts=armor_pts-:target_armor_lost, torps=torps-:targettorpnum, rating=:target_rating_change WHERE ship_id = :ship_id", [
            'target_energy' => $target_energy,
            'target_fighters_lost' => $target_fighters_lost,
            'target_armor_lost' => $target_armor_lost,
            'targettorpnum' => $targettorpnum,
            'target_rating_change' => $target_rating_change,
            'ship_id' => $targetinfo['ship_id']
        ]);
    }

    if (!$attackerarmor > 0) {
        LogPlayerDAO::call($container, $playerinfo['ship_id'], LogTypeConstants::LOG_RAW, sprintf("%s destroyed your ship!", $targetinfo['character_name']));
        db_kill_player($playerinfo['ship_id']);
        $xenobeisdead = 1;
        if ($targetarmor > 0) {
            $rating_change = round($playerinfo['rating'] * $rating_combat_factor);
            $free_ore = round($playerinfo['ship_ore'] / 2);
            $free_organics = round($playerinfo['ship_organics'] / 2);
            $free_goods = round($playerinfo['ship_goods'] / 2);
            $free_holds = NUM_HOLDS($targetinfo['hull']) - $targetinfo['ship_ore'] - $targetinfo['ship_organics'] - $targetinfo['ship_goods'] - $targetinfo['ship_colonists'];
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
            $ship_value = $upgrade_cost * (round(mypw($upgrade_factor, $playerinfo['hull'])) + round(mypw($upgrade_factor, $playerinfo['engines'])) + round(mypw($upgrade_factor, $playerinfo['power'])) + round(mypw($upgrade_factor, $playerinfo['computer'])) + round(mypw($upgrade_factor, $playerinfo['sensors'])) + round(mypw($upgrade_factor, $playerinfo['beams'])) + round(mypw($upgrade_factor, $playerinfo['torp_launchers'])) + round(mypw($upgrade_factor, $playerinfo['shields'])) + round(mypw($upgrade_factor, $playerinfo['armor'])) + round(mypw($upgrade_factor, $playerinfo['cloak'])));
            $ship_salvage_rate = rand(10, 20);
            $ship_salvage = $ship_value * $ship_salvage_rate / 100;
            LogPlayerDAO::call($container, $targetinfo['ship_id'], LogTypeConstants::LOG_ATTACK_WIN, sprintf("Xenobe %s|%s|%s", $playerinfo['character_name'], $armor_lost, $fighters_lost));
            LogPlayerDAO::call($container, $targetinfo['ship_id'], LogTypeConstants::LOG_RAW, sprintf("You destroyed the Xenobe ship and salvaged %s units of ore, %s units of organics, %s units of goods, and salvaged %s%% of the ship for %s credits.", $salv_ore, $salv_organics, $salv_goods, $ship_salvage_rate, $ship_salvage));
            db()->q("UPDATE ships SET ship_ore=ship_ore+:salv_ore, ship_organics=ship_organics+:salv_organics, ship_goods=ship_goods+:salv_goods, credits=credits+:ship_salvage WHERE ship_id = :ship_id", [
                'salv_ore' => $salv_ore,
                'salv_organics' => $salv_organics,
                'salv_goods' => $salv_goods,
                'ship_salvage' => $ship_salvage,
                'ship_id' => $targetinfo['ship_id']
            ]);
            $armor_lost = $targetinfo['armor_pts'] - $targetarmor;
            $fighters_lost = $targetinfo['ship_fighters'] - $targetfighters;
            $energy = $targetinfo['ship_energy'];
            db()->q("UPDATE ships SET ship_energy=:energy,ship_fighters=ship_fighters-:fighters_lost, torps=torps-:targettorpnum,armor_pts=armor_pts-:armor_lost, rating=rating-:rating_change WHERE ship_id = :ship_id", [
                'energy' => $energy,
                'fighters_lost' => $fighters_lost,
                'targettorpnum' => $targettorpnum,
                'armor_lost' => $armor_lost,
                'rating_change' => $rating_change,
                'ship_id' => $targetinfo['ship_id']
            ]);
        }
    }
}

function xenobetosecdef()
{
    global $playerinfo;
    global $targetlink;

    global $l_sf_sendlog;
    global $l_sf_sendlog2;
    global $l_chm_hehitminesinsector;
    global $l_chm_hewasdestroyedbyyourmines;

    global $xenobeisdead;
    global $container;

    if ($targetlink > 0) {
        $defences = [];
        $resultf = db()->fetchAll("SELECT * FROM sector_defence WHERE sector_id = :targetlink and defence_type = 'F' ORDER BY quantity DESC", [
            'targetlink' => $targetlink
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
        $resultm = db()->fetchAll("SELECT * FROM sector_defence WHERE sector_id = :targetlink and defence_type = 'M'", [
            'targetlink' => $targetlink
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
            LogPlayerDAO::call($container, $playerinfo['ship_id'], LogTypeConstants::LOG_RAW, sprintf("ATTACKING SECTOR DEFENCES %s fighters and %s mines.", $total_sector_fighters, $total_sector_mines));

            $targetfighters = $total_sector_fighters;
            $playerbeams = NUM_BEAMS($playerinfo['beams']);
            if ($playerbeams > $playerinfo['ship_energy']) {
                $playerbeams = $playerinfo['ship_energy'];
            }
            $playerinfo['ship_energy'] = $playerinfo['ship_energy'] - $playerbeams;
            $playershields = NUM_SHIELDS($playerinfo['shields']);
            if ($playershields > $playerinfo['ship_energy']) {
                $playershields = $playerinfo['ship_energy'];
            }
            $playertorpnum = round(mypw($level_factor, $playerinfo['torp_launchers'])) * 2;
            if ($playertorpnum > $playerinfo['torps']) {
                $playertorpnum = $playerinfo['torps'];
            }
            $playertorpdmg = $torp_dmg_rate * $playertorpnum;
            $playerarmor = $playerinfo['armor_pts'];
            $playerfighters = $playerinfo['ship_fighters'];
            $totalmines = $total_sector_mines;
            if ($totalmines > 1) {
                $roll = rand(1, $totalmines);
            } else {
                $roll = 1;
            }
            $totalmines = $totalmines - $roll;
            $playerminedeflect = $playerinfo['ship_fighters'];

            if ($targetfighters > 0 && $playerbeams > 0) {
                if ($playerbeams > round($targetfighters / 2)) {
                    $temp = round($targetfighters / 2);
                    $targetfighters = $temp;
                    $playerbeams = $playerbeams - $temp;
                } else {
                    $targetfighters = $targetfighters - $playerbeams;
                    $playerbeams = 0;
                }
            }

            if ($targetfighters > 0 && $playertorpdmg > 0) {
                if ($playertorpdmg > round($targetfighters / 2)) {
                    $temp = round($targetfighters / 2);
                    $targetfighters = $temp;
                    $playertorpdmg = $playertorpdmg - $temp;
                } else {
                    $targetfighters = $targetfighters - $playertorpdmg;
                    $playertorpdmg = 0;
                }
            }

            if ($playerfighters > 0 && $targetfighters > 0) {
                if ($playerfighters > $targetfighters) {
                    echo $l_sf_destfightall;
                    $temptargfighters = 0;
                } else {
                    $temptargfighters = $targetfighters - $playerfighters;
                }
                if ($targetfighters > $playerfighters) {
                    $tempplayfighters = 0;
                } else {
                    $tempplayfighters = $playerfighters - $targetfighters;
                }
                $playerfighters = $tempplayfighters;
                $targetfighters = $temptargfighters;
            }

            if ($targetfighters > 0) {
                if ($targetfighters > $playerarmor) {
                    $playerarmor = 0;
                } else {
                    $playerarmor = $playerarmor - $targetfighters;
                }
            }

            $fighterslost = $total_sector_fighters - $targetfighters;
            destroy_fighters($targetlink, $fighterslost);

            $l_sf_sendlog = sprintf($l_sf_sendlog, sprintf("Xenobe %s", $playerinfo['character_name']), $fighterslost, $targetlink);
            message_defence_owner($targetlink, $l_sf_sendlog);

            $armor_lost = $playerinfo['armor_pts'] - $playerarmor;
            $fighters_lost = $playerinfo['ship_fighters'] - $playerfighters;
            $energy = $playerinfo['ship_energy'];
            db()->q("UPDATE ships SET ship_energy = :energy, ship_fighters = ship_fighters - :fighters_lost, armor_pts = armor_pts - :armor_lost, torps = torps - :playertorpnum WHERE ship_id = :ship_id", [
                'energy' => $energy,
                'fighters_lost' => $fighters_lost,
                'armor_lost' => $armor_lost,
                'playertorpnum' => $playertorpnum,
                'ship_id' => $playerinfo['ship_id']
            ]);

            if ($playerarmor < 1) {
                $l_sf_sendlog2 = sprintf($l_sf_sendlog2, sprintf("Xenobe %s", $playerinfo['character_name']), $targetlink);
                message_defence_owner($targetlink, $l_sf_sendlog2);
                cancel_bounty($playerinfo['ship_id']);
                db_kill_player($playerinfo['ship_id']);
                $xenobeisdead = 1;
                return;
            }

            $l_chm_hehitminesinsector = sprintf($l_chm_hehitminesinsector, sprintf("Xenobe %s", $playerinfo['character_name']), $roll, $targetlink);
            message_defence_owner($targetlink, $l_chm_hehitminesinsector);

            if ($playerminedeflect >= $roll) {
                
            } else {
                $mines_left = $roll - $playerminedeflect;

                if ($playershields >= $mines_left) {
                    db()->q("UPDATE ships set ship_energy = ship_energy - :mines_left where ship_id = :ship_id", [
                        'mines_left' => $mines_left,
                        'ship_id' => $playerinfo['ship_id']
                    ]);
                } else {
                    $mines_left = $mines_left - $playershields;

                    if ($playerarmor >= $mines_left) {
                        db()->q("UPDATE ships set armor_pts = armor_pts - :mines_left, ship_energy = 0 where ship_id = :ship_id", [
                            'mines_left' => $mines_left,
                            'ship_id' => $playerinfo['ship_id']
                        ]);
                    } else {
                        $l_chm_hewasdestroyedbyyourmines = sprintf($l_chm_hewasdestroyedbyyourmines, sprintf("Xenobe %s", $playerinfo['character_name']), $targetlink);
                        message_defence_owner($targetlink, $l_chm_hewasdestroyedbyyourmines);

                        cancel_bounty($playerinfo['ship_id']);
                        db_kill_player($playerinfo['ship_id']);
                        $xenobeisdead = 1;

                        explode_mines($targetlink, $roll);
                        return;
                    }
                }
            }

            explode_mines($targetlink, $roll);
        } else {
            return;
        }
    }
}

function xenobemove()
{
    global $playerinfo;
    global $sector_max;
    global $targetlink;
    global $xenobeisdead;
    global $container;

    if ($targetlink == $playerinfo['sector']) {
        $targetlink = 0;
    }
    $linkres = db()->fetchAll("SELECT * FROM links WHERE link_start = :sector", [
        'sector' => $playerinfo['sector']
    ]);
    if (!empty($linkres)) {
        foreach ($linkres as $row) {
            $sectrow = db()->fetch("SELECT sector_id,zone_id FROM universe WHERE sector_id = :sector_id", [
                'sector_id' => $row['link_dest']
            ]);
            $zonerow = db()->fetch("SELECT zone_id,allow_attack FROM zones WHERE zone_id = :zone_id", [
                'zone_id' => $sectrow['zone_id']
            ]);
            if ($zonerow['allow_attack'] == "Y") {
                $setlink = rand(0, 2);
                if ($setlink == 0 || !$targetlink > 0) {
                    $targetlink = $row['link_dest'];
                }
            }
        }
    }

    if (!$targetlink > 0) {
        $wormto = rand(1, ($sector_max - 15));
        $limitloop = 1;
        while (!$targetlink > 0 && $limitloop < 15) {
            $sectrow = db()->fetch("SELECT sector_id,zone_id FROM universe WHERE sector_id = :sector_id", [
                'sector_id' => $wormto
            ]);
            $zonerow = db()->fetch("SELECT zone_id,allow_attack FROM zones WHERE zone_id = :zone_id", [
                'zone_id' => $sectrow['zone_id']
            ]);
            if ($zonerow['allow_attack'] == "Y") {
                $targetlink = $wormto;
                LogPlayerDAO::call($container, $playerinfo['ship_id'], LogTypeConstants::LOG_RAW, "Used a wormhole to warp to a zone where attacks are allowed.");
            }
            $wormto++;
            $wormto++;
            $limitloop++;
        }
    }

    if ($targetlink > 0) {
        $defences = [];
        $resultf = db()->fetchAll("SELECT * FROM sector_defence WHERE sector_id = :targetlink and defence_type = 'F' ORDER BY quantity DESC", [
            'targetlink' => $targetlink
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
        $resultm = db()->fetchAll("SELECT * FROM sector_defence WHERE sector_id = :targetlink and defence_type = 'M'", [
            'targetlink' => $targetlink
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
            if ($playerinfo['aggression'] == 2 || $playerinfo['aggression'] == 1) {
                xenobetosecdef();
                return;
            } else {
                LogPlayerDAO::call($container, $playerinfo['ship_id'], LogTypeConstants::LOG_RAW, sprintf("Move failed, the sector is defended by %s fighters and %s mines.", $total_sector_fighters, $total_sector_mines));
                return;
            }
        }
    }

    if ($targetlink > 0) {
        $stamp = date("Y-m-d H-i-s");
        $result = db()->q("UPDATE ships SET last_login = :stamp, turns_used = turns_used + 1, sector = :targetlink where ship_id = :ship_id", [
            'stamp' => $stamp,
            'targetlink' => $targetlink,
            'ship_id' => $playerinfo['ship_id']
        ]);
        if (!$result) {
            $error = db()->ErrorMsg();
            LogPlayerDAO::call($container, $playerinfo['ship_id'], LogTypeConstants::LOG_RAW, sprintf("Move failed with error: %s ", $error));
        }
    } else {
        LogPlayerDAO::call($container, $playerinfo['ship_id'], LogTypeConstants::LOG_RAW, "Move failed due to lack of target link.");
        $targetlink = $playerinfo['sector'];
    }
}

function xenoberegen()
{
    global $playerinfo;
    global $xen_unemployment;
    global $xenobeisdead;
    global $container;

    $playerinfo['credits'] = $playerinfo['credits'] + $xen_unemployment;

    $maxenergy = NUM_ENERGY($playerinfo['power']);
    if ($playerinfo['ship_energy'] <= ($maxenergy - 50)) {
        $playerinfo['ship_energy'] = $playerinfo['ship_energy'] + round(($maxenergy - $playerinfo['ship_energy']) / 2);
        $gene = sprintf("regenerated Energy to %s units,", $playerinfo['ship_energy']);
    }

    $maxarmor = NUM_ARMOUR($playerinfo['armor']);
    if ($playerinfo['armor_pts'] <= ($maxarmor - 50)) {
        $playerinfo['armor_pts'] = $playerinfo['armor_pts'] + round(($maxarmor - $playerinfo['armor_pts']) / 2);
        $gena = sprintf("regenerated Armour to %s points,", $playerinfo['armor_pts']);
    }

    $available_fighters = NUM_FIGHTERS($playerinfo['computer']) - $playerinfo['ship_fighters'];
    if (($playerinfo['credits'] > 5) && ($available_fighters > 0)) {
        if (round($playerinfo['credits'] / 6) > $available_fighters) {
            $purchase = ($available_fighters * 6);
            $playerinfo['credits'] = $playerinfo['credits'] - $purchase;
            $playerinfo['ship_fighters'] = $playerinfo['ship_fighters'] + $available_fighters;
            $genf = sprintf("purchased %s fighters for %s credits,", $available_fighters, $purchase);
        }
        if (round($playerinfo['credits'] / 6) <= $available_fighters) {
            $purchase = (round($playerinfo['credits'] / 6));
            $playerinfo['ship_fighters'] = $playerinfo['ship_fighters'] + $purchase;
            $genf = sprintf("purchased %s fighters for %s credits,", $purchase, $playerinfo['credits']);
            $playerinfo['credits'] = 0;
        }
    }

    $available_torpedoes = NUM_TORPEDOES($playerinfo['torp_launchers']) - $playerinfo['torps'];
    if (($playerinfo['credits'] > 2) && ($available_torpedoes > 0)) {
        if (round($playerinfo['credits'] / 3) > $available_torpedoes) {
            $purchase = ($available_torpedoes * 3);
            $playerinfo['credits'] = $playerinfo['credits'] - $purchase;
            $playerinfo['torps'] = $playerinfo['torps'] + $available_torpedoes;
            $gent = sprintf("purchased %s torpedoes for %s credits,", $available_torpedoes, $purchase);
        }
        if (round($playerinfo['credits'] / 3) <= $available_torpedoes) {
            $purchase = (round($playerinfo['credits'] / 3));
            $playerinfo['torps'] = $playerinfo['torps'] + $purchase;
            $gent = sprintf("purchased %s torpedoes for %s credits,", $purchase, $playerinfo['credits']);
            $playerinfo['credits'] = 0;
        }
    }

    db()->q("UPDATE ships SET ship_energy = :ship_energy, armor_pts = :armor_pts, ship_fighters = :ship_fighters, torps = :torps, credits = :credits WHERE ship_id = :ship_id", [
        'ship_energy' => $playerinfo['ship_energy'],
        'armor_pts' => $playerinfo['armor_pts'],
        'ship_fighters' => $playerinfo['ship_fighters'],
        'torps' => $playerinfo['torps'],
        'credits' => $playerinfo['credits'],
        'ship_id' => $playerinfo['ship_id']
    ]);

    if (!empty($gene) || !empty($gena) || !empty($genf) || !empty($gent)) {
        LogPlayerDAO::call($container, $playerinfo['ship_id'], LogTypeConstants::LOG_RAW, sprintf("Xenobe %s %s %s %s and has been updated.", $gene, $gena, $genf, $gent));
    }
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
        $move_result = db()->q("UPDATE ships SET last_login = :stamp, turns_used = turns_used + 1, sector = :sector where ship_id = :ship_id", [
            'stamp' => $stamp,
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