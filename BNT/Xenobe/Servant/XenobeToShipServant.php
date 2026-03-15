<?php

declare(strict_types=1);

namespace BNT\Xenobe\Servant;

class XenobeToShipServant extends \UUA\Servant
{
    public int $ship;

    public function serve(): void
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
        global $torp_dmg_rate;
        global $level_factor;
        
        $ship_id = $this->ship;

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
}
