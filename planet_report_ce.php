<?php

include 'config.php';

$title = $l_pr_title;

include("header.php");

if (checklogin()) {
    die();
}

// This is required by Setup Info
// planet_hack_fix,0.2.0,25-02-2004,TheMightyDude

bigtitle();

echo "<BR>";
echo "Click <A HREF=planet_report.php>here</A> to return to report menu<br>";

if (isset($_POST["TPCreds"])) {
    collect_credits($_POST["TPCreds"]);
} elseif (isset($buildp) and isset($builds)) {
    go_build_base($buildp, $builds);
} else {
    change_planet_production($_POST);
}

echo "<BR><BR>";

function go_build_base($planet_id, $sector_id)
{
    global $base_ore, $base_organics, $base_goods, $base_credits;
    global $l_planet_bbuild;

    echo "<BR>Click <A HREF=planet_report.php?PRepType=1>here</A> to return to the Planet Status Report<BR><BR>";

    $playerinfo = db()->fetch("SELECT * FROM ships WHERE email= :username", [
        'username' => $GLOBALS['username']
    ]);

    $sectorinfo = db()->fetch("SELECT * FROM universe WHERE sector_id= :sector", [
        'sector' => $playerinfo['sector']
    ]);

    $planetinfo = db()->fetch("SELECT * FROM planets WHERE planet_id= :planet_id", [
        'planet_id' => $planet_id
    ]);

    Real_Space_Move($sector_id);

    echo "<BR>Click <A HREF=planet.php?planet_id=$planet_id>here</A> to go to the Planet Menu<BR><BR>";

    // build a base
    if ($planetinfo['ore'] >= $base_ore && $planetinfo['organics'] >= $base_organics && $planetinfo['goods'] >= $base_goods && $planetinfo['credits'] >= $base_credits) {
        // ** Create The Base
        db()->q("UPDATE planets SET base='Y', ore=ore- :base_ore, organics=organics- :base_organics, goods=goods- :base_goods, credits=credits- :base_credits WHERE planet_id= :planet_id", [
            'base_ore' => $base_ore,
            'base_organics' => $base_organics,
            'base_goods' => $base_goods,
            'base_credits' => $base_credits,
            'planet_id' => $planet_id
        ]);

        // ** Update User Turns
        db()->q("UPDATE ships SET turns=turns-1, turns_used=turns_used+1 where ship_id= :ship_id", [
            'ship_id' => $playerinfo['ship_id']
        ]);

        // ** Refresh Plant Info
        $planetinfo = db()->fetch("SELECT * FROM planets WHERE planet_id= :planet_id", [
            'planet_id' => $planet_id
        ]);

        // ** Notify User Of Base Results
        echo "$l_planet_bbuild<BR><BR>";

        // ** Calc Ownership and Notify User Of Results
        $ownership = calc_ownership($playerinfo['sector']);
        if (!empty($ownership)) {
            echo "$ownership<p>";
        }
    }
}

function collect_credits($planetarray)
{
    $CS = "GO"; // Current State
    // create an array of sector -> planet pairs
    $s_p_pair = array();
    for ($i = 0; $i < count($planetarray); $i++) {
        $planet = db()->fetch("SELECT * FROM planets WHERE planet_id= :planet_id", [
            'planet_id' => $planetarray[$i]
        ]);
        $s_p_pair[$i] = array($planet["sector_id"], $planetarray[$i]);
    }

    // Sort the array so that it is in order of sectors, lowest number first, not closest
    sort($s_p_pair);

    // run through the list of sector planet pairs realspace moving to each sector and then performing the transfer.
    for ($i = 0; $i < count($planetarray) && $CS == "GO"; $i++) {
        echo "<BR>";
        $CS = Real_space_move($s_p_pair[$i][0]);

        if ($CS == "HOSTILE") {
            $CS = "GO";
        } elseif ($CS == "GO") {
            $CS = Take_Credits($s_p_pair[$i][0], $s_p_pair[$i][1]);
        } else {
            echo "<BR> NOT ENOUGH TURNS TO TAKE CREDITS<BR>";
        }
        echo "<BR>";
    }

    if ($CS != "GO" && $CS != "HOSTILE") {
        echo "<BR>Not enough turns to complete credit collection<BR>";
    }

    echo "<BR>";
    echo "Click <A HREF=planet_report.php?PRepType=1>here</A> to return to the Planet Status Report<br>";
}

