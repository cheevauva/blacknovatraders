<?php

include 'config.php';

$title = $l_ewd_title;
include("header.php");

if (checkship()) {
    die();
}

srand((double)microtime() * 1000000);
bigtitle();

if ($playerinfo['dev_emerwarp'] > 0) {
    $dest_sector = rand(0, $sector_max);
    
    db()->q("UPDATE ships SET sector= :sector, dev_emerwarp=dev_emerwarp-1 WHERE ship_id= :ship_id", [
        'sector' => $dest_sector,
        'ship_id' => $playerinfo['ship_id']
    ]);
    
    log_move($playerinfo['ship_id'], $dest_sector);
    
    $l_ewd_used = str_replace("[sector]", $dest_sector, $l_ewd_used);
    echo "$l_ewd_used<BR><BR>";
} else {
    echo "$l_ewd_none<BR><BR>";
}

include("footer.php");
