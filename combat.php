<?php

//$Id$

function calcplanetbeams()
{
    global $playerinfo;
    global $ownerinfo;
    global $sectorinfo;
    global $basedefense;
    global $planetinfo;

    $energy_available = $planetinfo['energy'];
    $base_factor = ($planetinfo['base'] == 'Y') ? $basedefense : 0;
    $planetbeams = NUM_BEAMS($ownerinfo['beams'] + $base_factor);
    
    $ships = db()->fetchAll("SELECT * FROM ships WHERE planet_id= :planet_id AND on_planet='Y'", [
        'planet_id' => $planetinfo['planet_id']
    ]);
    
    foreach ($ships as $ship) {
        $planetbeams = $planetbeams + NUM_BEAMS($ship['beams']);
    }

    if ($planetbeams > $energy_available) {
        $planetbeams = $energy_available;
    }

    return $planetbeams;
}

function calcplanetfighters()
{
    global $planetinfo;

    $planetfighters = $planetinfo['fighters'];
    return $planetfighters;
}


function calcplanettorps()
{
    global $playerinfo;
    global $ownerinfo;
    global $sectorinfo;
    global $level_factor;
    global $basedefense;
    global $planetinfo;

    $base_factor = ($planetinfo['base'] == 'Y') ? $basedefense : 0;

    $ships = db()->fetchAll("SELECT * FROM ships WHERE planet_id= :planet_id AND on_planet='Y'", [
        'planet_id' => $planetinfo['planet_id']
    ]);
    
    $torp_launchers = round(mypw($level_factor, ($ownerinfo['torp_launchers']) + $base_factor)) * 10;
    $torps = $planetinfo['torps'];
    
    foreach ($ships as $ship) {
        $ship_torps =  round(mypw($level_factor, $ship['torp_launchers'])) * 10;
        $torp_launchers = $torp_launchers + $ship_torps;
    }
    
    if ($torp_launchers > $torps) {
        $planettorps = $torps;
    } else {
        $planettorps = $torp_launchers;
    }
    $planetinfo['torps'] -= $planettorps;

    return $planettorps;
}

function calcplanetshields()
{
    global $playerinfo;
    global $ownerinfo;
    global $sectorinfo;
    global $basedefense;
    global $planetinfo;


    $base_factor = ($planetinfo['base'] == 'Y') ? $basedefense : 0;
    
    $ships = db()->fetchAll("SELECT * FROM ships WHERE planet_id= :planet_id AND on_planet='Y'", [
        'planet_id' => $planetinfo['planet_id']
    ]);
    
    $planetshields = NUM_SHIELDS($ownerinfo['shields'] + $base_factor);
    $energy_available = $planetinfo['energy'];
    
    foreach ($ships as $ship) {
        $planetshields += NUM_SHIELDS($ship['shields']);
    }

    if ($planetshields > $energy_available) {
        $planetshields = $energy_available;
    }
    $planetinfo['energy'] -= $planetshields;
    return $planetshields;
}


function planetbombing()
{
    global $playerinfo;
    global $ownerinfo;
    global $sectorinfo;
    global $planetinfo;
    global $planetbeams;
    global $planetfighters;
    global $attackerfighters;
    global $planettorps;
    global $torp_dmg_rate;
    global $l_cmb_atleastoneturn;
    global $l_bombsaway;
    global $l_bigfigs;
    global $l_bigbeams;
    global $l_bigtorps;
    global $l_strafesuccess;

    if ($playerinfo['turns'] < 1) {
        echo "$l_cmb_atleastoneturn<BR><BR>";
        
        include("footer.php");
        die();
    }

    echo "$l_bombsaway<br><br>\n";

    $attackerfighterslost = 0;
    $planetfighterslost = 0;
    $attackerfightercapacity = NUM_FIGHTERS($playerinfo['computer']);
    $ownerfightercapacity = NUM_FIGHTERS($ownerinfo['computer']);
    $beamsused = 0;
    $planettorps = calcplanettorps();
    $planetbeams = calcplanetbeams();
    $planetfighters = calcplanetfighters();
    $attackerfighters = $playerinfo['ship_fighters'];
    
    if ($ownerfightercapacity / $attackerfightercapacity < 1) {
        echo "$l_bigfigs<br><br>\n";
    }

    if ($planetbeams <= $attackerfighters) {
        $attackerfighterslost = $planetbeams;
        $beamsused = $planetbeams;
    } else {
        $attackerfighterslost = $attackerfighters;
        $beamsused = $attackerfighters;
    }

    if ($attackerfighters <= $attackerfighterslost) {
        echo "$l_bigbeams<br>\n";
    } else {
        $attackerfighterslost += $planettorps * $torp_dmg_rate;

        if ($attackerfighters <= $attackerfighterslost) {
            echo "$l_bigtorps<br>\n";
        } else {
            echo "$l_strafesuccess<br>\n";
            if ($ownerfightercapacity / $attackerfightercapacity > 1) {
                $planetfighterslost = $attackerfighters - $attackerfighterslost;
            } else {
                $planetfighterslost = round(($attackerfighters - $attackerfighterslost) * $ownerfightercapacity / $attackerfightercapacity);
                if ($planetfighterslost > $planetfighters) {
                    $planetfighterslost = $planetfighters;
                }
            }
        }
    }

    playerlog($ownerinfo['ship_id'], \BNT\Log\LogTypeConstants::LOG_PLANET_BOMBED, sprintf("%s|%s|%s|%s|%s|%s",
        $planetinfo['name'],
        $playerinfo['sector'],
        $playerinfo['character_name'],
        $beamsused,
        $planettorps,
        $planetfighterslost
    ));

    db()->q("UPDATE ships SET turns=turns-1, turns_used=turns_used+1, ship_fighters=ship_fighters- :attackerfighters WHERE ship_id= :ship_id", [
        'attackerfighters' => $attackerfighters,
        'ship_id' => $playerinfo['ship_id']
    ]);
    
    db()->q("UPDATE planets SET energy=energy- :beamsused, fighters=fighters- :planetfighterslost, torps=torps- :planettorps WHERE planet_id= :planet_id", [
        'beamsused' => $beamsused,
        'planetfighterslost' => $planetfighterslost,
        'planettorps' => $planettorps,
        'planet_id' => $planetinfo['planet_id']
    ]);
}