function change_planet_production($prodpercentarray)
{
    global $default_prod_ore, $default_prod_organics, $default_prod_goods, $default_prod_energy, $default_prod_fighters, $default_prod_torp;
    global $username, $l_unnamed;

    $player = db()->fetch("SELECT ship_id,team FROM ships WHERE email= :username", [
        'username' => $username
    ]);
    $ship_id = $player['ship_id'];
    $team_id = $player['team'];

    echo "Click <A HREF=planet_report.php?PRepType=2>here</A> to return to the Change Planet Production Report<br><br>";

    $planet_hack = false;

    foreach ($prodpercentarray as $commod_type => $valarray) {
        if ($commod_type != "team_id" && $commod_type != "ship_id" && is_array($valarray)) {
            foreach ($valarray as $planet_id => $prodpercent) {
                if ($commod_type == "prod_ore" || $commod_type == "prod_organics" || $commod_type == "prod_goods" || $commod_type == "prod_energy" || $commod_type == "prod_fighters" || $commod_type == "prod_torp") {
                    $owned = db()->fetch("SELECT COUNT(*) as owned_planet FROM planets WHERE planet_id= :planet_id AND owner = :ship_id", [
                        'planet_id' => $planet_id,
                        'ship_id' => $ship_id
                    ]);

                    if ($owned['owned_planet'] == 0) {
                        $planet_hack = true;
                    }

                    db()->q("UPDATE planets SET $commod_type= :prodpercent WHERE planet_id= :planet_id AND owner = :ship_id", [
                        'prodpercent' => $prodpercent,
                        'planet_id' => $planet_id,
                        'ship_id' => $ship_id
                    ]);

                    db()->q("UPDATE planets SET sells='N' WHERE planet_id= :planet_id AND owner = :ship_id", [
                        'planet_id' => $planet_id,
                        'ship_id' => $ship_id
                    ]);

                    db()->q("UPDATE planets SET corp=0 WHERE planet_id= :planet_id AND owner = :ship_id", [
                        'planet_id' => $planet_id,
                        'ship_id' => $ship_id
                    ]);
                } elseif ($commod_type == "sells") {
                    db()->q("UPDATE planets SET sells='Y' WHERE planet_id= :planet_id AND owner = :ship_id", [
                        'planet_id' => $prodpercent,
                        'ship_id' => $ship_id
                    ]);
                } elseif ($commod_type == "corp") {
                    /* Compare entered team_id and one in the db */
                    /* If different then use one from db */
                    $owner_team = db()->fetch("SELECT ships.team as owner FROM ships, planets WHERE ( ships.ship_id = planets.owner ) AND ( planets.planet_id = :planet_id)", [
                        'planet_id' => $prodpercent
                    ]);

                    if ($owner_team) {
                        $team_id = $owner_team["owner"];
                    } else {
                        $team_id = 0;
                    }

                    db()->q("UPDATE planets SET corp= :team_id WHERE planet_id= :planet_id AND owner = :ship_id", [
                        'team_id' => $team_id,
                        'planet_id' => $prodpercent,
                        'ship_id' => $ship_id
                    ]);

                    if ($prodpercentarray['team_id'] != $team_id) {
                        /* Oh dear they are different so send admin a log */
                        $planet_hack = true;
                    }
                } else {
                    $planet_hack = true;
                }
            }
        }
    }

    if ($planet_hack) {
        echo "<span style=\"color: red;\"><strong>Your Cheat has been logged to the admin.</strong></span><br>\n";
    }

    echo "<BR>";
    echo "Production Percentages Updated <BR><BR>";
    echo "Checking Values for excess of 100% and negative production values <BR><BR>";

    $planets = db()->fetchAll("SELECT * FROM planets WHERE owner= :ship_id ORDER BY sector_id", [
        'ship_id' => $ship_id
    ]);

    foreach ($planets as $planet) {
        if (empty($planet['name'])) {
            $planet['name'] = $l_unnamed;
        }

        $needs_reset = false;

        if ($planet['prod_ore'] < 0) {
            $planet['prod_ore'] = 110;
            $needs_reset = true;
        }
        if ($planet['prod_organics'] < 0) {
            $planet['prod_organics'] = 110;
            $needs_reset = true;
        }
        if ($planet['prod_goods'] < 0) {
            $planet['prod_goods'] = 110;
            $needs_reset = true;
        }
        if ($planet['prod_energy'] < 0) {
            $planet['prod_energy'] = 110;
            $needs_reset = true;
        }
        if ($planet['prod_fighters'] < 0) {
            $planet['prod_fighters'] = 110;
            $needs_reset = true;
        }
        if ($planet['prod_torp'] < 0) {
            $planet['prod_torp'] = 110;
            $needs_reset = true;
        }

        if ($planet['prod_ore'] + $planet['prod_organics'] + $planet['prod_goods'] + $planet['prod_energy'] + $planet['prod_fighters'] + $planet['prod_torp'] > 100) {
            $needs_reset = true;
        }

        if ($needs_reset) {
            echo "Planet " . $planet['name'] . " in sector " . $planet['sector_id'] . " has a negative production value or exceeds 100% production. Resetting to default production values<BR>";

            db()->q("UPDATE planets SET prod_ore= :prod_ore WHERE planet_id= :planet_id", [
                'prod_ore' => $default_prod_ore,
                'planet_id' => $planet['planet_id']
            ]);

            db()->q("UPDATE planets SET prod_organics= :prod_organics WHERE planet_id= :planet_id", [
                'prod_organics' => $default_prod_organics,
                'planet_id' => $planet['planet_id']
            ]);

            db()->q("UPDATE planets SET prod_goods= :prod_goods WHERE planet_id= :planet_id", [
                'prod_goods' => $default_prod_goods,
                'planet_id' => $planet['planet_id']
            ]);

            db()->q("UPDATE planets SET prod_energy= :prod_energy WHERE planet_id= :planet_id", [
                'prod_energy' => $default_prod_energy,
                'planet_id' => $planet['planet_id']
            ]);

            db()->q("UPDATE planets SET prod_fighters= :prod_fighters WHERE planet_id= :planet_id", [
                'prod_fighters' => $default_prod_fighters,
                'planet_id' => $planet['planet_id']
            ]);

            db()->q("UPDATE planets SET prod_torp= :prod_torp WHERE planet_id= :planet_id", [
                'prod_torp' => $default_prod_torp,
                'planet_id' => $planet['planet_id']
            ]);
        }
    }
}

