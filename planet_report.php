<?php

include 'config.php';

$title = $l_pr_title;

include("header.php");

if (checklogin()) {
    die();
}

// Get data about planets


// determine what type of report is displayed and display it's title
if ($PRepType == 1 || !isset($PRepType)) { // display the commodities on the planets
    $title = "$title: Status";
    bigtitle();
    standard_report();
} elseif ($PRepType == 2) {                  // display the production values of your planets and allow changing
    $title = "$title: Production";
    bigtitle();
    planet_production_change();
} elseif ($PRepType == 0) {                  // For typing in manually to get a report menu
    $title = "$title: Menu";
    bigtitle();
    planet_report_menu();
} else { // display the menu if no valid options are passed in
    $title = "$title: Status";
    bigtitle();
    planet_report();
}

// ---- Begin functions ------ //
function planet_report_menu()
{
    global $playerinfo;
    global $l_pr_teamlink;

    echo "<strong><A HREF=planet_report.php?PRepType=1 NAME=Planet Status>Planet Status</A></strong><BR>" .
    "Displays the number of each Commodity on the planet (Ore, Organics, Goods, Energy, Colonists, Credits, Fighters, and Torpedoes)<BR>" .
    "<BR>" .
    "<strong><A HREF=planet_report.php?PRepType=2 NAME=Planet Status>Change Production</A></strong> &nbsp;&nbsp; <strong>Base Required</strong> on Planet<BR>" .
    "This Report allows you to change the rate of production of commondits on planets that have a base<BR>" .
    "-- You must travel to the planet to build a base set the planet to coporate or change the name (celebrations and such)<BR>";

    if ($playerinfo['team'] > 0) {
        echo "<BR>" .
        "<strong><A HREF=team_planets.php>$l_pr_teamlink</A></strong><BR> " .
        "Commondity Report (like Planet Status) for planets marked Corporate by you and/or your fellow alliance member<BR>" .
        "<BR>";
    }
}

