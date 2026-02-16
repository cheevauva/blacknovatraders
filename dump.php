<?php

include 'config.php';

$title = $l_dump_title;
include("header.php");

if (checklogin()) {
    die();
}


$sectorinfo = db()->fetch("SELECT * FROM universe WHERE sector_id= :sector", [
    'sector' => $playerinfo['sector']
]);

bigtitle();

if ($playerinfo['turns'] < 1) {
    echo "$l_dump_turn<BR><BR>";
    
    include("footer.php");
    die();
}

if ($playerinfo['ship_colonists'] == 0) {
    echo "$l_dump_nocol<BR><BR>";
} elseif ($sectorinfo['port_type'] == "special") {
    db()->q("UPDATE ships SET ship_colonists=0, turns=turns-1, turns_used=turns_used+1 WHERE ship_id= :ship_id", [
        'ship_id' => $playerinfo['ship_id']
    ]);
    echo "$l_dump_dumped<BR><BR>";
} else {
    echo "$l_dump_nono<BR><BR>";
}

include("footer.php");
