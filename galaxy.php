<?php

include 'config.php';

$title = $l_map_title;
include("header.php");

if (checkship()) {
    die();
}


$explored_sectors = db()->fetchAll("SELECT DISTINCT movement_log.sector_id, universe.port_type 
                                   FROM movement_log, universe 
                                   WHERE ship_id = :ship_id 
                                   AND movement_log.sector_id = universe.sector_id 
                                   ORDER BY sector_id ASC", [
    'ship_id' => $playerinfo['ship_id']
]);

bigtitle();

$tile = [
    'special' => "space261_md_blk.gif",
    'ore' => "space262_md_blk.gif",
    'organics' => "space263_md_blk.gif",
    'energy' => "space264_md_blk.gif",
    'goods' => "space265_md_blk.gif",
    'none' => "space.gif",
    'unknown' => "uspace.gif"
];

$cur_sector = 0;
$cur_index = 0;

echo "cursector = $cur_sector max = $sector_max";
echo "<TABLE class='galaxy-map'>\n";

// Create an associative array for quick lookup of explored sectors
$explored_map = [];
foreach ($explored_sectors as $sector) {
    $explored_map[$sector['sector_id']] = $sector['port_type'];
}

while ($cur_sector < $sector_max) {
    $break = ($cur_sector + 1) % 50;

    if ($break == 1) {
        echo "<TR><TD>$cur_sector</TD>";
    }

    if (isset($explored_map[$cur_sector])) {
        $port = $explored_map[$cur_sector];
        $alt = "$cur_sector - $port";
    } else {
        $port = "unknown";
        $alt = "$cur_sector - unknown";
    }

    echo "<TD><A HREF=rsmove.php?engage=1&destination=$cur_sector><img src=images/" . $tile[$port] . " alt=\"$alt\"></A></TD>";

    if ($break == 0) {
        echo "<TD>$cur_sector</TD></TR>\n";
    }

    $cur_sector++;
}

echo "</TABLE>\n";

echo "<BR><BR>";
echo "<div class='legend'>";
echo "<img src=images/" . $tile['special'] . "> - Special Port<BR>\n";
echo "<img src=images/" . $tile['ore'] . "> - Ore Port<BR>\n";
echo "<img src=images/" . $tile['organics'] . "> - Organics Port<BR>\n";
echo "<img src=images/" . $tile['energy'] . "> - Energy Port<BR>\n";
echo "<img src=images/" . $tile['goods'] . "> - Goods Port<BR>\n";
echo "<img src=images/" . $tile['none'] . "> - No Port<BR><BR>\n";
echo "<img src=images/" . $tile['unknown'] . "> - Unexplored<BR><BR>\n";
echo "</div>";
echo "Click <a href=" . route('main') . ">here</a> to return to main menu.";

include("footer.php");