function standard_report()
{
    global $playerinfo;
    global $sort;
    global $color_line1, $color_line2;
    global $l_pr_teamlink, $l_pr_clicktosort;
    global $l_sector, $l_name, $l_unnamed, $l_ore, $l_organics, $l_goods, $l_energy, $l_colonists, $l_credits, $l_fighters, $l_torps, $l_base, $l_selling, $l_pr_totals, $l_yes, $l_no;

    echo "Planetary report descriptions and <strong><A HREF=planet_report.php?PRepType=0>menu</A></strong><BR>" .
    "<BR>" .
    "<strong><A HREF=planet_report.php?PRepType=2>Change Production</A></strong> &nbsp;&nbsp; <strong>Base Required</strong> on Planet<BR>";

    if ($playerinfo['team'] > 0) {
        echo "<BR>" .
        "<strong><A HREF=team_planets.php>$l_pr_teamlink</A></strong><BR> " .
        "<BR>";
    }

    $query = "SELECT * FROM planets WHERE owner= :owner";

    if (!empty($sort)) {
        $query .= " ORDER BY";
        if ($sort == "name") {
            $query .= " $sort ASC";
        } elseif (
        $sort == "organics" || $sort == "ore" || $sort == "goods" || $sort == "energy" ||
        $sort == "colonists" || $sort == "credits" || $sort == "fighters"
        ) {
            $query .= " $sort DESC, sector_id ASC";
        } elseif ($sort == "torp") {
            $query .= " torps DESC, sector_id ASC";
        } else {
            $query .= " sector_id ASC";
        }
    } else {
        $query .= " ORDER BY sector_id ASC";
    }

    $planets = db()->fetchAll($query, [
        'owner' => $playerinfo['ship_id']
    ]);

    $num_planets = count($planets);
    if ($num_planets < 1) {
        echo "<BR>$l_pr_noplanet";
    } else {
        echo "<BR>";
        echo "<FORM ACTION=planet_report_ce.php METHOD=POST>";

        // ------ next block of echo's creates the header of the table
        echo "$l_pr_clicktosort<BR><BR>";
        echo "<strong>WARNING:</strong> \"Build\" and \"Take Credits\" will cause your ship to move. <BR><BR>";
        echo "<TABLE class=\"table\">";
        echo "<TR>";
        echo "<TH><A HREF=planet_report.php?PRepType=1&sort=sector_id>$l_sector</A></TH>";
        echo "<TH><A HREF=planet_report.php?PRepType=1&sort=name>$l_name</A></TH>";
        echo "<TH><A HREF=planet_report.php?PRepType=1&sort=ore>$l_ore</A></TH>";
        echo "<TH><A HREF=planet_report.php?PRepType=1&sort=organics>$l_organics</A></TH>";
        echo "<TH><A HREF=planet_report.php?PRepType=1&sort=goods>$l_goods</A></TH>";
        echo "<TH><A HREF=planet_report.php?PRepType=1&sort=energy>$l_energy</A></TH>";
        echo "<TH><A HREF=planet_report.php?PRepType=1&sort=colonists>$l_colonists</A></TH>";
        echo "<TH><A HREF=planet_report.php?PRepType=1&sort=credits>$l_credits</A></TH>";
        echo "<TH>Take Credits</TH>";
        echo "<TH><A HREF=planet_report.php?PRepType=1&sort=fighters>$l_fighters</A></TH>";
        echo "<TH><A HREF=planet_report.php?PRepType=1&sort=torp>$l_torps</A></TH>";
        echo "<TH>$l_base?</TH>";
        if ($playerinfo['team'] > 0) {
            echo "<TH>Corp?</TH>";
        }
        echo "<TH>$l_selling?</TH>";

        // ------ next block of echo's fills the table and calculates the totals
        echo "</TR>";

        $total_organics = 0;
        $total_ore = 0;
        $total_goods = 0;
        $total_energy = 0;
        $total_colonists = 0;
        $total_credits = 0;
        $total_fighters = 0;
        $total_torp = 0;
        $total_base = 0;
        $total_corp = 0;
        $total_selling = 0;

        foreach ($planets as $planet) {
            $total_organics += $planet['organics'];
            $total_ore += $planet['ore'];
            $total_goods += $planet['goods'];
            $total_energy += $planet['energy'];
            $total_colonists += $planet['colonists'];
            $total_credits += $planet['credits'];
            $total_fighters += $planet['fighters'];
            $total_torp += $planet['torps'];

            if ($planet['base'] == "Y") {
                $total_base += 1;
            }
            if ($planet['corp'] > 0) {
                $total_corp += 1;
            }
            if ($planet['sells'] == "Y") {
                $total_selling += 1;
            }

            if (empty($planet['name'])) {
                $planet['name'] = $l_unnamed;
            }

            echo "<TR>";
            echo "<TD><A HREF=rsmove.php?engage=1&destination=" . $planet['sector_id'] . ">" . $planet['sector_id'] . "</A></TD>";
            echo "<TD>" . $planet['name'] . "</TD>";
            echo "<TD>" . NUMBER($planet['ore']) . "</TD>";
            echo "<TD>" . NUMBER($planet['organics']) . "</TD>";
            echo "<TD>" . NUMBER($planet['goods']) . "</TD>";
            echo "<TD>" . NUMBER($planet['energy']) . "</TD>";
            echo "<TD>" . NUMBER($planet['colonists']) . "</TD>";
            echo "<TD>" . NUMBER($planet['credits']) . "</TD>";
            echo "<TD>" . "<INPUT TYPE=CHECKBOX NAME=TPCreds[] VALUE=\"" . $planet["planet_id"] . "\">" . "</TD>";
            echo "<TD>" . NUMBER($planet['fighters']) . "</TD>";
            echo "<TD>" . NUMBER($planet['torps']) . "</TD>";
            echo "<TD>" . base_build_check($planet) . "</TD>";
            if ($playerinfo['team'] > 0) {
                echo "<TD>" . ($planet['corp'] > 0 ? "$l_yes" : "$l_no") . "</TD>";
            }
            echo "<TD>" . ($planet['sells'] == 'Y' ? "$l_yes" : "$l_no") . "</TD>";
            echo "</TR>";
        }

        // the next block displays the totals
        echo "<TR>";
        echo "<TD COLSPAN=2>$l_pr_totals</TD>";
        echo "<TD>" . NUMBER($total_ore) . "</TD>";
        echo "<TD>" . NUMBER($total_organics) . "</TD>";
        echo "<TD>" . NUMBER($total_goods) . "</TD>";
        echo "<TD>" . NUMBER($total_energy) . "</TD>";
        echo "<TD>" . NUMBER($total_colonists) . "</TD>";
        echo "<TD>" . NUMBER($total_credits) . "</TD>";
        echo "<TD></TD>";
        echo "<TD>" . NUMBER($total_fighters) . "</TD>";
        echo "<TD>" . NUMBER($total_torp) . "</TD>";
        echo "<TD>" . NUMBER($total_base) . "</TD>";
        if ($playerinfo['team'] > 0) {
            echo "<TD>" . NUMBER($total_corp) . "</TD>";
        }
        echo "<TD>" . NUMBER($total_selling) . "</TD>";
        echo "</TR>";
        echo "</TABLE>";

        echo "<BR>";
        echo "<INPUT TYPE=SUBMIT VALUE=\"Collect Credits\">  <INPUT TYPE=RESET VALUE=RESET>";
        echo "</FORM>";
    }
}

