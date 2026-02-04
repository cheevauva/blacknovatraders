<?php

include 'config.php';

$title = "Administration";
include("header.php");

bigtitle();

function CHECKED($yesno) {
    return(($yesno == "Y") ? "CHECKED" : "");
}

function YESNO($onoff) {
    return(($onoff == "ON") ? "Y" : "N");
}

$swordfish = fromPost('swordfish');
$module = fromPost('menu', fromPost('module'));

if ($swordfish != $adminpass) {
    include 'tpls/admin/admin_login.tpl.php';
    return;
}

if (empty($module)) {
    $ships = db()->fetchAllKeyValue("SELECT ship_id,character_name FROM ships ORDER BY character_name");
    $sectors = db()->fetchAllKeyValue("SELECT sector_id, sector_id  AS value FROM universe ORDER BY sector_id");
    $planets = db()->fetchAllKeyValue("SELECT planet_id, CONCAT_WS(' in ', name, sector_id) FROM planets ORDER BY sector_id");
    $zones = db()->fetchAllKeyValue('SELECT zone_id,zone_name FROM zones ORDER BY zone_name');
    include 'tpls/admin/welcome.tpl.php';
    return;
}


$button_main = true;

if ($module == "useredit") {
    if (empty($user)) {
        $ships = db()->fetchAllKeyValue("SELECT ship_id,character_name FROM ships ORDER BY character_name");
        include 'tpls/admin/userlist.tpl.php';
    } else {
        if (empty($operation)) {
            $row = shipById($user);
            include 'tpls/admin/useredit.tpl.php';
        } elseif ($operation == "save") {
            // update database
            $_ship_destroyed = empty($ship_destroyed) ? "N" : "Y";
            $_dev_escapepod = empty($dev_escapepod) ? "N" : "Y";
            $_dev_fuelscoop = empty($dev_fuelscoop) ? "N" : "Y";
            $db->Execute("UPDATE ships SET character_name='$character_name',password='$password2',email='$email',ship_name='$ship_name',ship_destroyed='$_ship_destroyed',hull='$hull',engines='$engines',power='$power',computer='$computer',sensors='$sensors',armor='$armor',shields='$shields',beams='$beams',torp_launchers='$torp_launchers',cloak='$cloak',credits='$credits',turns='$turns',dev_warpedit='$dev_warpedit',dev_genesis='$dev_genesis',dev_beacon='$dev_beacon',dev_emerwarp='$dev_emerwarp',dev_escapepod='$_dev_escapepod',dev_fuelscoop='$_dev_fuelscoop',dev_minedeflector='$dev_minedeflector',sector='$sector',ship_ore='$ship_ore',ship_organics='$ship_organics',ship_goods='$ship_goods',ship_energy='$ship_energy',ship_colonists='$ship_colonists',ship_fighters='$ship_fighters',torps='$torps',armor_pts='$armor_pts' WHERE ship_id=$user");
            echo "Changes saved<BR><BR>";
            $button_main = true;
        } else {
            echo "Invalid operation";
        }
    }
} elseif ($module == "univedit") {
    if (empty($action)) {
        include 'tpls/admin/expand_universe.tpl.php';
    } elseif ($action == "doexpand") {
        echo "<BR><FONT SIZE='+2'>Be sure to update your config.php file with the new universe_size value</FONT><BR>";
        srand((double) microtime() * 1000000);
        $result = $db->Execute("SELECT sector_id FROM $dbtables[universe] ORDER BY sector_id ASC");
        while (!$result->EOF) {
            $row = $result->fields;
            $distance = rand(1, $radius);
            $db->Execute("UPDATE $dbtables[universe] SET distance=$distance WHERE sector_id=$row[sector_id]");
            echo "Updated sector $row[sector_id] set to $distance<BR>";
            $result->MoveNext();
        }
    }
} elseif ($module == "sectedit") {
    if (empty($sector)) {
        $sectors = db()->fetchAllKeyValue("SELECT sector_id, sector_id  AS value FROM universe ORDER BY sector_id");
        include 'tpls/admin/sectorlist.tpl.php';
    } else {
        if (empty($operation)) {
            $row = sectoryById($sector);
            $zones = db()->fetchAllKeyValue('SELECT zone_id, zone_name FROM zones ORDER BY zone_name');
            include 'tpls/admin/sectoredit.tpl.php';
        } elseif ($operation == "save") {
            // update database
            $secupdate = $db->Execute("UPDATE $dbtables[universe] SET sector_name='$sector_name',zone_id='$zone_id',beacon='$beacon',port_type='$port_type',port_organics='$port_organics',port_ore='$port_ore',port_goods='$port_goods',port_energy='$port_energy',distance='$distance',angle1='$angle1',angle2='$angle2' WHERE sector_id=$sector");
            if (!$secupdate) {
                echo "Changes to Sector record have FAILED Due to the following Error:<BR><BR>";
                echo $db->ErrorMsg() . "<br>";
            } else {
                echo "Changes to Sector record have been saved.<BR><BR>";
            }
            echo "<INPUT TYPE=SUBMIT VALUE=\"Return to Sector editor\">";
            $button_main = true;
        } else {
            echo "Invalid operation";
        }
    }
} elseif ($module == "planedit") {
    if (empty($planet)) {
        $planets = db()->fetchAllKeyValue("SELECT planet_id, CONCAT_WS(' in ', name, sector_id) FROM planets ORDER BY sector_id");
        include 'tpls/admin/planetlist.tpl.php';
    } else {
        if (empty($operation)) {
            $row = planetById($planet);
            $owners = db()->fetchAllKeyValue("SELECT ship_id,character_name FROM ships ORDER BY character_name");
            include 'tpls/admin/planetedit.tpl.php';
        } elseif ($operation == "save") {
            // update database
            $_defeated = empty($defeated) ? "N" : "Y";
            $_base = empty($base) ? "N" : "Y";
            $sells = empty($sells) ? "N" : "Y";
            $planupdate = $db->Execute("UPDATE $dbtables[planets] SET sector_id='$sector_id',defeated='$_defeated',name='$name',base='$_base',sells='$_sells',owner='$owner',organics='$organics',ore='$ore',goods='$goods',energy='$energy',corp='$corp',colonists='$colonists',credits='$credits',fighters='$fighters',torps='$torps',prod_organics='$prod_organics',prod_ore='$prod_ore',prod_goods='$prod_goods',prod_energy='$prod_energy',prod_fighters='$prod_fighters',prod_torp='$prod_torp' WHERE planet_id=$planet");
            if (!$planupdate) {
                echo "Changes to Planet record have FAILED Due to the following Error:<BR><BR>";
                echo $db->ErrorMsg() . "<br>";
            } else {
                echo "Changes to Planet record have been saved.<BR><BR>";
            }
            $button_main = true;
        } else {
            echo "Invalid operation";
        }
    }
} elseif ($module == "linkedit") {
    echo "<B>Link editor</B>";
} elseif ($module == "zoneedit") {
    if (empty($zone)) {
        $zones = db()->fetchAllKeyValue('SELECT zone_id,zone_name FROM zones ORDER BY zone_name');
        include 'tpls/admin/zonelist.tpl.php';
    } else {
        if ($operation == "editzone") {
            $row = zoneById($zone);
            include 'tpls/admin/zoneedit.tpl.php';
        } elseif ($operation == "savezone") {
            // update database
            $_zone_beacon = empty($zone_beacon) ? "N" : "Y";
            $_zone_attack = empty($zone_attack) ? "N" : "Y";
            $_zone_warpedit = empty($zone_warpedit) ? "N" : "Y";
            $_zone_planet = empty($zone_planet) ? "N" : "Y";
            $db->Execute("UPDATE $dbtables[zones] SET zone_name='$zone_name',allow_beacon='$_zone_beacon' ,allow_attack='$_zone_attack' ,allow_warpedit='$_zone_warpedit' ,allow_planet='$_zone_planet', max_hull='$zone_hull' WHERE zone_id=$zone");
            echo "Changes saved<BR><BR>";
            $button_main = true;
        } else {
            echo "Invalid operation";
        }
    }
} elseif ($module == "logview") {
    $ships = db()->fetchAllKeyValue("SELECT ship_id,character_name FROM ships ORDER BY character_name");
    include 'tpls/admin/logview.tpl.php';
} else {
    echo "Unknown function";
}

include 'tpls/admin/button_main.tpl.php';

include("footer.php");