function Take_Credits($sector_id, $planet_id)
{
    global $username, $l_unnamed;

    // Get basic Database information (ship and planet)
    $playerinfo = db()->fetch("SELECT * FROM ships WHERE email= :username", [
        'username' => $username
    ]);

    $planetinfo = db()->fetch("SELECT * FROM planets WHERE planet_id= :planet_id", [
        'planet_id' => $planet_id
    ]);

    // Set the name for unamed planets to be "unnamed"
    if (empty($planetinfo['name'])) {
        $planetinfo['name'] = $l_unnamed;
    }

    //verify player is still in same sector as the planet
    if ($playerinfo['sector'] == $planetinfo['sector_id']) {
        if ($playerinfo['turns'] >= 1) {
            // verify player owns the planet to take credits from
            if ($planetinfo['owner'] == $playerinfo['ship_id']) {
                // get number of credits from the planet and current number player has on ship
                $CreditsTaken = $planetinfo['credits'];
                $CreditsOnShip = $playerinfo['credits'];
                $NewShipCredits = $CreditsTaken + $CreditsOnShip;

                // update the planet record for credits
                db()->q("UPDATE planets SET credits=0 WHERE planet_id= :planet_id", [
                    'planet_id' => $planetinfo['planet_id']
                ]);

                // update the player record
                // credits
                db()->q("UPDATE ships SET credits= :credits WHERE email= :username", [
                    'credits' => $NewShipCredits,
                    'username' => $username
                ]);

                // turns
                db()->q("UPDATE ships SET turns=turns-1 WHERE email= :username", [
                    'username' => $username
                ]);

                echo "Took " . NUMBER($CreditsTaken) . " Credits from planet " . $planetinfo['name'] . ". <BR>";
                echo "Your ship - " . $playerinfo['ship_name'] . " - now has " . NUMBER($NewShipCredits) . " onboard. <BR>";
                $retval = "GO";
            } else {
                echo "<BR><BR>You do not own planet " . $planetinfo['name'] . "<BR><BR>";
                $retval = "GO";
            }
        } else {
            echo "<BR><BR>You do not have enough turns to take credits from " . $planetinfo['name'] . " in sector " . $planetinfo['sector_id'] . "<BR><BR>";
            $retval = "BREAK-TURNS";
        }
    } else {
        echo "<BR><BR>You must be in the same sector as the planet to transfer to/from the planet<BR><BR>";
        $retval = "BREAK-SECTORS";
    }
    return($retval);
}

