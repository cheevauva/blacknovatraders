<?php

if (preg_match("/sched_xenobe.php/i", $_SERVER['PHP_SELF'])) {
    echo "You can not access this file directly!";
    die();
}

// New database driven language entries
load_languages($db, $lang, array('sched_xenobe', 'common', 'global_includes', 'combat', 'footer', 'news'), $langvars, $db_logging);

global $targetlink;
global $xenobeisdead;

// Make Xenobe selection
$furcount = $furcount0 = $furcount0a = $furcount1 = $furcount1a = $furcount2 = $furcount2a = $furcount3 = $furcount3a = $furcount3h = 0;

$xenobes = db()->fetchAll("SELECT * FROM ships JOIN xenobe WHERE email = xenobe_id and active = 'Y' and ship_destroyed = 'N' ORDER BY ship_id");

foreach ($xenobes as $playerinfo) {
    $xenobeisdead = 0;

    xenoberegen();

    // Run through orders
    $furcount++;
    if (mt_rand(1, 5) > 1) {                                 // 20% Chance of not moving at all
        // Orders = 0 Sentinel
        if ($playerinfo['orders'] == 0) {
            $furcount0++;
            // Find a target in my sector, not myself, not on a planet

            $rowo0 = db()->fetch("SELECT * FROM ships WHERE sector = :sector AND email != :email AND email NOT LIKE '%@xenobe' AND planet_id = 0 AND ship_id > 1", [
                'sector' => $playerinfo['sector'],
                'email' => $playerinfo['email']
            ]);

            if ($rowo0) {
                if ($playerinfo['aggression'] == 0) {            // O = 0 & Aggression = 0 Peaceful
                    // This Guy Does Nothing But Sit As A Target Himself
                } elseif ($playerinfo['aggression'] == 1) {        // O = 0 & Aggression = 1 Attack sometimes O = 0
                    // Xenobe's only compare number of fighters when determining if they have an attack advantage
                    if ($playerinfo['ship_fighters'] > $rowo0['ship_fighters']) {
                        $furcount0a++;
                        playerlog($db, $playerinfo['ship_id'], LOG_Xenobe_ATTACK, $rowo0['character_name']);
                        xenobetoship($rowo0['ship_id']);
                        if ($xenobeisdead > 0) {
                            continue;
                        }
                    }
                } elseif ($playerinfo['aggression'] == 2) {        // O = 0 & Aggression = 2 attack always
                    $furcount0a++;
                    playerlog($db, $playerinfo['ship_id'], LOG_Xenobe_ATTACK, $rowo0['character_name']);
                    xenobetoship($rowo0['ship_id']);
                    if ($xenobeisdead > 0) {
                        continue;
                    }
                }
            }
        }
        // Orders = 1 roam
        elseif ($playerinfo['orders'] == 1) {
            $furcount1++;
            // Roam to a new sector before doing anything else
            $targetlink = $playerinfo['sector'];
            xenobemove($playerinfo, $playerinfo['sector']);
            if ($xenobeisdead > 0) {
                continue;
            }
            // Find a target in my sector, not myself
            $rowo1 = db()->fetch("SELECT * FROM ships WHERE sector = :sector and email != :email and ship_id > 1", [
                'sector' => $targetlink,
                'email' => $playerinfo['email']
            ]);

            if ($rowo1) {
                if ($playerinfo['aggression'] == 0) {            // O = 1 & Aggression = 0 Peaceful O = 1
                    // This Guy Does Nothing But Roam Around As A Target Himself
                } elseif ($playerinfo['aggression'] == 1) {        // O = 1 & AGRESSION = 1 ATTACK SOMETIMES
                    // Xenobe's only compare number of fighters when determining if they have an attack advantage
                    if ($playerinfo['ship_fighters'] > $rowo1['ship_fighters'] && $rowo1['planet_id'] == 0) {
                        $furcount1a++;
                        playerlog($db, $playerinfo['ship_id'], LOG_Xenobe_ATTACK, $rowo1['character_name']);
                        xenobetoship($rowo1['ship_id']);
                        if ($xenobeisdead > 0) {
                            continue;
                        }
                    }
                } elseif ($playerinfo['aggression'] == 2) {        //  O = 1 & AGRESSION = 2 ATTACK ALLWAYS
                    $furcount1a++;
                    playerlog($db, $playerinfo['ship_id'], LOG_Xenobe_ATTACK, $rowo1['character_name']);
                    if ($rowo1['planet_id'] != 0) {              // IS ON PLANET
                        xenobetoplanet($rowo1['planet_id']);
                    } else {
                        xenobetoship($rowo1['ship_id']);
                    }
                    if ($xenobeisdead > 0) {
                        continue;
                    }
                }
            }
        }
        // ORDERS = 2 ROAM AND TRADE
        elseif ($playerinfo['orders'] == 2) {
            $furcount2++;
            // ROAM TO A NEW SECTOR BEFORE DOING ANYTHING ELSE
            $targetlink = $playerinfo['sector'];
            xenobemove($playerinfo, $playerinfo['sector']);
            if ($xenobeisdead > 0) {
                continue;
            }
            // NOW TRADE BEFORE WE DO ANY AGGRESSION CHECKS
            xenobetrade();
            // FIND A TARGET
            // IN MY SECTOR, NOT MYSELF
            $rowo2 = db()->fetch("SELECT * FROM ships WHERE sector = :sector and email != :email and ship_id > 1", [
                'sector' => $targetlink,
                'email' => $playerinfo['email']
            ]);

            if ($rowo2) {
                if ($playerinfo['aggression'] == 0) {            // O = 2 & AGRESSION = 0 PEACEFUL
                    // This Guy Does Nothing But Roam And Trade
                } elseif ($playerinfo['aggression'] == 1) {        // O = 2 & AGRESSION = 1 ATTACK SOMETIMES 
                    // Xenobe's only compare number of fighters when determining if they have an attack advantage
                    if ($playerinfo['ship_fighters'] > $rowo2['ship_fighters'] && $rowo2['planet_id'] == 0) {
                        $furcount2a++;
                        playerlog($db, $playerinfo['ship_id'], LOG_Xenobe_ATTACK, $rowo2['character_name']);
                        xenobetoship($rowo2['ship_id']);
                        if ($xenobeisdead > 0) {
                            continue;
                        }
                    }
                } elseif ($playerinfo['aggression'] == 2) {        // O = 2 & AGRESSION = 2 ATTACK ALLWAYS
                    $furcount2a++;
                    playerlog($db, $playerinfo['ship_id'], LOG_Xenobe_ATTACK, $rowo2['character_name']);
                    if ($rowo2['planet_id'] != 0) {              // IS ON PLANET
                        xenobetoplanet($rowo2['planet_id']);
                    } else {
                        xenobetoship($rowo2['ship_id']);
                    }
                    if ($xenobeisdead > 0) {
                        continue;
                    }
                }
            }
        }
        // ORDERS = 3 ROAM AND HUNT
        elseif ($playerinfo['orders'] == 3) {
            $furcount3++;
            // LET SEE IF WE GO HUNTING THIS ROUND BEFORE WE DO ANYTHING ELSE
            $hunt = mt_rand(0, 3);                               // 25% CHANCE OF HUNTING
            // Uncomment below for Debugging
            //$hunt=0;
            if ($hunt == 0) {
                $furcount3h++;
                xenobehunter();
                if ($xenobeisdead > 0) {
                    continue;
                }
            } else {
                // ROAM TO A NEW SECTOR BEFORE DOING ANYTHING ELSE
                xenobemove();
                if ($xenobeisdead > 0) {
                    continue;
                }
                // FIND A TARGET
                // IN MY SECTOR, NOT MYSELF
                $rowo3 = db()->fetch("SELECT * FROM ships WHERE sector = :sector and email != :email and ship_id > 1", [
                    'sector' => $playerinfo['sector'],
                    'email' => $playerinfo['email']
                ]);

                if ($rowo3) {
                    if ($playerinfo['aggression'] == 0) {            // O = 3 & AGRESSION = 0 PEACEFUL
                        // This Guy Does Nothing But Roam Around As A Target Himself
                    } elseif ($playerinfo['aggression'] == 1) {        // O = 3 & AGRESSION = 1 ATTACK SOMETIMES
                        // Xenobe's only compare number of fighters when determining if they have an attack advantage
                        if ($playerinfo['ship_fighters'] > $rowo3['ship_fighters'] && $rowo3['planet_id'] == 0) {
                            $furcount3a++;
                            playerlog($db, $playerinfo['ship_id'], LOG_Xenobe_ATTACK, $rowo3['character_name']);
                            xenobetoship($rowo3['ship_id']);
                            if ($xenobeisdead > 0) {
                                continue;
                            }
                        }
                    } elseif ($playerinfo['aggression'] == 2) {        // O = 3 & AGRESSION = 2 ATTACK ALLWAYS
                        $furcount3a++;
                        playerlog($db, $playerinfo['ship_id'], LOG_Xenobe_ATTACK, $rowo3['character_name']);
                        if ($rowo3['planet_id'] != 0) {              // IS ON PLANET
                            xenobetoplanet($rowo3['planet_id']);
                        } else {
                            xenobetoship($rowo3['ship_id']);
                        }
                        if ($xenobeisdead > 0) {
                            continue;
                        }
                    }
                }
            }
        }
    }
}

$furnonmove = $furcount - ($furcount0 + $furcount1 + $furcount2 + $furcount3);
echo "Counted $furcount Xenobe players that are ACTIVE with working ships.<br>";
echo "$furnonmove Xenobe players did not do anything this round. <br>";
echo "$furcount0 Xenobe players had SENTINEL orders of which $furcount0a launched attacks. <br>";
echo "$furcount1 Xenobe players had ROAM orders of which $furcount1a launched attacks. <br>";
echo "$furcount2 Xenobe players had ROAM AND TRADE orders of which $furcount2a launched attacks. <br>";
echo "$furcount3 Xenobe players had ROAM AND HUNT orders of which $furcount3a launched attacks and $furcount3h went hunting. <br>";
echo "Xenobe TURNS COMPLETE. <br>";
echo "<br>";
// END OF Xenobe TURNS