function planet_production_change()
{
    global $playerinfo;
    global $sort;
    global $color_line1, $color_line2;
    global $l_pr_teamlink, $l_pr_clicktosort;
    global $l_sector, $l_name, $l_unnamed, $l_ore, $l_organics, $l_goods, $l_energy, $l_colonists, $l_credits, $l_fighters, $l_torps, $l_base, $l_selling, $l_pr_totals, $l_yes, $l_no;

    $query = "SELECT * FROM planets WHERE owner= :owner AND base='Y'";

    echo "Planetary report <strong><A HREF=planet_report.php?PRepType=0>menu</A></strong><BR>" .
    "<BR>" .
    "<strong><A HREF=planet_report.php?PRepType=1>Planet Status</A></strong><BR>";

    if ($playerinfo['team'] > 0) {
        echo "<BR>" .
        "<strong><A HREF=team_planets.php>$l_pr_teamlink</A></strong><BR> " .
        "<BR>";
    }

    if (!empty($sort)) {
        $query .= " ORDER BY";
        if ($sort == "name") {
            $query .= " $sort ASC";
        } elseif ($sort == "organics" || $sort == "ore" || $sort == "goods" || $sort == "energy" || $sort == "fighters") {
            $query .= " prod_$sort DESC, sector_id ASC";
        } elseif ($sort == "colonists" || $sort == "credits") {
            $query .= " $sort DESC, sector_id ASC";
        } elseif ($sort == "torp") {
            $query .= " prod_torp DESC, sector_id ASC";
        } else {
            $query .= " sector_id ASC";
        }
    } else {
        $query .= " ORDER BY sector_id ASC";
    }

    $planets = db()->fetchAll($query, [
        'owner' => $playerinfo['ship_id']
    ]);

    $num_planets = count($planets);
    if ($num_planets < 1) {
        echo "<BR>$l_pr_noplanet";
    } else {
        echo "<FORM ACTION=planet_report_ce.php METHOD=POST>";

        // ------ next block of echo's creates the header of the table
        echo "$l_pr_clicktosort<BR><BR>";
        echo "<TABLE class=\"table\">";
        echo "<TR>";
        echo "<TH><A HREF=planet_report.php?PRepType=2&sort=sector_id>$l_sector</A></TH>";
        echo "<TH><A HREF=planet_report.php?PRepType=2&sort=name>$l_name</A></TH>";
        echo "<TH><A HREF=planet_report.php?PRepType=2&sort=ore>$l_ore</A></TH>";
        echo "<TH><A HREF=planet_report.php?PRepType=2&sort=organics>$l_organics</A></TH>";
        echo "<TH><A HREF=planet_report.php?PRepType=2&sort=goods>$l_goods</A></TH>";
        echo "<TH><A HREF=planet_report.php?PRepType=2&sort=energy>$l_energy</A></TH>";
        echo "<TH><A HREF=planet_report.php?PRepType=2&sort=colonists>$l_colonists</A></TH>";
        echo "<TH><A HREF=planet_report.php?PRepType=2&sort=credits>$l_credits</A></TH>";
        echo "<TH><A HREF=planet_report.php?PRepType=2&sort=fighters>$l_fighters</A></TH>";
        echo "<TH><A HREF=planet_report.php?PRepType=2&sort=torp>$l_torps</A></TH>";
        if ($playerinfo['team'] > 0) {
            echo "<TH>Corp?</TH>";
        }
        echo "<TH>$l_selling?</TH>";
        echo "</TR>";

        $total_colonists = 0;
        $total_credits = 0;
        $total_corp = 0;

        foreach ($planets as $planet) {
            $total_colonists += $planet['colonists'];
            $total_credits += $planet['credits'];

            if (empty($planet['name'])) {
                $planet['name'] = $l_unnamed;
            }

            echo "<TR>";
            echo "<TD><A HREF=rsmove.php?engage=1&destination=" . $planet['sector_id'] . ">" . $planet['sector_id'] . "</A></TD>";
            echo "<TD>" . $planet['name'] . "</TD>";
            echo "<TD>" . "<input size=6 type=text name=\"prod_ore[" . $planet["planet_id"] . "]\" value=\"" . $planet["prod_ore"] . "\">" . "</TD>";
            echo "<TD>" . "<input size=6 type=text name=\"prod_organics[" . $planet["planet_id"] . "]\" value=\"" . $planet["prod_organics"] . "\">" . "</TD>";
            echo "<TD>" . "<input size=6 type=text name=\"prod_goods[" . $planet["planet_id"] . "]\" value=\"" . $planet["prod_goods"] . "\">" . "</TD>";
            echo "<TD>" . "<input size=6 type=text name=\"prod_energy[" . $planet["planet_id"] . "]\" value=\"" . $planet["prod_energy"] . "\">" . "</TD>";
            echo "<TD>" . NUMBER($planet['colonists']) . "</TD>";
            echo "<TD>" . NUMBER($planet['credits']) . "</TD>";
            echo "<TD>" . "<input size=6 type=text name=\"prod_fighters[" . $planet["planet_id"] . "]\" value=\"" . $planet["prod_fighters"] . "\">" . "</TD>";
            echo "<TD>" . "<input size=6 type=text name=\"prod_torp[" . $planet["planet_id"] . "]\" value=\"" . $planet["prod_torp"] . "\">" . "</TD>";
            if ($playerinfo['team'] > 0) {
                echo "<TD>" . corp_planet_checkboxes($planet) . "</TD>";
            }
            echo "<TD>" . selling_checkboxes($planet) . "</TD>";
            echo "</TR>";
        }

        echo "<TR>";
        echo "<TD COLSPAN=2>$l_pr_totals</TD>";
        echo "<TD></TD>";
        echo "<TD></TD>";
        echo "<TD></TD>";
        echo "<TD></TD>";
        echo "<TD>" . NUMBER($total_colonists) . "</TD>";
        echo "<TD>" . NUMBER($total_credits) . "</TD>";
        echo "<TD></TD>";
        echo "<TD></TD>";
        if ($playerinfo['team'] > 0) {
            echo "<TD></TD>";
        }
        echo "<TD></TD>";
        echo "</TR>";
        echo "</TABLE>";

        echo "<BR>";
        echo "<INPUT TYPE=HIDDEN NAME=ship_id VALUE='" . $playerinfo['ship_id'] . "'>";
        echo "<INPUT TYPE=HIDDEN NAME=team_id   VALUE='" . $playerinfo['team'] . "'>";
        echo "<INPUT TYPE=SUBMIT VALUE=SUBMIT>  <INPUT TYPE=RESET VALUE=RESET>";
        echo "</FORM>";
    }
}