function planetcombat()
{
    global $playerinfo;
    global $ownerinfo;
    global $sectorinfo;
    global $planetinfo;
    global $torpedo_price;
    global $colonist_price;
    global $ore_price;
    global $organics_price;
    global $goods_price;
    global $energy_price;

    global $planetbeams;
    global $planetfighters;
    global $planetshields;
    global $planettorps;
    global $attackerbeams;
    global $attackerfighters;
    global $attackershields;
    global $attackertorps;
    global $attackerarmor;
    global $attackertorpdamage;
    global $level_factor;
    global $start_energy;
    global $min_value_capture;
    global $l_cmb_atleastoneturn;
    global $l_cmb_shipenergybb, $l_cmb_shipenergyab, $l_cmb_shipenergyas, $l_cmb_shiptorpsbtl, $l_cmb_shiptorpsatl;
    global $l_cmb_planettorpdamage, $l_cmb_attackertorpdamage, $l_cmb_beams, $l_cmb_fighters, $l_cmb_shields, $l_cmb_torps;
    global $l_cmb_torpdamage, $l_cmb_armor, $l_cmb_you, $l_cmb_planet, $l_cmb_combatflow, $l_cmb_defender, $l_cmb_attackingplanet;
    global $l_cmb_youfireyourbeams, $l_cmb_defenselost, $l_cmb_defenselost2, $l_cmb_planetarybeams, $l_cmb_planetarybeams2;
    global $l_cmb_youdestroyedplanetshields, $l_cmb_beamsexhausted, $l_cmb_breachedyourshields, $l_cmb_destroyedyourshields;
    global $l_cmb_breachedyourarmor, $l_cmb_destroyedyourarmor, $l_cmb_torpedoexchangephase, $l_cmb_nofightersleft;
    global $l_cmb_youdestroyfighters, $l_cmb_planettorpsdestroy, $l_cmb_planettorpsdestroy2, $l_cmb_torpsbreachedyourarmor;
    global $l_cmb_planettorpsdestroy3, $l_cmb_youdestroyedallfighters, $l_cmb_youdestroyplanetfighters, $l_cmb_fightercombatphase;
    global $l_cmb_youdestroyedallfighters2, $l_cmb_youdestroyplanetfighters2, $l_cmb_allyourfightersdestroyed, $l_cmb_fightertofighterlost;
    global $l_cmb_youbreachedplanetshields, $l_cmb_shieldsremainup, $l_cmb_fighterswarm, $l_cmb_swarmandrepel, $l_cmb_engshiptoshipcombat;
    global $l_cmb_shipdock, $l_cmb_approachattackvector, $l_cmb_noshipsdocked, $l_cmb_yourshipdestroyed, $l_cmb_escapepod;
    global $l_cmb_finalcombatstats, $l_cmb_youlostfighters, $l_cmb_youlostarmorpoints, $l_cmb_energyused, $l_cmb_planetdefeated;
    global $l_cmb_citizenswanttodie, $l_cmb_youmaycapture, $l_cmb_youmaycapture2, $l_cmb_planetnotdefeated, $l_cmb_planetstatistics;
    global $l_cmb_fighterloststat, $l_cmb_energyleft;
    global $upgrade_cost, $upgrade_factor, $fighter_price, $rating_combat_factor;

    if ($playerinfo['turns'] < 1) {
        echo "$l_cmb_atleastoneturn<BR><BR>";
        
        include("footer.php");
        die();
    }

    // Planetary defense system calculation
    $planetbeams        = calcplanetbeams();
    $planetfighters     = calcplanetfighters();
    $planetshields      = calcplanetshields();
    $planettorps        = calcplanettorps();

    // Attacking ship calculations
    $attackerbeams      = NUM_BEAMS($playerinfo['beams']);
    $attackerfighters   = $playerinfo['ship_fighters'];
    $attackershields    = NUM_SHIELDS($playerinfo['shields']);
    $attackertorps      = round(mypw($level_factor, $playerinfo['torp_launchers'])) * 2;
    $attackerarmor      = $playerinfo['armor_pts'];

    // Now modify player beams, shields and torpedos on available materiel
    $start_energy = $playerinfo['ship_energy'];

    // Beams
    if ($attackerbeams > $playerinfo['ship_energy']) {
        $attackerbeams   = $playerinfo['ship_energy'];
    }
    $playerinfo['ship_energy'] = $playerinfo['ship_energy'] - $attackerbeams;

    // Shields
    if ($attackershields > $playerinfo['ship_energy']) {
        $attackershields = $playerinfo['ship_energy'];
    }
    $playerinfo['ship_energy'] = $playerinfo['ship_energy'] - $attackershields;

    // Torpedos
    if ($attackertorps > $playerinfo['torps']) {
        $attackertorps = $playerinfo['torps'];
    }
    $playerinfo['torps'] = $playerinfo['torps'] - $attackertorps;

    // Setup torp damage rate for both Planet and Ship
    $planettorpdamage   = $torp_dmg_rate * $planettorps;
    $attackertorpdamage = $torp_dmg_rate * $attackertorps;

    echo "
    <div class='combat-stats'>
    <hr>
    <table class='table'>
    <tr>
    <th></th>
    <th>$l_cmb_beams</th>
    <th>$l_cmb_fighters</th>
    <th>$l_cmb_shields</th>
    <th>$l_cmb_torps</th>
    <th>$l_cmb_torpdamage</th>
    <th>$l_cmb_armor</th>
    </tr>
    <tr>
    <th>$l_cmb_you</th>
    <td><strong>$attackerbeams</strong></td>
    <td><strong>$attackerfighters</strong></td>
    <td><strong>$attackershields</strong></td>
    <td><strong>$attackertorps</strong></td>
    <td><strong>$attackertorpdamage</strong></td>
    <td><strong>$attackerarmor</strong></td>
    </tr>
    <tr>
    <th>$l_cmb_planet</th>
    <td><strong>$planetbeams</strong></td>
    <td><strong>$planetfighters</strong></td>
    <td><strong>$planetshields</strong></td>
    <td><strong>$planettorps</strong></td>
    <td><strong>$planettorpdamage</strong></td>
    <td><strong>N/A</strong></td>
    </tr>
    </table>
    <hr>
    </div>
    ";

    // Begin actual combat calculations
    $planetdestroyed   = 0;
    $attackerdestroyed = 0;

    echo "<div class='combat-flow'><h3>$l_cmb_combatflow</h3>\n";
    echo "<table class='table'><tr><th>$l_cmb_you</th><th>$l_cmb_defender</th></tr>\n";
    echo "<tr><td><strong>$l_cmb_attackingplanet $playerinfo[sector]</strong></td><td></td></tr>";
    echo "<tr><td><strong>$l_cmb_youfireyourbeams</strong></td><td></td></tr>\n";
    
    if ($planetfighters > 0 && $attackerbeams > 0) {
        if ($attackerbeams > $planetfighters) {
            $l_cmb_defenselost = str_replace("[cmb_planetfighters]", $planetfighters, $l_cmb_defenselost);
            echo "<tr><td></td><td><strong>$l_cmb_defenselost</strong></td></tr>";
            $planetfighters = 0;
            $attackerbeams = $attackerbeams - $planetfighters;
        } else {
            $l_cmb_defenselost2 = str_replace("[cmb_attackerbeams]", $attackerbeams, $l_cmb_defenselost2);
            $planetfighters = $planetfighters - $attackerbeams;
            echo "<tr><td></td><td><strong>$l_cmb_defenselost2</strong></td></tr>";
            $attackerbeams = 0;
        }
    }

    if ($attackerfighters > 0 && $planetbeams > 0) {
        if ($planetbeams > round($attackerfighters / 2)) {
            $temp = round($attackerfighters / 2);
            $lost = $attackerfighters - $temp;
            $attackerfighters = $temp;
            $planetbeams = $planetbeams - $lost;
            $l_cmb_planetarybeams = str_replace("[cmb_temp]", $temp, $l_cmb_planetarybeams);
            echo "<tr><td><strong>$l_cmb_planetarybeams</strong></td><td></td></tr>";
        } else {
            $l_cmb_planetarybeams2 = str_replace("[cmb_planetbeams]", $planetbeams, $l_cmb_planetarybeams2);
            $attackerfighters = $attackerfighters - $planetbeams;
            echo "<tr><td><strong>$l_cmb_planetarybeams2</strong></td><td></td></tr>";
            $planetbeams = 0;
        }
    }
    
    if ($attackerbeams > 0) {
        if ($attackerbeams > $planetshields) {
            $attackerbeams = $attackerbeams - $planetshields;
            $planetshields = 0;
            echo "<tr><td><strong>$l_cmb_youdestroyedplanetshields</strong></td><td></td></tr>";
        } else {
            $l_cmb_beamsexhausted = str_replace("[cmb_attackerbeams]", $attackerbeams, $l_cmb_beamsexhausted);
            echo "<tr><td><strong>$l_cmb_beamsexhausted</strong></td><td></td></tr>";
            $planetshields = $planetshields - $attackerbeams;
            $attackerbeams = 0;
        }
    }
    
    if ($planetbeams > 0) {
        if ($planetbeams > $attackershields) {
            $planetbeams = $planetbeams - $attackershields;
            $attackershields = 0;
            echo "<tr><td></td><td><strong>$l_cmb_breachedyourshields</strong></td></tr>";
        } else {
            $attackershields = $attackershields - $planetbeams;
            $l_cmb_destroyedyourshields = str_replace("[cmb_planetbeams]", $planetbeams, $l_cmb_destroyedyourshields);
            echo "<tr><td></td><td><strong>$l_cmb_destroyedyourshields</strong></td></tr>";
            $planetbeams = 0;
        }
    }
    
    if ($planetbeams > 0) {
        if ($planetbeams > $attackerarmor) {
            $attackerarmor = 0;
            echo "<tr><td></td><td><strong>$l_cmb_breachedyourarmor</strong></td></tr>";
        } else {
            $attackerarmor = $attackerarmor - $planetbeams;
            $l_cmb_destroyedyourarmor = str_replace("[cmb_planetbeams]", $planetbeams, $l_cmb_destroyedyourarmor);
            echo "<tr><td></td><td><strong>$l_destroyedyourarmor</strong></td></tr>";
        }
    }
    
    echo "<tr><td colspan='2'><strong>$l_cmb_torpedoexchangephase</strong></td></tr>";
    
    if ($planetfighters > 0 && $attackertorpdamage > 0) {
        if ($attackertorpdamage > $planetfighters) {
            $l_cmb_nofightersleft = str_replace("[cmb_planetfighters]", $planetfighters, $l_cmb_nofightersleft);
            echo "<tr><td><strong>$l_cmb_nofightersleft</strong></td><td></td></tr>";
            $planetfighters = 0;
            $attackertorpdamage = $attackertorpdamage - $planetfighters;
        } else {
            $planetfighters = $planetfighters - $attackertorpdamage;
            $l_cmb_youdestroyfighters = str_replace("[cmb_attackertorpdamage]", $attackertorpdamage, $l_cmb_youdestroyfighters);
            echo "<tr><td><strong>$l_cmb_youdestroyfighters</strong></td><td></td></tr>";
            $attackertorpdamage = 0;
        }
    }
    
    if ($attackerfighters > 0 && $planettorpdamage > 0) {
        if ($planettorpdamage > round($attackerfighters / 2)) {
            $temp = round($attackerfighters / 2);
            $lost = $attackerfighters - $temp;
            $attackerfighters = $temp;
            $planettorpdamage = $planettorpdamage - $lost;
            $l_cmb_planettorpsdestroy = str_replace("[cmb_temp]", $temp, $l_cmb_planettorpsdestroy);
            echo "<tr><td></td><td><strong>$l_cmb_planettorpsdestroy</strong></td></tr>";
        } else {
            $attackerfighters = $attackerfighters - $planettorpdamage;
            $l_cmb_planettorpsdestroy2 = str_replace("[cmb_planettorpdamage]", $planettorpdamage, $l_cmb_planettorpsdestroy2);
            echo "<tr><td></td><td><strong>$l_cmb_planettorpsdestroy2</strong></td></tr>";
            $planettorpdamage = 0;
        }
    }
    
    if ($planettorpdamage > 0) {
        if ($planettorpdamage > $attackerarmor) {
            $attackerarmor = 0;
            echo "<tr><td><strong>$l_cmb_torpsbreachedyourarmor</strong></td><td></td></tr>";
        } else {
            $attackerarmor = $attackerarmor - $planettorpdamage;
            $l_cmb_planettorpsdestroy3 = str_replace("[cmb_planettorpdamage]", $planettorpdamage, $l_cmb_planettorpsdestroy3);
            echo "<tr><td><strong>$l_cmb_planettorpsdestroy3</strong></td><td></td></tr>";
        }
    }
    
    if ($attackertorpdamage > 0 && $planetfighters > 0) {
        $planetfighters = $planetfighters - $attackertorpdamage;
        if ($planetfighters < 0) {
            $planetfighters = 0;
            echo "<tr><td><strong>$l_cmb_youdestroyedallfighters</strong></td><td></td></tr>";
        } else {
            $l_cmb_youdestroyplanetfighters = str_replace("[cmb_attackertorpdamage]", $attackertorpdamage, $l_cmb_youdestroyplanetfighters);
            echo "<tr><td><strong>$l_cmb_youdestroyplanetfighters</strong></td><td></td></tr>";
        }
    }
    
    echo "<tr><td colspan='2'><strong>$l_cmb_fightercombatphase</strong></td></tr>";
    
    if ($attackerfighters > 0 && $planetfighters > 0) {
        if ($attackerfighters > $planetfighters) {
            echo "<tr><td><strong>$l_cmb_youdestroyedallfighters2</strong></td><td></td></tr>";
            $tempplanetfighters = 0;
        } else {
            $l_cmb_youdestroyplanetfighters2 = str_replace("[cmb_attackerfighters]", $attackerfighters, $l_cmb_youdestroyplanetfighters2);
            echo "<tr><td><strong>$l_cmb_youdestroyplanetfighters2</strong></td><td></td></tr>";
            $tempplanetfighters = $planetfighters - $attackerfighters;
        }
        if ($planetfighters > $attackerfighters) {
            echo "<tr><td><strong>$l_cmb_allyourfightersdestroyed</strong></td><td></td></tr>";
            $tempplayfighters = 0;
        } else {
            $tempplayfighters = $attackerfighters - $planetfighters;
            $l_cmb_fightertofighterlost = str_replace("[cmb_planetfighters]", $planetfighters, $l_cmb_fightertofighterlost);
            echo "<tr><td><strong>$l_cmb_fightertofighterlost</strong></td><td></td></tr>";
        }
        $attackerfighters = $tempplayfighters;
        $planetfighters = $tempplanetfighters;
    }
    
    if ($attackerfighters > 0 && $planetshields > 0) {
        if ($attackerfighters > $planetshields) {
            $attackerfighters = $attackerfighters - round($planetshields / 2);
            echo "<tr><td><strong>$l_cmb_youbreachedplanetshields</strong></td><td></td></tr>";
            $planetshields = 0;
        } else {
            $l_cmb_shieldsremainup = str_replace("[cmb_attackerfighters]", $attackerfighters, $l_cmb_shieldsremainup);
            echo "<tr><td></td><td><strong>$l_cmb_shieldsremainup</strong></td></tr>";
            $planetshields = $planetshields - $attackerfighters;
        }
    }
    
    if ($planetfighters > 0) {
        if ($planetfighters > $attackerarmor) {
            $attackerarmor = 0;
            echo "<tr><td><strong>$l_cmb_fighterswarm</strong></td><td></td></tr>";
        } else {
            $attackerarmor = $attackerarmor - $planetfighters;
            echo "<tr><td><strong>$l_cmb_swarmandrepel</strong></td><td></td></tr>";
        }
    }

    echo "</table></div>\n";
    
    // Send each docked ship in sequence to attack agressor
    $shipsOnPlanet = db()->fetchAll("SELECT * FROM ships WHERE planet_id= :planet_id AND on_planet='Y'", [
        'planet_id' => $planetinfo['planet_id']
    ]);
    
    $shipsonplanet = count($shipsOnPlanet);

    if ($shipsonplanet > 0) {
        $l_cmb_shipdock = str_replace("[cmb_shipsonplanet]", $shipsonplanet, $l_cmb_shipdock);
        echo "<div class='text-center'>$l_cmb_shipdock<br>$l_cmb_engshiptoshipcombat</div><br><br>\n";
        foreach ($shipsOnPlanet as $onplanet) {
            if ($attackerfighters < 0) {
                $attackerfighters = 0;
            }
            if ($attackertorps    < 0) {
                $attackertorps = 0;
            }
            if ($attackershields  < 0) {
                $attackershields = 0;
            }
            if ($attackerbeams    < 0) {
                $attackerbeams = 0;
            }
            if ($attackerarmor    < 1) {
                break;
            }

            echo "<br>- {$onplanet['ship_name']} $l_cmb_approachattackvector -<br>";
            shiptoship($onplanet['ship_id']);
        }
    } else {
        echo "<div class='text-center'>$l_cmb_noshipsdocked</div><br><br>\n";
    }

    if ($attackerarmor < 1) {
        $free_ore = round($playerinfo['ship_ore'] / 2);
        $free_organics = round($playerinfo['ship_organics'] / 2);
        $free_goods = round($playerinfo['ship_goods'] / 2);
        $ship_value = $upgrade_cost * (round(mypw($upgrade_factor, $playerinfo['hull'])) + round(mypw($upgrade_factor, $playerinfo['engines'])) + round(mypw($upgrade_factor, $playerinfo['power'])) + round(mypw($upgrade_factor, $playerinfo['computer'])) + round(mypw($upgrade_factor, $playerinfo['sensors'])) + round(mypw($upgrade_factor, $playerinfo['beams'])) + round(mypw($upgrade_factor, $playerinfo['torp_launchers'])) + round(mypw($upgrade_factor, $playerinfo['shields'])) + round(mypw($upgrade_factor, $playerinfo['armor'])) + round(mypw($upgrade_factor, $playerinfo['cloak'])));
        $ship_salvage_rate = rand(0, 10);
        $ship_salvage = $ship_value * $ship_salvage_rate / 100;
        echo "<div class='text-center'><h3>$l_cmb_yourshipdestroyed</h3></div><br>";
        if ($playerinfo['dev_escapepod'] == "Y") {
            echo "<div class='text-center'>$l_cmb_escapepod</div><br><br>";
            db()->q("UPDATE ships SET hull=0,engines=0,power=0,sensors=0,computer=0,beams=0,torp_launchers=0,torps=0,armor=0,armor_pts=100,cloak=0,shields=0,sector=0,ship_organics=0,ship_ore=0,ship_goods=0,ship_energy= :start_energy,ship_colonists=0,ship_fighters=100,dev_warpedit=0,dev_genesis=0,dev_beacon=0,dev_emerwarp=0,dev_escapepod='N',dev_fuelscoop='N',dev_minedeflector=0,on_planet='N',dev_lssd='N' WHERE ship_id= :ship_id", [
                'start_energy' => $start_energy,
                'ship_id' => $playerinfo['ship_id']
            ]);
            collect_bounty($planetinfo['owner'], $playerinfo['ship_id']);
        } else {
            db_kill_player($playerinfo['ship_id']);
            collect_bounty($planetinfo['owner'], $playerinfo['ship_id']);
        }
    } else {
        $free_ore = 0;
        $free_goods = 0;
        $free_organics = 0;
        $ship_salvage_rate = 0;
        $ship_salvage = 0;
        $planetrating = $ownerinfo['hull'] + $ownerinfo['engines'] + $ownerinfo['computer'] + $ownerinfo['beams'] + $ownerinfo['torp_launchers'] + $ownerinfo['shields'] + $ownerinfo['armor'];
        if ($ownerinfo['rating'] != 0) {
            $rating_change = ($ownerinfo['rating'] / abs($ownerinfo['rating'])) * $planetrating * 10;
        } else {
            $rating_change = -100;
        }
        echo "<div class='text-center'><h3>$l_cmb_finalcombatstats</h3><br><br>";
        $fighters_lost = $playerinfo['ship_fighters'] - $attackerfighters;
        $l_cmb_youlostfighters = str_replace("[cmb_fighters_lost]", $fighters_lost, $l_cmb_youlostfighters);
        $l_cmb_youlostfighters = str_replace("[cmb_playerinfo_ship_fighters]", $playerinfo['ship_fighters'], $l_cmb_youlostfighters);
        echo "$l_cmb_youlostfighters<br>";
        $armor_lost = $playerinfo['armor_pts'] - $attackerarmor;
        $l_cmb_youlostarmorpoints = str_replace("[cmb_armor_lost]", $armor_lost, $l_cmb_youlostarmorpoints);
        $l_cmb_youlostarmorpoints = str_replace("[cmb_playerinfo_armor_pts]", $playerinfo['armor_pts'], $l_cmb_youlostarmorpoints);
        $l_cmb_youlostarmorpoints = str_replace("[cmb_attackerarmor]", $attackerarmor, $l_cmb_youlostarmorpoints);
        echo "$l_cmb_youlostarmorpoints<br>";
        $energy = $playerinfo['ship_energy'];
        $energy_lost = $start_energy - $playerinfo['ship_energy'];
        $l_cmb_energyused = str_replace("[cmb_energy_lost]", $energy_lost, $l_cmb_energyused);
        $l_cmb_energyused = str_replace("[cmb_playerinfo_ship_energy]", $start_energy, $l_cmb_energyused);
        echo "$l_cmb_energyused</div>";
        
        db()->q("UPDATE ships SET ship_energy= :energy, ship_fighters=ship_fighters- :fighters_lost, torps=torps- :attackertorps, armor_pts=armor_pts- :armor_lost, rating=rating- :rating_change WHERE ship_id= :ship_id", [
            'energy' => $energy,
            'fighters_lost' => $fighters_lost,
            'attackertorps' => $attackertorps,
            'armor_lost' => $armor_lost,
            'rating_change' => $rating_change,
            'ship_id' => $playerinfo['ship_id']
        ]);
    }

    $shipsOnPlanetAfter = db()->fetchAll("SELECT * FROM ships WHERE planet_id= :planet_id AND on_planet='Y'", [
        'planet_id' => $planetinfo['planet_id']
    ]);
    $shipsonplanet = count($shipsOnPlanetAfter);

    if ($planetshields < 1 && $planetfighters < 1 && $attackerarmor > 0 && $shipsonplanet == 0) {
        echo "<div class='text-center'><strong>$l_cmb_planetdefeated</strong></div><br><br>";

        if ($min_value_capture != 0) {
            $playerscore = gen_score($playerinfo['ship_id']);
            $playerscore *= $playerscore;

            $planetscore = $planetinfo['organics'] * $organics_price + $planetinfo['ore'] * $ore_price + $planetinfo['goods'] * $goods_price + $planetinfo['energy'] * $energy_price + $planetinfo['fighters'] * $fighter_price + $planetinfo['torps'] * $torpedo_price + $planetinfo['colonists'] * $colonist_price + $planetinfo['credits'];
            $planetscore = $planetscore * $min_value_capture / 100;

            if ($playerscore < $planetscore) {
                echo "<div class='text-center'>$l_cmb_citizenswanttodie</div><br><br>";
                db()->q("DELETE FROM planets WHERE planet_id= :planet_id", [
                    'planet_id' => $planetinfo['planet_id']
                ]);
                playerlog($ownerinfo['ship_id'], \BNT\Log\LogTypeConstants::LOG_PLANET_DEFEATED_D, sprintf("%s|%s|%s",
                    $planetinfo['name'],
                    $playerinfo['sector'],
                    $playerinfo['character_name']
                ));
                adminlog(\BNT\Log\LogTypeConstants::LOG_ADMIN_PLANETDEL, sprintf("%s|%s|%s",
                    $playerinfo['character_name'],
                    $ownerinfo['character_name'],
                    $playerinfo['sector']
                ));
                gen_score($ownerinfo['ship_id']);
            } else {
                $l_cmb_youmaycapture = str_replace("[cmb_planetinfo_planet_id]", $planetinfo['planet_id'], $l_cmb_youmaycapture);
                echo "<div class='text-center'>$l_cmb_youmaycapture</div><br><br>";
                playerlog($ownerinfo['ship_id'], \BNT\Log\LogTypeConstants::LOG_PLANET_DEFEATED, sprintf("%s|%s|%s",
                    $planetinfo['name'],
                    $playerinfo['sector'],
                    $playerinfo['character_name']
                ));
                gen_score($ownerinfo['ship_id']);
                db()->q("UPDATE planets SET owner=0, fighters=0, torps=torps- :planettorps, base='N', defeated='Y' WHERE planet_id= :planet_id", [
                    'planettorps' => $planettorps,
                    'planet_id' => $planetinfo['planet_id']
                ]);
            }
        } else {
            $l_cmb_youmaycapture2 = str_replace("[cmb_planetinfo_planet_id]", $planetinfo['planet_id'], $l_cmb_youmaycapture2);
            echo "<div class='text-center'>$l_cmb_youmaycapture2</div><br><br>";
            playerlog($ownerinfo['ship_id'], \BNT\Log\LogTypeConstants::LOG_PLANET_DEFEATED, sprintf("%s|%s|%s",
                $planetinfo['name'],
                $playerinfo['sector'],
                $playerinfo['character_name']
            ));
            gen_score($ownerinfo['ship_id']);
            db()->q("UPDATE planets SET owner=0, fighters=0, torps=torps- :planettorps, base='N', defeated='Y' WHERE planet_id= :planet_id", [
                'planettorps' => $planettorps,
                'planet_id' => $planetinfo['planet_id']
            ]);
        }
        calc_ownership($planetinfo['sector_id']);
    } else {
        echo "<div class='text-center'><strong>$l_cmb_planetnotdefeated</strong></div><br><br>";
        $fighters_lost = $planetinfo['fighters'] - $planetfighters;
        $l_cmb_fighterloststat = str_replace("[cmb_fighters_lost]", $fighters_lost, $l_cmb_fighterloststat);
        $l_cmb_fighterloststat = str_replace("[cmb_planetinfo_fighters]", $planetinfo['fighters'], $l_cmb_fighterloststat);
        $l_cmb_fighterloststat = str_replace("[cmb_planetfighters]", $planetfighters, $l_cmb_fighterloststat);
        $energy = $planetinfo['energy'];
        playerlog($ownerinfo['ship_id'], \BNT\Log\LogTypeConstants::LOG_PLANET_NOT_DEFEATED, sprintf("%s|%s|%s|%s|%s|%s|%s|%s",
            $planetinfo['name'],
            $playerinfo['sector'],
            $playerinfo['character_name'],
            $free_ore,
            $free_organics,
            $free_goods,
            $ship_salvage_rate,
            $ship_salvage
        ));
        gen_score($ownerinfo['ship_id']);
        db()->q("UPDATE planets SET energy= :energy, fighters=fighters- :fighters_lost, torps=torps- :planettorps, ore=ore+ :free_ore, goods=goods+ :free_goods, organics=organics+ :free_organics, credits=credits+ :ship_salvage WHERE planet_id= :planet_id", [
            'energy' => $energy,
            'fighters_lost' => $fighters_lost,
            'planettorps' => $planettorps,
            'free_ore' => $free_ore,
            'free_goods' => $free_goods,
            'free_organics' => $free_organics,
            'ship_salvage' => $ship_salvage,
            'planet_id' => $planetinfo['planet_id']
        ]);
    }
    
    db()->q("UPDATE ships SET turns=turns-1, turns_used=turns_used+1 WHERE ship_id= :ship_id", [
        'ship_id' => $playerinfo['ship_id']
    ]);
}