function Real_Space_Move($destination)
{
    global $level_factor;
    global $username;

    $playerinfo = db()->fetch("SELECT * FROM ships WHERE email= :username", [
        'username' => $username
    ]);

    $start = db()->fetch("SELECT angle1,angle2,distance FROM universe WHERE sector_id= :sector", [
        'sector' => $playerinfo['sector']
    ]);

    $finish = db()->fetch("SELECT angle1,angle2,distance FROM universe WHERE sector_id= :destination", [
        'destination' => $destination
    ]);

    $sa1 = $start['angle1'] * $GLOBALS['deg'];
    $sa2 = $start['angle2'] * $GLOBALS['deg'];
    $fa1 = $finish['angle1'] * $GLOBALS['deg'];
    $fa2 = $finish['angle2'] * $GLOBALS['deg'];

    $x = ($start['distance'] * sin($sa1) * cos($sa2)) - ($finish['distance'] * sin($fa1) * cos($fa2));
    $y = ($start['distance'] * sin($sa1) * sin($sa2)) - ($finish['distance'] * sin($fa1) * sin($fa2));
    $z = ($start['distance'] * cos($sa1)) - ($finish['distance'] * cos($fa1));
    $distance = round(sqrt(mypw($x, 2) + mypw($y, 2) + mypw($z, 2)));
    $shipspeed = mypw($level_factor, $playerinfo['engines']);
    $triptime = round($distance / $shipspeed);

    if ($triptime == 0 && $destination != $playerinfo['sector']) {
        $triptime = 1;
    }

    if ($playerinfo['dev_fuelscoop'] == "Y") {
        $energyscooped = $distance * 100;
    } else {
        $energyscooped = 0;
    }

    if ($playerinfo['dev_fuelscoop'] == "Y" && $energyscooped == 0 && $triptime == 1) {
        $energyscooped = 100;
    }
    $free_power = NUM_ENERGY($playerinfo['power']) - $playerinfo['ship_energy'];

    // amount of energy that can be stored is less than amount scooped amount scooped is set to what can be stored
    if ($free_power < $energyscooped) {
        $energyscooped = $free_power;
    }

    // make sure energyscooped is not null
    if (!isset($energyscooped)) {
        $energyscooped = "0";
    }

    // make sure energyscooped not negative, or decimal
    if ($energyscooped < 1) {
        $energyscooped = 0;
    }

    // check to see if already in that sector
    if ($destination == $playerinfo['sector']) {
        $triptime = 0;
        $energyscooped = 0;
    }

    if ($triptime > $playerinfo['turns']) {
        $l_rs_movetime = str_replace("[triptime]", NUMBER($triptime), $GLOBALS['l_rs_movetime']);
        echo "$l_rs_movetime<BR><BR>";
        echo $GLOBALS['l_rs_noturns'];

        db()->q("UPDATE ships SET cleared_defences=' ' where ship_id= :ship_id", [
            'ship_id' => $playerinfo['ship_id']
        ]);

        $retval = "BREAK-TURNS";
    } else {
        // ***** Sector Defense Check *****
        $hostile = 0;

        $defences = db()->fetchAll("SELECT * FROM sector_defence WHERE sector_id = :destination AND ship_id <> :ship_id", [
            'destination' => $destination,
            'ship_id' => $playerinfo['ship_id']
        ]);

        foreach ($defences as $defence) {
            $owner = db()->fetch("SELECT * from ships where ship_id= :ship_id", [
                'ship_id' => $defence['ship_id']
            ]);

            if ($owner['team'] != $playerinfo['team'] || $playerinfo['team'] == 0) {
                $hostile = 1;
                break;
            }
        }

        if (($hostile > 0) && ($playerinfo['hull'] > $GLOBALS['mine_hullsize'])) {
            $retval = "HOSTILE";
            echo "CANNOT MOVE TO SECTOR $destination THROUGH HOSTILE DEFENSES<br>";
        } else {
            $stamp = date("Y-m-d H-i-s");

            db()->q("UPDATE ships SET last_login= :stamp, sector= :destination, ship_energy=ship_energy+ :energyscooped, turns=turns- :triptime, turns_used=turns_used+ :triptime WHERE ship_id= :ship_id", [
                'stamp' => $stamp,
                'destination' => $destination,
                'energyscooped' => $energyscooped,
                'triptime' => $triptime,
                'ship_id' => $playerinfo['ship_id']
            ]);

            $l_rs_ready = str_replace("[sector]", $destination, $GLOBALS['l_rs_ready']);
            $l_rs_ready = str_replace("[triptime]", NUMBER($triptime), $l_rs_ready);
            $l_rs_ready = str_replace("[energy]", NUMBER($energyscooped), $l_rs_ready);
            echo "$l_rs_ready<BR>";
            $retval = "GO";
        }
    }
    return($retval);
}

include("footer.php");