function corp_planet_checkboxes($planet)
{
    if ($planet['corp'] <= 0) {
        return("<INPUT TYPE=CHECKBOX NAME=corp[] VALUE=\"" . $planet["planet_id"] . "\">");
    } elseif ($planet['corp'] > 0) {
        return("<INPUT TYPE=CHECKBOX NAME=corp[] VALUE=\"" . $planet["planet_id"] . "\" CHECKED>");
    }
}

function selling_checkboxes($planet)
{
    if ($planet['sells'] != 'Y') {
        return("<INPUT TYPE=CHECKBOX NAME=sells[] VALUE=\"" . $planet["planet_id"] . "\">");
    } elseif ($planet['sells'] == 'Y') {
        return("<INPUT TYPE=CHECKBOX NAME=sells[] VALUE=\"" . $planet["planet_id"] . "\" CHECKED>");
    }
}

function base_build_check($planet)
{
    global $l_yes, $l_no;
    global $base_ore, $base_organics, $base_goods, $base_credits;

    if ($planet['base'] == 'Y') {
        return("$l_yes");
    } elseif ($planet['ore'] >= $base_ore && $planet['organics'] >= $base_organics && $planet['goods'] >= $base_goods && $planet['credits'] >= $base_credits) {
        return("<A HREF=planet_report_ce.php?buildp=" . $planet["planet_id"] . "&builds=" . $planet["sector_id"] . ">Build</A>");
    } else {
        return("$l_no");
    }
}

echo "<BR><BR>";

include("footer.php");