function shiptoship($ship_id)
{
    global $attackerbeams;
    global $attackerfighters;
    global $attackershields;
    global $attackertorps;
    global $attackerarmor;
    global $attackertorpdamage;
    global $start_energy;
    global $playerinfo;
    global $l_cmb_attackershields, $l_cmb_attackertorps, $l_cmb_attackerarmor, $l_cmb_attackertorpdamage;
    global $l_cmb_startingstats, $l_cmb_statattackerbeams, $l_cmb_statattackerfighters, $l_cmb_statattackershields, $l_cmb_statattackertorps;
    global $l_cmb_statattackerarmor, $l_cmb_statattackertorpdamage, $l_cmb_isattackingyou, $l_cmb_beamexchange, $l_cmb_beamsdestroy;
    global $l_cmb_beamsdestroy2, $l_cmb_nobeamsareleft, $l_cmb_beamshavenotarget, $l_cmb_fighterdestroyedbybeams, $l_cmb_beamsdestroystillhave;
    global $l_cmb_fighterunhindered, $l_cmb_youhavenofightersleft, $l_cmb_breachedsomeshields, $l_cmb_shieldsarehitbybeams, $l_cmb_nobeamslefttoattack;
    global $l_cmb_yourshieldsbreachedby, $l_cmb_yourshieldsarehit, $l_cmb_hehasnobeamslefttoattack, $l_cmb_yourbeamsbreachedhim;
    global $l_cmb_yourbeamshavedonedamage, $l_cmb_nobeamstoattackarmor, $l_cmb_yourarmorbreachedbybeams, $l_cmb_yourarmorhitdamaged;
    global $l_cmb_torpedoexchange, $l_cmb_hehasnobeamslefttoattackyou, $l_cmb_yourtorpsdestroy, $l_cmb_yourtorpsdestroy2;
    global $l_cmb_youhavenotorpsleft, $l_cmb_hehasnofighterleft, $l_cmb_torpsdestroyyou, $l_cmb_someonedestroyedfighters, $l_cmb_hehasnotorpsleftforyou;
    global $l_cmb_youhavenofightersanymore, $l_cmb_youbreachedwithtorps, $l_cmb_hisarmorishitbytorps, $l_cmb_notorpslefttoattackarmor;
    global $l_cmb_yourarmorbreachedbytorps, $l_cmb_yourarmorhitdmgtors, $l_cmb_hehasnotorpsforyourarmor, $l_cmb_fightersattackexchange;
    global $l_cmb_enemylostallfighters, $l_cmb_helostsomefighters, $l_cmb_youlostallfighters, $l_cmb_youalsolostsomefighters, $l_cmb_hehasnofightersleftattack;
    global $l_cmb_younofightersattackleft, $l_cmb_youbreachedarmorwithfighters, $l_cmb_youhitarmordmgfighters, $l_cmb_youhavenofighterstoarmor;
    global $l_cmb_hasbreachedarmorfighters, $l_cmb_yourarmorishitfordmgby, $l_cmb_nofightersleftheforyourarmor, $l_cmb_hehasbeendestroyed;
    global $l_cmb_escapepodlaunched, $l_cmb_yousalvaged, $l_cmb_youdidntdestroyhim, $l_cmb_shiptoshipcombatstats;
    global $level_factor, $torp_dmg_rate, $upgrade_cost, $upgrade_factor, $rating_combat_factor;

    $targetinfo = db()->fetch("SELECT * FROM ships WHERE ship_id= :ship_id", [
        'ship_id' => $ship_id
    ]);

    echo "<br><br>-=-=-=-=-=-=-=--<br>
    $l_cmb_startingstats:<br>
    <br>
    $l_cmb_statattackerbeams: $attackerbeams<br>
    $l_cmb_statattackerfighters: $attackerfighters<br>
    $l_cmb_statattackershields: $attackershields<br>
    $l_cmb_statattackertorps: $attackertorps<br>
    $l_cmb_statattackerarmor: $attackerarmor<br>
    $l_cmb_statattackertorpdamage: $attackertorpdamage<br>";

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
    $targettorpdmg = $torp_dmg_rate * $targettorpnum;
    $targetarmor = $targetinfo['armor_pts'];
    $targetfighters = $targetinfo['ship_fighters'];
    $targetdestroyed = 0;
    $playerdestroyed = 0;
    echo "-->{$targetinfo['ship_name']} $l_cmb_isattackingyou<br><br>";
    echo "$l_cmb_beamexchange<br>";
    
    if ($targetfighters > 0 && $attackerbeams > 0) {
        if ($attackerbeams > round($targetfighters / 2)) {
            $temp = round($targetfighters / 2);
            $lost = $targetfighters - $temp;
            $targetfighters = $temp;
            $attackerbeams = $attackerbeams - $lost;
            $l_cmb_beamsdestroy = str_replace("[cmb_lost]", $lost, $l_cmb_beamsdestroy);
            echo "<-- $l_cmb_beamsdestroy<br>";
        } else {
            $targetfighters = $targetfighters - $attackerbeams;
            $l_cmb_beamsdestroy2 = str_replace("[cmb_attackerbeams]", $attackerbeams, $l_cmb_beamsdestroy2);
            echo "--> $l_cmb_beamsdestroy2<br>";
            $attackerbeams = 0;
        }
    } elseif ($targetfighters > 0 && $attackerbeams < 1) {
        echo "$l_cmb_nobeamsareleft<br>";
    } else {
        echo "$l_cmb_beamshavenotarget<br>";
    }
    
    if ($attackerfighters > 0 && $targetbeams > 0) {
        if ($targetbeams > round($attackerfighters / 2)) {
            $temp = round($attackerfighters / 2);
            $lost = $attackerfighters - $temp;
            $attackerfighters = $temp;
            $targetbeams = $targetbeams - $lost;
            $l_cmb_fighterdestroyedbybeams = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $l_cmb_fighterdestroyedbybeams);
            $l_cmb_fighterdestroyedbybeams = str_replace("[cmb_lost]", $lost, $l_cmb_fighterdestroyedbybeams);
            echo "--> $l_cmb_fighterdestroyedbybeams";
        } else {
            $attackerfighters = $attackerfighters - $targetbeams;
            $l_cmb_beamsdestroystillhave = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $l_cmb_beamsdestroystillhave);
            $l_cmb_beamsdestroystillhave = str_replace("[cmb_targetbeams]", $targetbeams, $l_cmb_beamsdestroystillhave);
            $l_cmb_beamsdestroystillhave = str_replace("[cmb_attackerfighters]", $attackerfighters, $l_cmb_beamsdestroystillhave);
            echo "<-- $l_cmb_beamsdestroystillhave<br>";
            $targetbeams = 0;
        }
    } elseif ($attackerfighters > 0 && $targetbeams < 1) {
        echo "$l_cmb_fighterunhindered<br>";
    } else {
        echo "$l_cmb_youhavenofightersleft<br>";
    }
    
    if ($attackerbeams > 0) {
        if ($attackerbeams > $targetshields) {
            $attackerbeams = $attackerbeams - $targetshields;
            $targetshields = 0;
            $l_cmb_breachedsomeshields = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $l_cmb_breachedsomeshields);
            echo "<-- $l_cmb_breachedsomeshields<br>";
        } else {
            $l_cmb_shieldsarehitbybeams = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $l_cmb_shieldsarehitbybeams);
            $l_cmb_shieldsarehitbybeams = str_replace("[cmb_attackerbeams]", $attackerbeams, $l_cmb_shieldsarehitbybeams);
            echo "$l_cmb_shieldsarehitbybeams<br>";
            $targetshields = $targetshields - $attackerbeams;
            $attackerbeams = 0;
        }
    } else {
        $l_cmb_nobeamslefttoattack = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $l_cmb_nobeamslefttoattack);
        echo "$l_cmb_nobeamslefttoattack<br>";
    }
    
    if ($targetbeams > 0) {
        if ($targetbeams > $attackershields) {
            $targetbeams = $targetbeams - $attackershields;
            $attackershields = 0;
            $l_cmb_yourshieldsbreachedby = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $l_cmb_yourshieldsbreachedby);
            echo "--> $l_cmb_yourshieldsbreachedby<br>";
        } else {
            $l_cmb_yourshieldsarehit = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $l_cmb_yourshieldsarehit);
            $l_cmb_yourshieldsarehit = str_replace("[cmb_targetbeams]", $targetbeams, $l_cmb_yourshieldsarehit);
            echo "<-- $l_cmb_yourshieldsarehit<br>";
            $attackershields = $attackershields - $targetbeams;
            $targetbeams = 0;
        }
    } else {
        $l_cmb_hehasnobeamslefttoattack = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $l_cmb_hehasnobeamslefttoattack);
        echo "$l_cmb_hehasnobeamslefttoattack<br>";
    }
    
    if ($attackerbeams > 0) {
        if ($attackerbeams > $targetarmor) {
            $targetarmor = 0;
            $l_cmb_yourbeamsbreachedhim = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $l_cmb_yourbeamsbreachedhim);
            echo "--> $l_cmb_yourbeamsbreachedhim<br>";
        } else {
            $targetarmor = $targetarmor - $attackerbeams;
            $l_cmb_yourbeamshavedonedamage = str_replace("[cmb_attackerbeams]", $attackerbeams, $l_cmb_yourbeamshavedonedamage);
            $l_cmb_yourbeamshavedonedamage = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $l_cmb_yourbeamshavedonedamage);
            echo "$l_cmb_yourbeamshavedonedamage<br>";
        }
    } else {
        $l_cmb_nobeamstoattackarmor = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $l_cmb_nobeamstoattackarmor);
        echo "$l_cmb_nobeamstoattackarmor<br>";
    }
    
    if ($targetbeams > 0) {
        if ($targetbeams > $attackerarmor) {
            $attackerarmor = 0;
            $l_cmb_yourarmorbreachedbybeams = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $l_cmb_yourarmorbreachedbybeams);
            echo "--> $l_cmb_yourarmorbreachedbybeams<br>";
        } else {
            $attackerarmor = $attackerarmor - $targetbeams;
            $l_cmb_yourarmorhitdamaged = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $l_cmb_yourarmorhitdamaged);
            $l_cmb_yourarmorhitdamaged = str_replace("[cmb_targetbeams]", $targetbeams, $l_cmb_yourarmorhitdamaged);
            echo "<-- $l_cmb_yourarmorhitdamaged<br>";
        }
    } else {
        $l_cmb_hehasnobeamslefttoattackyou = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $l_cmb_hehasnobeamslefttoattackyou);
        echo "$l_cmb_hehasnobeamslefttoattackyou<br>";
    }
    
    echo "<br>$l_cmb_torpedoexchange<br>";
    
    if ($targetfighters > 0 && $attackertorpdamage > 0) {
        if ($attackertorpdamage > round($targetfighters / 2)) {
            $temp = round($targetfighters / 2);
            $lost = $targetfighters - $temp;
            $targetfighters = $temp;
            $attackertorpdamage = $attackertorpdamage - $lost;
            $l_cmb_yourtorpsdestroy = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $l_cmb_yourtorpsdestroy);
            $l_cmb_yourtorpsdestroy = str_replace("[cmb_lost]", $lost, $l_cmb_yourtorpsdestroy);
            echo "--> $l_cmb_yourtorpsdestroy<br>";
        } else {
            $targetfighters = $targetfighters - $attackertorpdamage;
            $l_cmb_yourtorpsdestroy2 = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $l_cmb_yourtorpsdestroy2);
            $l_cmb_yourtorpsdestroy2 = str_replace("[cmb_attackertorpdamage]", $attackertorpdamage, $l_cmb_yourtorpsdestroy2);
            echo "<-- $l_cmb_yourtorpsdestroy2<br>";
            $attackertorpdamage = 0;
        }
    } elseif ($targetfighters > 0 && $attackertorpdamage < 1) {
        $l_cmb_youhavenotorpsleft = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $l_cmb_youhavenotorpsleft);
        echo "$l_cmb_youhavenotorpsleft<br>";
    } else {
        $l_cmb_hehasnofighterleft = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $l_cmb_hehasnofighterleft);
        echo "$_cmb_hehasnofighterleft<br>";
    }
    
    if ($attackerfighters > 0 && $targettorpdmg > 0) {
        if ($targettorpdmg > round($attackerfighters / 2)) {
            $temp = round($attackerfighters / 2);
            $lost = $attackerfighters - $temp;
            $attackerfighters = $temp;
            $targettorpdmg = $targettorpdmg - $lost;
            $l_cmb_torpsdestroyyou = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $l_cmb_torpsdestroyyou);
            $l_cmb_torpsdestroyyou = str_replace("[cmb_lost]", $lost, $l_cmb_torpsdestroyyou);
            echo "--> $l_cmb_torpsdestroyyou<br>";
        } else {
            $attackerfighters = $attackerfighters - $targettorpdmg;
            $l_cmb_someonedestroyedfighters = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $l_cmb_someonedestroyedfighters);
            $l_cmb_someonedestroyedfighters = str_replace("[cmb_targettorpdmg]", $targettorpdmg, $l_cmb_someonedestroyedfighters);
            echo "<-- $l_cmb_someonedestroyedfighters<br>";
            $targettorpdmg = 0;
        }
    } elseif ($attackerfighters > 0 && $targettorpdmg < 1) {
        $l_cmb_hehasnotorpsleftforyou = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $l_cmb_hehasnotorpsleftforyou);
        echo "$l_cmb_hehasnotorpsleftforyou<br>";
    } else {
        $l_cmb_youhavenofightersanymore = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $l_cmb_youhavenofightersanymore);
        echo "$l_cmb_youhavenofightersanymore<br>";
    }
    
    if ($attackertorpdamage > 0) {
        if ($attackertorpdamage > $targetarmor) {
            $targetarmor = 0;
            $l_cmb_youbreachedwithtorps = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $l_cmb_youbreachedwithtorps);
            echo "--> $l_cmb_youbreachedwithtorps<br>";
        } else {
            $targetarmor = $targetarmor - $attackertorpdamage;
            $l_cmb_hisarmorishitbytorps = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $l_cmb_hisarmorishitbytorps);
            $l_cmb_hisarmorishitbytorps = str_replace("[cmb_attackertorpdamage]", $attackertorpdamage, $l_cmb_hisarmorishitbytorps);
            echo "<-- $l_cmb_hisarmorishitbytorps<br>";
        }
    } else {
        $l_cmb_notorpslefttoattackarmor = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $l_cmb_notorpslefttoattackarmor);
        echo "$l_cmb_notorpslefttoattackarmor<br>";
    }
    
    if ($targettorpdmg > 0) {
        if ($targettorpdmg > $attackerarmor) {
            $attackerarmor = 0;
            $l_cmb_yourarmorbreachedbytorps = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $l_cmb_yourarmorbreachedbytorps);
            echo "<-- $l_cmb_yourarmorbreachedbytorps<br>";
        } else {
            $attackerarmor = $attackerarmor - $targettorpdmg;
            $l_cmb_yourarmorhitdmgtors = str_replace("[cmb_targettorpdmg]", $targettorpdmg, $l_cmb_yourarmorhitdmgtors);
            $l_cmb_yourarmorhitdmgtors = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $l_cmb_yourarmorhitdmgtors);
            echo "<-- $l_cmb_yourarmorhitdmgtors<br>";
        }
    } else {
        $l_cmb_hehasnotorpsforyourarmor = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $l_cmb_hehasnotorpsforyourarmor);
        echo "$l_cmb_hehasnotorpsforyourarmor<br>";
    }
    
    echo "<br>$l_cmb_fightersattackexchange<br>";
    
    if ($attackerfighters > 0 && $targetfighters > 0) {
        if ($attackerfighters > $targetfighters) {
            $l_cmb_enemylostallfighters = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $l_cmb_enemylostallfighters);
            echo "--> $l_cmb_enemylostallfighters<br>";
            $temptargfighters = 0;
        } else {
            $l_cmb_helostsomefighters = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $l_cmb_helostsomefighters);
            $l_cmb_helostsomefighters = str_replace("[cmb_attackerfighters]", $attackerfighters, $l_cmb_helostsomefighters);
            echo "$l_cmb_helostsomefighters<br>";
            $temptargfighters = $targetfighters - $attackerfighters;
        }
        if ($targetfighters > $attackerfighters) {
            echo "<-- $l_cmb_youlostallfighters<br>";
            $tempplayfighters = 0;
        } else {
            $l_cmb_youalsolostsomefighters = str_replace("[cmb_targetfighters]", $targetfighters, $l_cmb_youalsolostsomefighters);
            echo "<-- $l_cmb_youalsolostsomefighters<br>";
            $tempplayfighters = $attackerfighters - $targetfighters;
        }
        $attackerfighters = $tempplayfighters;
        $targetfighters = $temptargfighters;
    } elseif ($attackerfighters > 0 && $targetfighters < 1) {
        $l_cmb_hehasnofightersleftattack = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $l_cmb_hehasnofightersleftattack);
        echo "$l_cmb_hehasnofightersleftattack<br>";
    } else {
        $l_cmb_younofightersattackleft = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $l_cmb_younofightersattackleft);
        echo "$l_cmb_younofightersattackleft<br>";
    }
    
    if ($attackerfighters > 0) {
        if ($attackerfighters > $targetarmor) {
            $targetarmor = 0;
            $l_cmb_youbreachedarmorwithfighters = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $l_cmb_youbreachedarmorwithfighters);
            echo "--> $l_cmb_youbreachedarmorwithfighters<br>";
        } else {
            $targetarmor = $targetarmor - $attackerfighters;
            $l_cmb_youhitarmordmgfighters = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $l_cmb_youhitarmordmgfighters);
            $l_cmb_youhitarmordmgfighters = str_replace("[cmb_attackerfighters]", $attackerfighters, $l_cmb_youhitarmordmgfighters);
            echo "<-- $l_cmb_youhitarmordmgfighters<br>";
        }
    } else {
        $l_cmb_youhavenofighterstoarmor = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $l_cmb_youhavenofighterstoarmor);
        echo "$l_cmb_youhavenofighterstoarmor<br>";
    }
    
    if ($targetfighters > 0) {
        if ($targetfighters > $attackerarmor) {
            $attackerarmor = 0;
            $l_cmb_hasbreachedarmorfighters = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $l_cmb_hasbreachedarmorfighters);
            echo "<-- $l_cmb_hasbreachedarmorfighters<br>";
        } else {
            $attackerarmor = $attackerarmor - $targetfighters;
            $l_cmb_yourarmorishitfordmgby = str_replace("[cmb_targetfighters]", $targetfighters, $l_cmb_yourarmorishitfordmgby);
            $l_cmb_yourarmorishitfordmgby = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $l_cmb_yourarmorishitfordmgby);
            echo "--> $l_cmb_yourarmorishitfordmgby<br>";
        }
    } else {
        $l_cmb_nofightersleftheforyourarmor = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $l_cmb_nofightersleftheforyourarmor);
        echo "$l_cmb_nofightersleftheforyourarmor<br>";
    }
    
    if ($targetarmor < 1) {
        $l_cmb_hehasbeendestroyed = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $l_cmb_hehasbeendestroyed);
        echo "<br>$l_cmb_hehasbeendestroyed<br>";
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
            $l_cmb_yousalvaged = str_replace("[cmb_salv_ore]", $salv_ore, $l_cmb_yousalvaged);
            $l_cmb_yousalvaged = str_replace("[cmb_salv_organics]", $salv_organics, $l_cmb_yousalvaged);
            $l_cmb_yousalvaged = str_replace("[cmb_salv_goods]", $salv_goods, $l_cmb_yousalvaged);
            $l_cmb_yousalvaged = str_replace("[cmb_salvage_rate]", $ship_salvage_rate, $l_cmb_yousalvaged);
            $l_cmb_yousalvaged = str_replace("[cmb_salvage]", $ship_salvage, $l_cmb_yousalvaged);
            $l_cmb_yousalvaged = str_replace("[cmb_number_rating_change]", NUMBER(abs($rating_change)), $l_cmb_yousalvaged);
            echo "$l_cmb_yousalvaged";
            
            db()->q("UPDATE ships SET ship_ore=ship_ore+ :salv_ore, ship_organics=ship_organics+ :salv_organics, ship_goods=ship_goods+ :salv_goods, credits=credits+ :ship_salvage WHERE ship_id= :ship_id", [
                'salv_ore' => $salv_ore,
                'salv_organics' => $salv_organics,
                'salv_goods' => $salv_goods,
                'ship_salvage' => $ship_salvage,
                'ship_id' => $playerinfo['ship_id']
            ]);
        }

        if ($targetinfo['dev_escapepod'] == "Y") {
            $rating = round($targetinfo['rating'] / 2);
            echo "$l_cmb_escapepodlaunched<br><br>";
            db()->q("UPDATE ships SET hull=0,engines=0,power=0,sensors=0,computer=0,beams=0,torp_launchers=0,torps=0,armor=0,armor_pts=100,cloak=0,shields=0,sector=0,ship_organics=0,ship_ore=0,ship_goods=0,ship_energy= :start_energy,ship_colonists=0,ship_fighters=100,dev_warpedit=0,dev_genesis=0,dev_beacon=0,dev_emerwarp=0,dev_escapepod='N',dev_fuelscoop='N',dev_minedeflector=0,on_planet='N',rating= :rating,dev_lssd='N' WHERE ship_id= :ship_id", [
                'start_energy' => $start_energy,
                'rating' => $rating,
                'ship_id' => $targetinfo['ship_id']
            ]);
            playerlog($targetinfo['ship_id'], \BNT\Log\LogTypeConstants::LOG_ATTACK_LOSE, sprintf("%s|Y", $playerinfo['character_name']));
            collect_bounty($playerinfo['ship_id'], $targetinfo['ship_id']);
        } else {
            playerlog($targetinfo['ship_id'], \BNT\Log\LogTypeConstants::LOG_ATTACK_LOSE, sprintf("%s|N", $playerinfo['character_name']));
            db_kill_player($targetinfo['ship_id']);
            collect_bounty($playerinfo['ship_id'], $targetinfo['ship_id']);
        }
    } else {
        $l_cmb_youdidntdestroyhim = str_replace("[cmb_targetinfo_ship_name]", $targetinfo['ship_name'], $l_cmb_youdidntdestroyhim);
        echo "$l_cmb_youdidntdestroyhim<br>";
        $target_rating_change = round($targetinfo['rating'] * .1);
        $target_armor_lost = $targetinfo['armor_pts'] - $targetarmor;
        $target_fighters_lost = $targetinfo['ship_fighters'] - $targetfighters;
        $target_energy = $targetinfo['ship_energy'];
        playerlog($targetinfo['ship_id'], \BNT\Log\LogTypeConstants::LOG_ATTACKED_WIN, sprintf("%s %s %s",
            $playerinfo['character_name'],
            $armor_lost,
            $fighters_lost
        ));
        
        db()->q("UPDATE ships SET ship_energy= :target_energy, ship_fighters=ship_fighters- :target_fighters_lost, armor_pts=armor_pts- :target_armor_lost, torps=torps- :targettorpnum WHERE ship_id= :ship_id", [
            'target_energy' => $target_energy,
            'target_fighters_lost' => $target_fighters_lost,
            'target_armor_lost' => $target_armor_lost,
            'targettorpnum' => $targettorpnum,
            'ship_id' => $targetinfo['ship_id']
        ]);
    }
    
    echo "<br>_+_+_+_+_+_+_<br>";
    echo "$l_cmb_shiptoshipcombatstats<br>";
    echo "$l_cmb_statattackerbeams: $attackerbeams<br>";
    echo "$l_cmb_statattackerfighters: $attackerfighters<br>";
    echo "$l_cmb_attackershields: $attackershields<br>";
    echo "$l_cmb_attackertorps: $attackertorps<br>";
    echo "$l_cmb_attackerarmor: $attackerarmor<br>";
    echo "$l_cmb_attackertorpdamage: $attackertorpdamage<br>";
    echo "_+_+_+_+_+_+<br>";
}