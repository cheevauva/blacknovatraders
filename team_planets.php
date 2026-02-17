<?php

include 'config.php';

$title = $l_teamplanet_title;
include("header.php");

if (checkship()) {
    die();
}



if ($playerinfo['team'] == 0) {
    echo "<BR>$l_teamplanet_notally";
    echo "<BR><BR>";

    include("footer.php");
    return;
}

$query = "SELECT * FROM planets WHERE corp= :team";
if (!empty($sort)) {
    $query .= " ORDER BY";
    if ($sort == "name") {
        $query .= " $sort ASC";
    } elseif (
    $sort == "organics" || $sort == "ore" || $sort == "goods" || $sort == "energy" ||
    $sort == "colonists" || $sort == "credits" || $sort == "fighters"
    ) {
        $query .= " $sort DESC";
    } elseif ($sort == "torp") {
        $query .= " torps DESC";
    } else {
        $query .= " sector_id ASC";
    }
}

$planets = db()->fetchAll($query, [
    'team' => $playerinfo['team']
]);

bigtitle();

echo "<BR>";
echo "<strong><A HREF=planet_report.php>$l_teamplanet_personal</A></strong>";
echo "<BR>";
echo "<BR>";

$num_planets = count($planets);
if ($num_planets < 1) {
    echo "<BR>$l_teamplanet_noplanet";
} else {
    echo "$l_pr_clicktosort<BR><BR>";
    echo "<TABLE class=\"table\">";
    echo "<TR>";
    echo "<TH><A HREF=team_planets.php?sort=sector>$l_sector</A></TH>";
    echo "<TH><A HREF=team_planets.php?sort=name>$l_name</A></TH>";
    echo "<TH><A HREF=team_planets.php?sort=ore>$l_ore</A></TH>";
    echo "<TH><A HREF=team_planets.php?sort=organics>$l_organics</A></TH>";
    echo "<TH><A HREF=team_planets.php?sort=goods>$l_goods</A></TH>";
    echo "<TH><A HREF=team_planets.php?sort=energy>$l_energy</A></TH>";
    echo "<TH><A HREF=team_planets.php?sort=colonists>$l_colonists</A></TH>";
    echo "<TH><A HREF=team_planets.php?sort=credits>$l_credits</A></TH>";
    echo "<TH><A HREF=team_planets.php?sort=fighters>$l_fighters</A></TH>";
    echo "<TH><A HREF=team_planets.php?sort=torp>$l_torps</A></TH>";
    echo "<TH>$l_base?</TH><TH>$l_selling?</TH>";
    echo "<TH>Player</TH>";
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
        if ($planet['sells'] == "Y") {
            $total_selling += 1;
        }

        if (empty($planet['name'])) {
            $planet['name'] = "$l_unnamed";
        }

        $owner = $planet['owner'];
        $player = db()->fetch("SELECT character_name FROM ships WHERE ship_id= :owner", [
            'owner' => $owner
        ]);

        echo "<TR>";
        echo "<TD><A HREF=rsmove.php?engage=1&destination=" . $planet['sector_id'] . ">" . $planet['sector_id'] . "</A></TD>";
        echo "<TD>" . $planet['name'] . "</TD>";
        echo "<TD>" . NUMBER($planet['ore']) . "</TD>";
        echo "<TD>" . NUMBER($planet['organics']) . "</TD>";
        echo "<TD>" . NUMBER($planet['goods']) . "</TD>";
        echo "<TD>" . NUMBER($planet['energy']) . "</TD>";
        echo "<TD>" . NUMBER($planet['colonists']) . "</TD>";
        echo "<TD>" . NUMBER($planet['credits']) . "</TD>";
        echo "<TD>" . NUMBER($planet['fighters']) . "</TD>";
        echo "<TD>" . NUMBER($planet['torps']) . "</TD>";
        echo "<TD>" . ($planet['base'] == 'Y' ? "$l_yes" : "$l_no") . "</TD>";
        echo "<TD>" . ($planet['sells'] == 'Y' ? "$l_yes" : "$l_no") . "</TD>";
        echo "<TD>" . $player['character_name'] . "</TD>";
        echo "</TR>";
    }

    echo "<TR>";
    echo "<TD></TD>";
    echo "<TD>$l_pr_totals</TD>";
    echo "<TD>" . NUMBER($total_ore) . "</TD>";
    echo "<TD>" . NUMBER($total_organics) . "</TD>";
    echo "<TD>" . NUMBER($total_goods) . "</TD>";
    echo "<TD>" . NUMBER($total_energy) . "</TD>";
    echo "<TD>" . NUMBER($total_colonists) . "</TD>";
    echo "<TD>" . NUMBER($total_credits) . "</TD>";
    echo "<TD>" . NUMBER($total_fighters) . "</TD>";
    echo "<TD>" . NUMBER($total_torp) . "</TD>";
    echo "<TD>" . NUMBER($total_base) . "</TD>";
    echo "<TD>" . NUMBER($total_selling) . "</TD>";
    echo "<TD></TD>";
    echo "</TR>";
    echo "</TABLE>";
}

echo "<BR><BR>";

include("footer.php");
