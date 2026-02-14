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
    global $db;
    global $container;

    $resultt = $db->adoExecute("SELECT * FROM ships WHERE ship_id='$ship_id'");
    $targetinfo = $resultt->fields;

    if (strstr($targetinfo['email'], '@xenobe')) {
        return;
    }

    $sectres = $db->adoExecute("SELECT sector_id,zone_id FROM universe WHERE sector_id='$targetinfo[sector]'");
    $sectrow = $sectres->fields;
    $zoneres = $db->adoExecute("SELECT zone_id,allow_attack FROM zones WHERE zone_id=$sectrow[zone_id]");
    $zonerow = $zoneres->fields;
    if ($zonerow['allow_attack'] == "N") {
        LogPlayerDAO::call($container, $playerinfo['ship_id'], LogTypeConstants::LOG_RAW, "Attack failed, you are in a sector that prohibits attacks.");
        return;
    }

    if ($targetinfo['dev_emerwarp'] > 0) {
        LogPlayerDAO::call($container, $targetinfo['ship_id'], LogTypeConstants::LOG_ATTACK_EWD, "Xenobe $playerinfo[character_name]");
        $dest_sector = rand(0, $sector_max);
        $result_warp = $db->adoExecute("UPDATE ships SET sector=$dest_sector, dev_emerwarp=dev_emerwarp-1 WHERE ship_id=$targetinfo[ship_id]");
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
            $db->adoExecute("UPDATE ships SET hull=0, engines=0, power=0, computer=0,sensors=0, beams=0, torp_launchers=0, torps=0, armor=0, armor_pts=100, cloak=0, shields=0, sector=0, ship_ore=0, ship_organics=0, ship_energy=1000, ship_colonists=0, ship_goods=0, ship_fighters=100, ship_damage=0, on_planet='N', planet_id=0, dev_warpedit=0, dev_genesis=0, dev_beacon=0, dev_emerwarp=0, dev_escapepod='N', dev_fuelscoop='N', dev_minedeflector=0, ship_destroyed='N', rating='$rating',dev_lssd='N' where ship_id=$targetinfo[ship_id]");
            LogPlayerDAO::call($container, $targetinfo['ship_id'], LogTypeConstants::LOG_ATTACK_LOSE, "Xenobe $playerinfo[character_name]|Y");
        } else {
            LogPlayerDAO::call($container, $targetinfo['ship_id'], LogTypeConstants::LOG_ATTACK_LOSE, "Xenobe $playerinfo[character_name]|N");
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
            LogPlayerDAO::call($container, $playerinfo['ship_id'], LogTypeConstants::LOG_RAW, "Attack successful, $targetinfo[character_name] was defeated and salvaged for $ship_salvage credits.");
            $db->adoExecute("UPDATE ships SET ship_ore=ship_ore+$salv_ore, ship_organics=ship_organics+$salv_organics, ship_goods=ship_goods+$salv_goods, credits=credits+$ship_salvage WHERE ship_id=$playerinfo[ship_id]");
            $armor_lost = $playerinfo['armor_pts'] - $attackerarmor;
            $fighters_lost = $playerinfo['ship_fighters'] - $attackerfighters;
            $energy = $playerinfo['ship_energy'];
            $db->adoExecute("UPDATE ships SET ship_energy=$energy,ship_fighters=ship_fighters-$fighters_lost, torps=torps-$attackertorps,armor_pts=armor_pts-$armor_lost, rating=rating-$rating_change WHERE ship_id=$playerinfo[ship_id]");
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
        LogPlayerDAO::call($container, $playerinfo['ship_id'], LogTypeConstants::LOG_RAW, "Attack failed, $targetinfo[character_name] survived.");
        LogPlayerDAO::call($container, $targetinfo['ship_id'], LogTypeConstants::LOG_ATTACK_WIN, "Xenobe $playerinfo[character_name]|$target_armor_lost|$target_fighters_lost");
        $db->adoExecute("UPDATE ships SET ship_energy=$energy,ship_fighters=ship_fighters-$fighters_lost, torps=torps-$attackertorps,armor_pts=armor_pts-$armor_lost, rating=rating-$rating_change WHERE ship_id=$playerinfo[ship_id]");
        $db->adoExecute("UPDATE ships SET ship_energy=$target_energy,ship_fighters=ship_fighters-$target_fighters_lost, armor_pts=armor_pts-$target_armor_lost, torps=torps-$targettorpnum, rating=$target_rating_change WHERE ship_id=$targetinfo[ship_id]");
    }

    if (!$attackerarmor > 0) {
        LogPlayerDAO::call($container, $playerinfo['ship_id'], LogTypeConstants::LOG_RAW, "$targetinfo[character_name] destroyed your ship!");
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
            LogPlayerDAO::call($container, $targetinfo['ship_id'], LogTypeConstants::LOG_ATTACK_WIN, "Xenobe $playerinfo[character_name]|$armor_lost|$fighters_lost");
            LogPlayerDAO::call($container, $targetinfo['ship_id'], LogTypeConstants::LOG_RAW, "You destroyed the Xenobe ship and salvaged $salv_ore units of ore, $salv_organics units of organics, $salv_goods units of goods, and salvaged $ship_salvage_rate% of the ship for $ship_salvage credits.");
            $db->adoExecute("UPDATE ships SET ship_ore=ship_ore+$salv_ore, ship_organics=ship_organics+$salv_organics, ship_goods=ship_goods+$salv_goods, credits=credits+$ship_salvage WHERE ship_id=$targetinfo[ship_id]");
            $armor_lost = $targetinfo['armor_pts'] - $targetarmor;
            $fighters_lost = $targetinfo['ship_fighters'] - $targetfighters;
            $energy = $targetinfo['ship_energy'];
            $db->adoExecute("UPDATE ships SET ship_energy=$energy,ship_fighters=ship_fighters-$fighters_lost, torps=torps-$targettorpnum,armor_pts=armor_pts-$armor_lost, rating=rating-$rating_change WHERE ship_id=$targetinfo[ship_id]");
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
    global $db;
    global $container;

    if ($targetlink > 0) {
        $resultf = $db->adoExecute("SELECT * FROM sector_defence WHERE sector_id='$targetlink' and defence_type ='F' ORDER BY quantity DESC");
        $i = 0;
        $total_sector_fighters = 0;
        if ($resultf > 0) {
            while (!$resultf->EOF) {
                $defences[$i] = $resultf->fields;
                $total_sector_fighters += $defences[$i]['quantity'];
                $i++;
                $resultf->MoveNext();
            }
        }
        $resultm = $db->adoExecute("SELECT * FROM sector_defence WHERE sector_id='$targetlink' and defence_type ='M'");
        $i = 0;
        $total_sector_mines = 0;
        if ($resultm > 0) {
            while (!$resultm->EOF) {
                $defences[$i] = $resultm->fields;
                $total_sector_mines += $defences[$i]['quantity'];
                $i++;
                $resultm->MoveNext();
            }
        }
        if ($total_sector_fighters > 0 || $total_sector_mines > 0 || ($total_sector_fighters > 0 && $total_sector_mines > 0)) {
            LogPlayerDAO::call($container, $playerinfo['ship_id'], LogTypeConstants::LOG_RAW, "ATTACKING SECTOR DEFENCES $total_sector_fighters fighters and $total_sector_mines mines.");

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

            $l_sf_sendlog = str_replace("[player]", "Xenobe $playerinfo[character_name]", $l_sf_sendlog);
            $l_sf_sendlog = str_replace("[lost]", $fighterslost, $l_sf_sendlog);
            $l_sf_sendlog = str_replace("[sector]", $targetlink, $l_sf_sendlog);
            message_defence_owner($targetlink, $l_sf_sendlog);

            $armor_lost = $playerinfo['armor_pts'] - $playerarmor;
            $fighters_lost = $playerinfo['ship_fighters'] - $playerfighters;
            $energy = $playerinfo['ship_energy'];
            $update1 = $db->adoExecute("UPDATE ships SET ship_energy=$energy,ship_fighters=ship_fighters-$fighters_lost, armor_pts=armor_pts-$armor_lost, torps=torps-$playertorpnum WHERE ship_id=$playerinfo[ship_id]");

            if ($playerarmor < 1) {
                $l_sf_sendlog2 = str_replace("[player]", "Xenobe " . $playerinfo['character_name'], $l_sf_sendlog2);
                $l_sf_sendlog2 = str_replace("[sector]", $targetlink, $l_sf_sendlog2);
                message_defence_owner($targetlink, $l_sf_sendlog2);
                cancel_bounty($playerinfo['ship_id']);
                db_kill_player($playerinfo['ship_id']);
                $xenobeisdead = 1;
                return;
            }

            $l_chm_hehitminesinsector = str_replace("[chm_playerinfo_character_name]", "Xenobe " . $playerinfo['character_name'], $l_chm_hehitminesinsector);
            $l_chm_hehitminesinsector = str_replace("[chm_roll]", $roll, $l_chm_hehitminesinsector);
            $l_chm_hehitminesinsector = str_replace("[chm_sector]", $targetlink, $l_chm_hehitminesinsector);
            message_defence_owner($targetlink, "$l_chm_hehitminesinsector");

            if ($playerminedeflect >= $roll) {
                
            } else {
                $mines_left = $roll - $playerminedeflect;

                if ($playershields >= $mines_left) {
                    $update2 = $db->adoExecute("UPDATE ships set ship_energy=ship_energy-$mines_left where ship_id=$playerinfo[ship_id]");
                } else {
                    $mines_left = $mines_left - $playershields;

                    if ($playerarmor >= $mines_left) {
                        $update2 = $db->adoExecute("UPDATE ships set armor_pts=armor_pts-$mines_left,ship_energy=0 where ship_id=$playerinfo[ship_id]");
                    } else {
                        $l_chm_hewasdestroyedbyyourmines = str_replace("[chm_playerinfo_character_name]", "Xenobe " . $playerinfo['character_name'], $l_chm_hewasdestroyedbyyourmines);
                        $l_chm_hewasdestroyedbyyourmines = str_replace("[chm_sector]", $targetlink, $l_chm_hewasdestroyedbyyourmines);
                        message_defence_owner($targetlink, "$l_chm_hewasdestroyedbyyourmines");

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
    global $db;
    global $container;

    if ($targetlink == $playerinfo['sector']) {
        $targetlink = 0;
    }
    $linkres = $db->adoExecute("SELECT * FROM links WHERE link_start='$playerinfo[sector]'");
    if ($linkres > 0) {
        while (!$linkres->EOF) {
            $row = $linkres->fields;
            $sectres = $db->adoExecute("SELECT sector_id,zone_id FROM universe WHERE sector_id='$row[link_dest]'");
            $sectrow = $sectres->fields;
            $zoneres = $db->adoExecute("SELECT zone_id,allow_attack FROM zones WHERE zone_id=$sectrow[zone_id]");
            $zonerow = $zoneres->fields;
            if ($zonerow['allow_attack'] == "Y") {
                $setlink = rand(0, 2);
                if ($setlink == 0 || !$targetlink > 0) {
                    $targetlink = $row['link_dest'];
                }
            }
            $linkres->MoveNext();
        }
    }

    if (!$targetlink > 0) {
        $wormto = rand(1, ($sector_max - 15));
        $limitloop = 1;
        while (!$targetlink > 0 && $limitloop < 15) {
            $sectres = $db->adoExecute("SELECT sector_id,zone_id FROM universe WHERE sector_id='$wormto'");
            $sectrow = $sectres->fields;
            $zoneres = $db->adoExecute("SELECT zone_id,allow_attack FROM zones WHERE zone_id=$sectrow[zone_id]");
            $zonerow = $zoneres->fields;
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
        $resultf = $db->adoExecute("SELECT * FROM sector_defence WHERE sector_id='$targetlink' and defence_type ='F' ORDER BY quantity DESC");
        $i = 0;
        $total_sector_fighters = 0;
        if ($resultf > 0) {
            while (!$resultf->EOF) {
                $defences[$i] = $resultf->fields;
                $total_sector_fighters += $defences[$i]['quantity'];
                $i++;
                $resultf->MoveNext();
            }
        }
        $resultm = $db->adoExecute("SELECT * FROM sector_defence WHERE sector_id='$targetlink' and defence_type ='M'");
        $i = 0;
        $total_sector_mines = 0;
        if ($resultm > 0) {
            while (!$resultm->EOF) {
                $defences[$i] = $resultm->fields;
                $total_sector_mines += $defences[$i]['quantity'];
                $i++;
                $resultm->MoveNext();
            }
        }
        if ($total_sector_fighters > 0 || $total_sector_mines > 0 || ($total_sector_fighters > 0 && $total_sector_mines > 0)) {
            if ($playerinfo['aggression'] == 2 || $playerinfo['aggression'] == 1) {
                xenobetosecdef();
                return;
            } else {
                LogPlayerDAO::call($container, $playerinfo['ship_id'], LogTypeConstants::LOG_RAW, "Move failed, the sector is defended by $total_sector_fighters fighters and $total_sector_mines mines.");
                return;
            }
        }
    }

    if ($targetlink > 0) {
        $stamp = date("Y-m-d H-i-s");
        $query = "UPDATE ships SET last_login='$stamp', turns_used=turns_used+1, sector=$targetlink where ship_id=$playerinfo[ship_id]";
        $move_result = $db->adoExecute("$query");
        if (!$move_result) {
            $error = $db->ErrorMsg();
            LogPlayerDAO::call($container, $playerinfo['ship_id'], LogTypeConstants::LOG_RAW, "Move failed with error: $error ");
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
    global $db;
    global $container;

    $playerinfo['credits'] = $playerinfo['credits'] + $xen_unemployment;

    $maxenergy = NUM_ENERGY($playerinfo['power']);
    if ($playerinfo['ship_energy'] <= ($maxenergy - 50)) {
        $playerinfo['ship_energy'] = $playerinfo['ship_energy'] + round(($maxenergy - $playerinfo['ship_energy']) / 2);
        $gene = "regenerated Energy to $playerinfo[ship_energy] units,";
    }

    $maxarmor = NUM_ARMOUR($playerinfo['armor']);
    if ($playerinfo['armor_pts'] <= ($maxarmor - 50)) {
        $playerinfo['armor_pts'] = $playerinfo['armor_pts'] + round(($maxarmor - $playerinfo['armor_pts']) / 2);
        $gena = "regenerated Armour to $playerinfo[armor_pts] points,";
    }

    $available_fighters = NUM_FIGHTERS($playerinfo['computer']) - $playerinfo['ship_fighters'];
    if (($playerinfo['credits'] > 5) && ($available_fighters > 0)) {
        if (round($playerinfo['credits'] / 6) > $available_fighters) {
            $purchase = ($available_fighters * 6);
            $playerinfo['credits'] = $playerinfo['credits'] - $purchase;
            $playerinfo['ship_fighters'] = $playerinfo['ship_fighters'] + $available_fighters;
            $genf = "purchased $available_fighters fighters for $purchase credits,";
        }
        if (round($playerinfo['credits'] / 6) <= $available_fighters) {
            $purchase = (round($playerinfo['credits'] / 6));
            $playerinfo['ship_fighters'] = $playerinfo['ship_fighters'] + $purchase;
            $genf = "purchased $purchase fighters for $playerinfo[credits] credits,";
            $playerinfo['credits'] = 0;
        }
    }

    $available_torpedoes = NUM_TORPEDOES($playerinfo['torp_launchers']) - $playerinfo['torps'];
    if (($playerinfo['credits'] > 2) && ($available_torpedoes > 0)) {
        if (round($playerinfo['credits'] / 3) > $available_torpedoes) {
            $purchase = ($available_torpedoes * 3);
            $playerinfo['credits'] = $playerinfo['credits'] - $purchase;
            $playerinfo['torps'] = $playerinfo['torps'] + $available_torpedoes;
            $gent = "purchased $available_torpedoes torpedoes for $purchase credits,";
        }
        if (round($playerinfo['credits'] / 3) <= $available_torpedoes) {
            $purchase = (round($playerinfo['credits'] / 3));
            $playerinfo['torps'] = $playerinfo['torps'] + $purchase;
            $gent = "purchased $purchase torpedoes for $playerinfo[credits] credits,";
            $playerinfo['credits'] = 0;
        }
    }

    $db->adoExecute("UPDATE ships SET ship_energy=$playerinfo[ship_energy], armor_pts=$playerinfo[armor_pts], ship_fighters=$playerinfo[ship_fighters], torps=$playerinfo[torps], credits=$playerinfo[credits] WHERE ship_id=$playerinfo[ship_id]");
    if (!$gene == '' || !$gena == '' || !$genf == '' || !$gent == '') {
        LogPlayerDAO::call($container, $playerinfo['ship_id'], LogTypeConstants::LOG_RAW, "Xenobe $gene $gena $genf $gent and has been updated.");
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
    global $db;
    global $container;

    $ore_price = 11;
    $organics_price = 5;
    $goods_price = 15;

    $sectres = $db->adoExecute("SELECT * FROM universe WHERE sector_id='$playerinfo[sector]'");
    $sectorinfo = $sectres->fields;

    $zoneres = $db->adoExecute("SELECT zone_id,allow_attack,allow_trade FROM zones WHERE zone_id='$sectorinfo[zone_id]'");
    $zonerow = $zoneres->fields;

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
        $trade_result = $db->adoExecute("UPDATE ships SET rating=rating+1, credits=$newcredits, ship_ore=$newore, ship_organics=$neworganics, ship_goods=$newgoods where ship_id=$playerinfo[ship_id]");
        $trade_result2 = $db->adoExecute("UPDATE universe SET port_ore=port_ore-$amount_ore, port_organics=port_organics+$amount_organics, port_goods=port_goods+$amount_goods where sector_id=$sectorinfo[sector_id]");
        LogPlayerDAO::call($container, $playerinfo['ship_id'], LogTypeConstants::LOG_RAW, "Xenobe Trade Results: Sold $amount_organics Organics Sold $amount_goods Goods Bought $amount_ore Ore Cost $total_cost");
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
        $trade_result = $db->adoExecute("UPDATE ships SET rating=rating+1, credits=$newcredits, ship_ore=$newore, ship_organics=$neworganics, ship_goods=$newgoods where ship_id=$playerinfo[ship_id]");
        $trade_result2 = $db->adoExecute("UPDATE universe SET port_ore=port_ore+$amount_ore, port_organics=port_organics-$amount_organics, port_goods=port_goods+$amount_goods where sector_id=$sectorinfo[sector_id]");
        LogPlayerDAO::call($container, $playerinfo['ship_id'], LogTypeConstants::LOG_RAW, "Xenobe Trade Results: Sold $amount_goods Goods Sold $amount_ore Ore Bought $amount_organics Organics Cost $total_cost");
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
        $trade_result = $db->adoExecute("UPDATE ships SET rating=rating+1, credits=$newcredits, ship_ore=$newore, ship_organics=$neworganics, ship_goods=$newgoods where ship_id=$playerinfo[ship_id]");
        $trade_result2 = $db->adoExecute("UPDATE universe SET port_ore=port_ore+$amount_ore, port_organics=port_organics+$amount_organics, port_goods=port_goods-$amount_goods where sector_id=$sectorinfo[sector_id]");
        LogPlayerDAO::call($container, $playerinfo['ship_id'], LogTypeConstants::LOG_RAW, "Xenobe Trade Results: Sold $amount_ore Ore Sold $amount_organics Organics Bought $amount_goods Goods Cost $total_cost");
    }
}

function xenobehunter()
{
    global $playerinfo;
    global $targetlink;
    global $xenobeisdead;
    global $db;
    global $container;

    $rescount = $db->adoExecute("SELECT COUNT(*) AS num_players FROM ships WHERE ship_destroyed='N' and email NOT LIKE '%@xenobe' and ship_id > 1");
    $rowcount = $rescount->fields;
    $topnum = min(10, $rowcount['num_players']);

    if ($topnum < 1) {
        return;
    }

    $res = $db->adoExecute("SELECT * FROM ships WHERE ship_destroyed='N' and email NOT LIKE '%@xenobe' and ship_id > 1 ORDER BY score DESC LIMIT $topnum");

    $i = 1;
    $targetnum = rand(1, $topnum);
    while (!$res->EOF) {
        if ($i == $targetnum) {
            $targetinfo = $res->fields;
        }
        $i++;
        $res->MoveNext();
    }

    if (!$targetinfo) {
        LogPlayerDAO::call($container, $playerinfo['ship_id'], LogTypeConstants::LOG_RAW, "Hunt Failed: No Target ");
        return;
    }

    $sectres = $db->adoExecute("SELECT sector_id,zone_id FROM universe WHERE sector_id='$targetinfo[sector]'");
    $sectrow = $sectres->fields;
    $zoneres = $db->adoExecute("SELECT zone_id,allow_attack FROM zones WHERE zone_id=$sectrow[zone_id]");
    $zonerow = $zoneres->fields;
    if ($zonerow['allow_attack'] == "Y") {
        $stamp = date("Y-m-d H-i-s");
        $query = "UPDATE ships SET last_login='$stamp', turns_used=turns_used+1, sector=$targetinfo[sector] where ship_id=$playerinfo[ship_id]";
        $move_result = $db->adoExecute("$query");
        LogPlayerDAO::call($container, $playerinfo['ship_id'], LogTypeConstants::LOG_RAW, "Xenobe used a wormhole to warp to sector $targetinfo[sector] where he is hunting player $targetinfo[character_name].");
        if (!$move_result) {
            $error = $db->ErrorMsg();
            LogPlayerDAO::call($container, $playerinfo['ship_id'], LogTypeConstants::LOG_RAW, "Move failed with error: $error ");
            return;
        }

        $resultf = $db->adoExecute("SELECT * FROM sector_defence WHERE sector_id=$targetinfo[sector] and defence_type ='F' ORDER BY quantity DESC");
        $i = 0;
        $total_sector_fighters = 0;
        if ($resultf > 0) {
            while (!$resultf->EOF) {
                $defences[$i] = $resultf->fields;
                $total_sector_fighters += $defences[$i]['quantity'];
                $i++;
                $resultf->MoveNext();
            }
        }
        $resultm = $db->adoExecute("SELECT * FROM sector_defence WHERE sector_id=$targetinfo[sector] and defence_type ='M'");
        $i = 0;
        $total_sector_mines = 0;
        if ($resultm > 0) {
            while (!$resultm->EOF) {
                $defences[$i] = $resultm->fields;
                $total_sector_mines += $defences[$i]['quantity'];
                $i++;
                $resultm->MoveNext();
            }
        }

        if ($total_sector_fighters > 0 || $total_sector_mines > 0 || ($total_sector_fighters > 0 && $total_sector_mines > 0)) {
            $targetlink = $targetinfo['sector'];
            xenobetosecdef();
        }
        if ($xenobeisdead > 0) {
            return;
        }

        LogPlayerDAO::call($container, $playerinfo['ship_id'], LogTypeConstants::LOG_RAW, "Xenobe launching an attack on $targetinfo[character_name].");

        if ($targetinfo['planet_id'] > 0) {
            xenobetoplanet($targetinfo['planet_id']);
        } else {
            xenobetoship($targetinfo['ship_id']);
        }
    } else {
        LogPlayerDAO::call($container, $playerinfo['ship_id'], LogTypeConstants::LOG_RAW, "Xenobe hunt failed, target $targetinfo[character_name] was in a no attack zone (sector $targetinfo[sector]).");
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
    global $db;
    global $container;

    $resultp = $db->adoExecute("SELECT * FROM planets WHERE planet_id='$planet_id'");
    $planetinfo = $resultp->fields;

    $resulto = $db->adoExecute("SELECT * FROM ships WHERE ship_id='$planetinfo[owner]'");
    $ownerinfo = $resulto->fields;

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
        LogPlayerDAO::call($container, $playerinfo['ship_id'], LogTypeConstants::LOG_RAW, "Ship destroyed by planetary defenses on planet $planetinfo[name]");
        db_kill_player($playerinfo['ship_id']);
        $xenobeisdead = 1;

        $free_ore = round($playerinfo['ship_ore'] / 2);
        $free_organics = round($playerinfo['ship_organics'] / 2);
        $free_goods = round($playerinfo['ship_goods'] / 2);
        $ship_value = $upgrade_cost * (round(mypw($upgrade_factor, $playerinfo['hull'])) + round(mypw($upgrade_factor, $playerinfo['engines'])) + round(mypw($upgrade_factor, $playerinfo['power'])) + round(mypw($upgrade_factor, $playerinfo['computer'])) + round(mypw($upgrade_factor, $playerinfo['sensors'])) + round(mypw($upgrade_factor, $playerinfo['beams'])) + round(mypw($upgrade_factor, $playerinfo['torp_launchers'])) + round(mypw($upgrade_factor, $playerinfo['shields'])) + round(mypw($upgrade_factor, $playerinfo['armor'])) + round(mypw($upgrade_factor, $playerinfo['cloak'])));
        $ship_salvage_rate = rand(10, 20);
        $ship_salvage = $ship_value * $ship_salvage_rate / 100;
        $fighters_lost = $planetinfo['fighters'] - $targetfighters;

        LogPlayerDAO::call($container, $planetinfo['owner'], LogTypeConstants::LOG_PLANET_NOT_DEFEATED, "$planetinfo[name]|$playerinfo[sector]|Xenobe $playerinfo[character_name]|$free_ore|$free_organics|$free_goods|$ship_salvage_rate|$ship_salvage");

        $db->adoExecute("UPDATE planets SET energy=$planetinfo[energy],fighters=fighters-$fighters_lost, torps=torps-$targettorps, ore=ore+$free_ore, goods=goods+$free_goods, organics=organics+$free_organics, credits=credits+$ship_salvage WHERE planet_id=$planetinfo[planet_id]");
    } else {
        $armor_lost = $playerinfo['armor_pts'] - $attackerarmor;
        $fighters_lost = $playerinfo['ship_fighters'] - $attackerfighters;
        $target_fighters_lost = $planetinfo['ship_fighters'] - $targetfighters;
        LogPlayerDAO::call($container, $playerinfo['ship_id'], LogTypeConstants::LOG_RAW, "Made it past defenses on planet $planetinfo[name]");

        $db->adoExecute("UPDATE ships SET ship_energy=$playerinfo[ship_energy], ship_fighters=ship_fighters-$fighters_lost, torps=torps-$attackertorps, armor_pts=armor_pts-$armor_lost WHERE ship_id=$playerinfo[ship_id]");
        $playerinfo['ship_fighters'] = $attackerfighters;
        $playerinfo['torps'] = $attackertorps;
        $playerinfo['armor_pts'] = $attackerarmor;

        $db->adoExecute("UPDATE planets SET energy=$planetinfo[energy], fighters=$targetfighters, torps=torps-$targettorps WHERE planet_id=$planetinfo[planet_id]");
        $planetinfo['fighters'] = $targetfighters;
        $planetinfo['torps'] = $targettorps;

        $resultps = $db->adoExecute("SELECT ship_id,ship_name FROM ships WHERE planet_id=$planetinfo[planet_id] AND on_planet='Y'");
        $shipsonplanet = $resultps->RecordCount();
        if ($shipsonplanet > 0) {
            while (!$resultps->EOF && $xenobeisdead < 1) {
                $onplanet = $resultps->fields;
                xenobetoship($onplanet['ship_id']);
                $resultps->MoveNext();
            }
        }
        $resultps = $db->adoExecute("SELECT ship_id,ship_name FROM ships WHERE planet_id=$planetinfo[planet_id] AND on_planet='Y'");
        $shipsonplanet = $resultps->RecordCount();
        if ($shipsonplanet == 0 && $xenobeisdead < 1) {
            LogPlayerDAO::call($container, $playerinfo['ship_id'], LogTypeConstants::LOG_RAW, "Defeated all ships on planet $planetinfo[name]");
            LogPlayerDAO::call($container, $planetinfo['owner'], LogTypeConstants::LOG_PLANET_DEFEATED, "$planetinfo[name]|$playerinfo[sector]|$playerinfo[character_name]");

            $db->adoExecute("UPDATE planets SET fighters=0, torps=0, base='N', owner=0, corp=0 WHERE planet_id=$planetinfo[planet_id]");
            calc_ownership($planetinfo['sector_id']);
        } else {
            LogPlayerDAO::call($container, $playerinfo['ship_id'], LogTypeConstants::LOG_RAW, "We were KILLED by ships defending planet $planetinfo[name]");
            LogPlayerDAO::call($container, $planetinfo['owner'], LogTypeConstants::LOG_PLANET_NOT_DEFEATED, "$planetinfo[name]|$playerinfo[sector]|Xenobe $playerinfo[character_name]|0|0|0|0|0");
        }
    }
}
