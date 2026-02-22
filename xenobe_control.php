<?php

use BNT\Log\LogTypeConstants;

$disableRegisterGlobalFix = true;

include 'config.php';

$title = "Xenobe Control";
include("header.php");

bigtitle();

function CHECKED($yesno)
{
    return(($yesno == "Y") ? "CHECKED" : "");
}

function YESNO($onoff)
{
    return(($onoff == "ON") ? "Y" : "N");
}

$module = fromPOST('menu');
$swordfish = fromPOST('swordfish');

if ($swordfish != $adminpass) {
    echo "<FORM ACTION=xenobe_control.php METHOD=POST>";
    echo "Password: <INPUT TYPE=PASSWORD NAME=swordfish SIZE=20 MAXLENGTH=20><BR><BR>";
    echo "<INPUT TYPE=SUBMIT VALUE=Submit><INPUT TYPE=RESET VALUE=Reset>";
    echo "</FORM>";
} else {
    // ******************************
    // ******** MAIN MENU ***********
    // ******************************
    if (empty($module)) {
        echo "Welcome to the BlackNova Traders Xenobe Control module<BR><BR>";
        echo "Select a function from the list below:<BR>";
        echo "<FORM ACTION=xenobe_control.php METHOD=POST>";
        echo "<SELECT NAME=menu>";
        echo "<OPTION VALUE=instruct>Xenobe Instructions</OPTION>";
        echo "<OPTION VALUE=xenobeedit SELECTED>Xenobe Character Editor</OPTION>";
        echo "<OPTION VALUE=createnew>Create A New Xenobe Character</OPTION>";
        echo "<OPTION VALUE=clearlog>Clear All Xenobe Log Files</OPTION>";
        echo "<OPTION VALUE=dropxenobe>Drop and Re-Install Xenobe Database</OPTION>";
        echo "</SELECT>";
        printf("<INPUT TYPE=HIDDEN NAME=swordfish VALUE=%s>", $swordfish);
        echo "&nbsp;<INPUT TYPE=SUBMIT VALUE=Submit>";
        echo "</FORM>";
    } else {
        $button_main = true;
        // ***********************************************
        // ********* START OF INSTRUCTIONS SUB ***********
        // ***********************************************
        if ($module == "instruct") {
            echo "<H2>Xenobe Instructions</H2>";
            echo "<P>&nbsp;&nbsp;&nbsp; Welcome to the Xenobe Control module.  This is the module that will control the Xenobe players in the game. ";
            echo "It is very simple right now, but will be expanded in future versions. ";
            echo "The ultimate goal of the Xenobe players is to create some interactivity for those games without a large user base. ";
            echo "I need not say that the Xenobe will also make good cannon fodder for those games with a large user base. ";

            echo "<H3>Xenobe Creation</H3>";
            echo "<P>&nbsp;&nbsp;&nbsp; In order to create a Xenobe you must choose the <B>\"Create A Xenobe Character\"</B> option from the menu. ";
            echo "This will bring up the Xenobe character creation screen.  There are only a few fields for you to edit. ";
            echo "However, with these fields you will determine not only how your Xenobe will be created, but how he will act in the game. ";
            echo "We will now go over these fields and what they will do. ";

            echo "<P>&nbsp;&nbsp;&nbsp; When creating a new Xenobe character the <B>Xenobe Name</B> and the <B>Shipname</B> are automatically generated. ";
            echo "You can change these default values by editing these fields before submitting the character for creation. ";
            echo "Take care not to duplicate a current player or ship name, for that will result in creation failure. ";
            echo "<BR>&nbsp;&nbsp;&nbsp; The starting <B>Sector</B> number will also be randomly generated. ";
            echo "You can change this to any sector.  However, you should take care to use a valid sector number. Otherwise the creation will fail.";
            echo "<BR>&nbsp;&nbsp;&nbsp; The <B>Level</B> field will default to '3'.  This field refers to the starting tech level of all ship stats. ";
            echo "So a default Xenobe will have it's Hull, Beams, Power, Engine, etc... all set to 3 unless this value is changed. ";
            echo "All appropriate ship stores will be set to the maximum allowed by the given tech level. ";
            echo "So, starting levels of energy, fighters, armor, torps, etc... are all affected by this setting. ";
            echo "<BR>&nbsp;&nbsp;&nbsp; The <B>Active</B> checkbox will default to checked. ";
            echo "This box refers to if the Xenobe AI system will see this Xenobe and execute it's orders. ";
            echo "If this box is not checked then the Xenobe AI system will ignore this record and the next two fields are ignored. ";
            echo "<BR>&nbsp;&nbsp;&nbsp; The <B>Orders</B> selection box will default to 'SENTINEL'. ";
            echo "There are three other options available: ROAM, ROAM AND TRADE, and ROAM AND HUNT. ";
            echo "These Orders and what they mean will be detailed below. ";
            echo "<BR>&nbsp;&nbsp;&nbsp; The <B>Aggression</B> selection box will default to 'PEACEFUL'. ";
            echo "There are two other options available: ATTACK SOMETIMES, and ATTACK ALWAYS. ";
            echo "These Aggression settings and what they mean will be detailed below. ";
            echo "<BR>&nbsp;&nbsp;&nbsp; Pressing the <B>Create</B> button will create the Xenobe and return to the creation screen to create another. ";

            echo "<H3>Xenobe Orders</H3>";
            echo "<P> Here are the Xenobe Order options and what the Xenobe AI system will do for each: ";
            echo "<UL>SENTINEL<BR> ";
            echo "This Xenobe will stay in place.  His only interactions will be with those who are in his sector at the time he takes his turn. ";
            echo "The aggression level will determine what those player interactions are.</UL> ";
            echo "<UL>ROAM<BR> ";
            echo "This Xenobe will warp from sector to sector looking for players to interact with. ";
            echo "The aggression level will determine what those player interactions are.</UL> ";
            echo "<UL>ROAM AND TRADE<BR> ";
            echo "This Xenobe will warp from sector to sector looking for players to interact with and ports to trade with. ";
            echo "The Xenobe will trade at a port if possible before looking for player interactions. ";
            echo "The aggression level will determine what those player interactions are.</UL> ";
            echo "<UL>ROAM AND HUNT<BR> ";
            echo "This Xenobe has a taste for blood and likes the sport of a good hunt. ";
            echo "Ocassionally (around 1/4th the time) this Xenobe has the urge to go hunting.  He will randomly choose one of the top ten players to hunt. ";
            echo "If that player is in a sector that allows attack, then the Xenobe warps there and attacks. ";
            echo "When he is not out hunting this Xenobe acts just like one with ROAM orders.</UL> ";

            echo "<H3>Xenobe Aggression</H3>";
            echo "<P> Here are the Xenobe Aggression levels and what the Xenobe AI system will do for each: ";
            echo "<UL>PEACEFUL<BR> ";
            echo "This Xenobe will not attack players.  He will continue to roam or trade as ordered but will not launch any attacks. ";
            echo "If this Xenobe is a hunter then he will still attack players on the hunt but never otherwise.</UL> ";
            echo "<UL>ATTACK SOMETIMES<BR> ";
            echo "This Xenobe will compare it's current number of fighters to a players fighters before deciding to attack. ";
            echo "If the Xenobe's fighters are greater then the player's, then the Xenobe will attack the player.</UL> ";
            echo "<UL>ATTACK ALWAYS<BR> ";
            echo "This Xenobe is just mean.  He will attack anyone he comes across regardless of the odds.</UL> ";
        }
        // ***********************************************
        // ********* START OF Xenobe EDIT SUB ***********
        // ***********************************************
        elseif ($module == "xenobeedit") {
            $user = fromPOST('user');
            echo "<span style=\"font-family : courier, monospace; font-size: 12pt; color: #00FF00 \">Xenobe Editor</span><BR>";
            echo "<FORM ACTION=xenobe_control.php METHOD=POST>";
            if (empty($user)) {
                echo "<SELECT SIZE=20 NAME=user>";
                $res = db()->fetchAll("SELECT email, character_name, ship_destroyed, active, sector FROM ships JOIN xenobe WHERE email=xenobe_id ORDER BY sector");
                foreach ($res as $row) {
                    $charnamelist = sprintf("%-20s", $row['character_name']);
                    $charnamelist = str_replace("  ", "&nbsp;&nbsp;", $charnamelist);
                    $sectorlist = sprintf("Sector %'04d&nbsp;&nbsp;", $row['sector']);
                    if ($row['active'] == "Y") {
                        $activelist = "Active &Oslash;&nbsp;&nbsp;";
                    } else {
                        $activelist = "Active O&nbsp;&nbsp;";
                    }
                    if ($row['ship_destroyed'] == "Y") {
                        $destroylist = "Destroyed &Oslash;&nbsp;&nbsp;";
                    } else {
                        $destroylist = "Destroyed O&nbsp;&nbsp;";
                    }
                    printf("<OPTION VALUE=%s>%s %s %s %s</OPTION>", $row['email'], $activelist, $destroylist, $sectorlist, $charnamelist);
                }
                echo "</SELECT>";
                echo "&nbsp;<INPUT TYPE=SUBMIT VALUE=Edit>";
            } else {
                if (empty($operation)) {
                    $row = db()->fetch("SELECT * FROM ships JOIN xenobe WHERE email=xenobe_id AND email= :user", [
                        'user' => $user,
                    ]);
                    echo "<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=5>";
                    printf("<TR><TD>Xenobe name</TD><TD><INPUT TYPE=TEXT NAME=character_name VALUE=\"%s\"></TD></TR>", $row['character_name']);
                    printf("<TR><TD>Active?</TD><TD><INPUT TYPE=CHECKBOX NAME=active VALUE=ON %s></TD></TR>", CHECKED($row['active']));
                    printf("<TR><TD>E-mail</TD><TD>%s</TD></TR>", $row['email']);
                    printf("<TR><TD>ID</TD><TD>%s</TD></TR>", $row['ship_id']);
                    printf("<TR><TD>Ship</TD><TD><INPUT TYPE=TEXT NAME=ship_name VALUE=\"%s\"></TD></TR>", $row['ship_name']);
                    printf("<TR><TD>Destroyed?</TD><TD><INPUT TYPE=CHECKBOX NAME=ship_destroyed VALUE=ON %s></TD></TR>", CHECKED($row['ship_destroyed']));
                    echo "<TR><TD>Orders</TD><TD>";
                    echo "<SELECT SIZE=1 NAME=orders>";
                    $oorder0 = $oorder1 = $oorder2 = $oorder3 = "VALUE";
                    if ($row['orders'] == 0) {
                        $oorder0 = "SELECTED=0 VALUE";
                    }
                    if ($row['orders'] == 1) {
                        $oorder1 = "SELECTED=1 VALUE";
                    }
                    if ($row['orders'] == 2) {
                        $oorder2 = "SELECTED=2 VALUE";
                    }
                    if ($row['orders'] == 3) {
                        $oorder3 = "SELECTED=3 VALUE";
                    }
                    printf("<OPTION %s=0>Sentinel</OPTION>", $oorder0);
                    printf("<OPTION %s=1>Roam</OPTION>", $oorder1);
                    printf("<OPTION %s=2>Roam and Trade</OPTION>", $oorder2);
                    printf("<OPTION %s=3>Roam and Hunt</OPTION>", $oorder3);
                    echo "</SELECT></TD></TR>";
                    echo "<TR><TD>Aggression</TD><TD>";
                    $oaggr0 = $oaggr1 = $oaggr2 = "VALUE";
                    if ($row['aggression'] == 0) {
                        $oaggr0 = "SELECTED=0 VALUE";
                    }
                    if ($row['aggression'] == 1) {
                        $oaggr1 = "SELECTED=1 VALUE";
                    }
                    if ($row['aggression'] == 2) {
                        $oaggr2 = "SELECTED=2 VALUE";
                    }
                    echo "<SELECT SIZE=1 NAME=aggression>";
                    printf("<OPTION %s=0>Peaceful</OPTION>", $oaggr0);
                    printf("<OPTION %s=1>Attack Sometimes</OPTION>", $oaggr1);
                    printf("<OPTION %s=2>Attack Always</OPTION>", $oaggr2);
                    echo "</SELECT></TD></TR>";
                    echo "<TR><TD>Levels</TD>";
                    echo "<TD><TABLE BORDER=0 CELLSPACING=0 CELLPADDING=5>";
                    printf("<TR><TD>Hull</TD><TD><INPUT TYPE=TEXT SIZE=5 NAME=hull VALUE=\"%s\"></TD>", $row['hull']);
                    printf("<TD>Engines</TD><TD><INPUT TYPE=TEXT SIZE=5 NAME=engines VALUE=\"%s\"></TD>", $row['engines']);
                    printf("<TD>Power</TD><TD><INPUT TYPE=TEXT SIZE=5 NAME=power VALUE=\"%s\"></TD>", $row['power']);
                    printf("<TD>Computer</TD><TD><INPUT TYPE=TEXT SIZE=5 NAME=computer VALUE=\"%s\"></TD></TR>", $row['computer']);
                    printf("<TR><TD>Sensors</TD><TD><INPUT TYPE=TEXT SIZE=5 NAME=sensors VALUE=\"%s\"></TD>", $row['sensors']);
                    printf("<TD>Armour</TD><TD><INPUT TYPE=TEXT SIZE=5 NAME=armor VALUE=\"%s\"></TD>", $row['armor']);
                    printf("<TD>Shields</TD><TD><INPUT TYPE=TEXT SIZE=5 NAME=shields VALUE=\"%s\"></TD>", $row['shields']);
                    printf("<TD>Beams</TD><TD><INPUT TYPE=TEXT SIZE=5 NAME=beams VALUE=\"%s\"></TD></TR>", $row['beams']);
                    printf("<TR><TD>Torpedoes</TD><TD><INPUT TYPE=TEXT SIZE=5 NAME=torp_launchers VALUE=\"%s\"></TD>", $row['torp_launchers']);
                    printf("<TD>Cloak</TD><TD><INPUT TYPE=TEXT SIZE=5 NAME=cloak VALUE=\"%s\"></TD></TR>", $row['cloak']);
                    echo "</TABLE></TD></TR>";
                    echo "<TR><TD>Holds</TD>";
                    echo "<TD><TABLE BORDER=0 CELLSPACING=0 CELLPADDING=5>";
                    printf("<TR><TD>Ore</TD><TD><INPUT TYPE=TEXT SIZE=8 NAME=ship_ore VALUE=\"%s\"></TD>", $row['ship_ore']);
                    printf("<TD>Organics</TD><TD><INPUT TYPE=TEXT SIZE=8 NAME=ship_organics VALUE=\"%s\"></TD>", $row['ship_organics']);
                    printf("<TD>Goods</TD><TD><INPUT TYPE=TEXT SIZE=8 NAME=ship_goods VALUE=\"%s\"></TD></TR>", $row['ship_goods']);
                    printf("<TR><TD>Energy</TD><TD><INPUT TYPE=TEXT SIZE=8 NAME=ship_energy VALUE=\"%s\"></TD>", $row['ship_energy']);
                    printf("<TD>Colonists</TD><TD><INPUT TYPE=TEXT SIZE=8 NAME=ship_colonists VALUE=\"%s\"></TD></TR>", $row['ship_colonists']);
                    echo "</TABLE></TD></TR>";
                    echo "<TR><TD>Combat</TD>";
                    echo "<TD><TABLE BORDER=0 CELLSPACING=0 CELLPADDING=5>";
                    printf("<TR><TD>Fighters</TD><TD><INPUT TYPE=TEXT SIZE=8 NAME=ship_fighters VALUE=\"%s\"></TD>", $row['ship_fighters']);
                    printf("<TD>Torpedoes</TD><TD><INPUT TYPE=TEXT SIZE=8 NAME=torps VALUE=\"%s\"></TD></TR>", $row['torps']);
                    printf("<TR><TD>Armour Pts</TD><TD><INPUT TYPE=TEXT SIZE=8 NAME=armor_pts VALUE=\"%s\"></TD></TR>", $row['armor_pts']);
                    echo "</TABLE></TD></TR>";
                    echo "<TR><TD>Devices</TD>";
                    echo "<TD><TABLE BORDER=0 CELLSPACING=0 CELLPADDING=5>";
                    printf("<TR><TD>Beacons</TD><TD><INPUT TYPE=TEXT SIZE=5 NAME=dev_beacon VALUE=\"%s\"></TD>", $row['dev_beacon']);
                    printf("<TD>Warp Editors</TD><TD><INPUT TYPE=TEXT SIZE=5 NAME=dev_warpedit VALUE=\"%s\"></TD>", $row['dev_warpedit']);
                    printf("<TD>Genesis Torpedoes</TD><TD><INPUT TYPE=TEXT SIZE=5 NAME=dev_genesis VALUE=\"%s\"></TD></TR>", $row['dev_genesis']);
                    printf("<TR><TD>Mine Deflectors</TD><TD><INPUT TYPE=TEXT SIZE=5 NAME=dev_minedeflector VALUE=\"%s\"></TD>", $row['dev_minedeflector']);
                    printf("<TD>Emergency Warp</TD><TD><INPUT TYPE=TEXT SIZE=5 NAME=dev_emerwarp VALUE=\"%s\"></TD></TR>", $row['dev_emerwarp']);
                    printf("<TR><TD>Escape Pod</TD><TD><INPUT TYPE=CHECKBOX NAME=dev_escapepod VALUE=ON %s></TD>", CHECKED($row['dev_escapepod']));
                    printf("<TD>FuelScoop</TD><TD><INPUT TYPE=CHECKBOX NAME=dev_fuelscoop VALUE=ON %s></TD></TR>", CHECKED($row['dev_fuelscoop']));
                    echo "</TABLE></TD></TR>";
                    printf("<TR><TD>Credits</TD><TD><INPUT TYPE=TEXT NAME=credits VALUE=\"%s\"></TD></TR>", $row['credits']);
                    printf("<TR><TD>Turns</TD><TD><INPUT TYPE=TEXT NAME=turns VALUE=\"%s\"></TD></TR>", $row['turns']);
                    printf("<TR><TD>Current sector</TD><TD><INPUT TYPE=TEXT NAME=sector VALUE=\"%s\"></TD></TR>", $row['sector']);
                    echo "</TABLE>";
                    echo "<BR>";
                    printf("<INPUT TYPE=HIDDEN NAME=user VALUE=%s>", $user);
                    echo "<INPUT TYPE=HIDDEN NAME=operation VALUE=save>";
                    echo "<INPUT TYPE=SUBMIT VALUE=Save>";
                    //******************************
                    //*** SHOW Xenobe LOG DATA ***
                    //******************************
                    echo "<HR>";
                    echo "<span style=\"font-family : courier, monospace; font-size: 12pt; color: #00FF00;\">Log Data For This Xenobe</span><BR>";

                    $logres = db()->fetchAll("SELECT * FROM logs WHERE ship_id= :shipId ORDER BY time DESC, type DESC", [
                        'shipId' => $row['ship_id'],
                    ]);
                    foreach ($logres as $logrow) {
                        $logtype = "";
                        switch ($logrow['type']) {
                            case LogTypeConstants::LOG_Xenobe_ATTACK:
                                $logtype = "Launching an attack on ";
                                break;
                            case LogTypeConstants::LOG_ATTACK_LOSE:
                                $logtype = "We were attacked and lost against ";
                                break;
                            case LogTypeConstants::LOG_ATTACK_WIN:
                                $logtype = "We were attacked and won against ";
                                break;
                        }
                        $logdatetime = substr($logrow['time'], 4, 2) . "/" . substr($logrow['time'], 6, 2) . "/" . substr($logrow['time'], 0, 4) . " " . substr($logrow['time'], 8, 2) . ":" . substr($logrow['time'], 10, 2) . ":" . substr($logrow['time'], 12, 2);
                        printf("%s %s%s <BR>", $logdatetime, $logtype, $logrow['data']);
                    }
                } elseif ($operation == "save") {
                    $character_name = fromPOST('character_name');
                    $ship_name = fromPOST('ship_name');
                    $ship_destroyed = fromPOST('ship_destroyed');
                    $hull = fromPOST('hull');
                    $engines = fromPOST('engines');
                    $power = fromPOST('power');
                    $computer = fromPOST('computer');
                    $sensors = fromPOST('sensors');
                    $armor = fromPOST('armor');
                    $shields = fromPOST('shields');
                    $beams = fromPOST('beams');
                    $torp_launchers = fromPOST('torp_launchers');
                    $cloak = fromPOST('cloak');
                    $credits = fromPOST('credits');
                    $turns = fromPOST('turns');
                    $dev_warpedit = fromPOST('dev_warpedit');
                    $dev_genesis = fromPOST('dev_genesis');
                    $dev_beacon = fromPOST('dev_beacon');
                    $dev_emerwarp = fromPOST('dev_emerwarp');
                    $dev_escapepod = fromPOST('dev_escapepod');
                    $dev_fuelscoop = fromPOST('dev_fuelscoop');
                    $dev_minedeflector = fromPOST('dev_minedeflector');
                    $sector = fromPOST('sector');
                    $ship_ore = fromPOST('ship_ore');
                    $ship_organics = fromPOST('ship_organics');
                    $ship_goods = fromPOST('ship_goods');
                    $ship_energy = fromPOST('ship_energy');
                    $ship_colonists = fromPOST('ship_colonists');
                    $ship_fighters = fromPOST('ship_fighters');
                    $torps = fromPOST('torps');
                    $armor_pts = fromPOST('armor_pts');
                    $active = fromPOST('active');

                    // update database
                    $_ship_destroyed = empty($ship_destroyed) ? "N" : "Y";
                    $_dev_escapepod = empty($dev_escapepod) ? "N" : "Y";
                    $_dev_fuelscoop = empty($dev_fuelscoop) ? "N" : "Y";
                    $_active = empty($active) ? "N" : "Y";
                    db()->q("UPDATE ships SET character_name= :character_name, ship_name= :ship_name, ship_destroyed= :ship_destroyed, hull= :hull, engines= :engines, power= :power, computer= :computer, sensors= :sensors, armor= :armor, shields= :shields, beams= :beams, torp_launchers= :torp_launchers, cloak= :cloak, credits= :credits, turns= :turns, dev_warpedit= :dev_warpedit, dev_genesis= :dev_genesis, dev_beacon= :dev_beacon, dev_emerwarp= :dev_emerwarp, dev_escapepod= :dev_escapepod, dev_fuelscoop= :dev_fuelscoop, dev_minedeflector= :dev_minedeflector, sector= :sector, ship_ore= :ship_ore, ship_organics= :ship_organics, ship_goods= :ship_goods, ship_energy= :ship_energy, ship_colonists= :ship_colonists, ship_fighters= :ship_fighters, torps= :torps, armor_pts= :armor_pts WHERE email= :user", [
                        'character_name' => $character_name,
                        'ship_name' => $ship_name,
                        'ship_destroyed' => $_ship_destroyed,
                        'hull' => $hull,
                        'engines' => $engines,
                        'power' => $power,
                        'computer' => $computer,
                        'sensors' => $sensors,
                        'armor' => $armor,
                        'shields' => $shields,
                        'beams' => $beams,
                        'torp_launchers' => $torp_launchers,
                        'cloak' => $cloak,
                        'credits' => $credits,
                        'turns' => $turns,
                        'dev_warpedit' => $dev_warpedit,
                        'dev_genesis' => $dev_genesis,
                        'dev_beacon' => $dev_beacon,
                        'dev_emerwarp' => $dev_emerwarp,
                        'dev_escapepod' => $_dev_escapepod,
                        'dev_fuelscoop' => $_dev_fuelscoop,
                        'dev_minedeflector' => $dev_minedeflector,
                        'sector' => $sector,
                        'ship_ore' => $ship_ore,
                        'ship_organics' => $ship_organics,
                        'ship_goods' => $ship_goods,
                        'ship_energy' => $ship_energy,
                        'ship_colonists' => $ship_colonists,
                        'ship_fighters' => $ship_fighters,
                        'torps' => $torps,
                        'armor_pts' => $armor_pts,
                        'user' => $user,
                    ]);
                    if (db()->ErrorMsg()) {
                        echo "Changes to Xenobe ship record have FAILED Due to the following Error:<BR><BR>";
                        echo db()->ErrorMsg() . "<br>";
                    } else {
                        echo "Changes to Xenobe ship record have been saved.<BR><BR>";
                        db()->q("UPDATE xenobe SET active= :active, orders= :orders, aggression= :aggression WHERE xenobe_id= :user", [
                            'active' => $_active,
                            'orders' => fromPOST('orders'),
                            'aggression' => fromPOST('aggression'),
                            'user' => $user,
                        ]);
                        if (db()->ErrorMsg()) {
                            echo "Changes to Xenobe activity record have FAILED Due to the following Error:<BR><BR>";
                            echo db()->ErrorMsg() . "<br>";
                        } else {
                            echo "Changes to Xenobe activity record have been saved.<BR><BR>";
                        }
                    }
                    echo "<INPUT TYPE=SUBMIT VALUE=\"Return to Xenobe editor\">";
                    $button_main = false;
                } else {
                    echo "Invalid operation";
                }
            }
            printf("<INPUT TYPE=HIDDEN NAME=menu VALUE=xenobeedit>");
            printf("<INPUT TYPE=HIDDEN NAME=swordfish VALUE=%s>", $swordfish);
            echo "</FORM>";
        }
        // ***********************************************
        // ******** START OF DROP Xenobe SUB ***********
        // ***********************************************
        elseif ($module == "dropxenobe") {
            echo "<H1>Drop and Re-Install Xenobe Database</H1>";
            echo "<H3>This will DELETE All Xenobe records from the <i>ships</i> TABLE then DROP and reset the <i>xenobe</i> TABLE</H3>";
            echo "<FORM ACTION=xenobe_control.php METHOD=POST>";
            if (empty($operation)) {
                echo "<BR>";
                echo "<H2><FONT COLOR=Red>Are You Sure?</FONT></H2><BR>";
                echo "<INPUT TYPE=HIDDEN NAME=operation VALUE=dropxen>";
                echo "<INPUT TYPE=SUBMIT VALUE=Drop>";
            } elseif ($operation == "dropxen") {
                // Delete all xenobe in the ships table
                echo "Deleting xenobe records in the ships table...<BR>";
                db()->q("DELETE FROM ships WHERE email LIKE '%@xenobe'");
                echo "deleted.<BR>";
                // Drop xenobe table
                echo "Dropping xenobe table...<BR>";
                db()->q("DROP TABLE IF EXISTS xenobe");
                echo "dropped.<BR>";
                // Create xenobe table
                echo "Re-Creating table: xenobe...<BR>";
                db()->q("CREATE TABLE xenobe( xenobe_id char(40) NOT NULL, active enum('Y','N') DEFAULT 'Y' NOT NULL, aggression smallint(5) DEFAULT '0' NOT NULL, orders smallint(5) DEFAULT '0' NOT NULL, PRIMARY KEY (xenobe_id), KEY xenobe_id (xenobe_id) )");
                echo "created.<BR>";
            } else {
                echo "Invalid operation";
            }
            printf("<INPUT TYPE=HIDDEN NAME=menu VALUE=dropxenobe>");
            printf("<INPUT TYPE=HIDDEN NAME=swordfish VALUE=%s>", $swordfish);
            echo "</FORM>";
        }
        // ***********************************************
        // ***** START OF CLEAR Xenobe LOG SUB *********
        // ***********************************************
        elseif ($module == "clearlog") {
            echo "<H1>Clear All Xenobe Logs</H1>";
            echo "<H3>This will DELETE All Xenobe log files</H3>";
            echo "<FORM ACTION=xenobe_control.php METHOD=POST>";
            if (empty($operation)) {
                echo "<BR>";
                echo "<H2><FONT COLOR=Red>Are You Sure?</FONT></H2><BR>";
                echo "<INPUT TYPE=HIDDEN NAME=operation VALUE=clearxenlog>";
                echo "<INPUT TYPE=SUBMIT VALUE=Clear>";
            } elseif ($operation == "clearxenlog") {
                $res = db()->fetchAll("SELECT email, ship_id FROM ships WHERE email LIKE '%@xenobe'");
                foreach ($res as $row) {
                    db()->q("DELETE FROM logs WHERE ship_id= :shipId", [
                        'shipId' => $row['ship_id'],
                    ]);
                    printf("Log for ship_id %s cleared.<BR>", $row['ship_id']);
                }
            } else {
                echo "Invalid operation";
            }
            printf("<INPUT TYPE=HIDDEN NAME=menu VALUE=clearlog>");
            printf("<INPUT TYPE=HIDDEN NAME=swordfish VALUE=%s>", $swordfish);
            echo "</FORM>";
        }
        // ***********************************************
        // ******** START OF CREATE Xenobe SUB **********
        // ***********************************************
        elseif ($module == "createnew") {
            echo "<B>Create A New Xenobe</B>";
            echo "<BR>";
            echo "<FORM ACTION=xenobe_control.php METHOD=POST>";
            if (empty($operation)) {
                // Create Xenobe Name
                $Sylable1 = array("Ak", "Al", "Ar", "B", "Br", "D", "F", "Fr", "G", "Gr", "K", "Kr", "N", "Ol", "Om", "P", "Qu", "R", "S", "Z");
                $Sylable2 = array("a", "ar", "aka", "aza", "e", "el", "i", "in", "int", "ili", "ish", "ido", "ir", "o", "oi", "or", "os", "ov", "u", "un");
                $Sylable3 = array("ag", "al", "ak", "ba", "dar", "g", "ga", "k", "ka", "kar", "kil", "l", "n", "nt", "ol", "r", "s", "ta", "til", "x");
                $sy1roll = rand(0, 19);
                $sy2roll = rand(0, 19);
                $sy3roll = rand(0, 19);
                $character = $Sylable1[$sy1roll] . $Sylable2[$sy2roll] . $Sylable3[$sy3roll];

                $resultnm = db()->fetch("select character_name from ships where character_name= :character", [
                    'character' => $character,
                ]);
                $namecheck = $resultnm;

                $nametry = 1;
                // If Name Exists Try Again - Up To Nine Times
                while (($namecheck['character_name']) and ($nametry <= 9)) {
                    $sy1roll = rand(0, 19);
                    $sy2roll = rand(0, 19);
                    $sy3roll = rand(0, 19);
                    $character = $Sylable1[$sy1roll] . $Sylable2[$sy2roll] . $Sylable3[$sy3roll];
                    $resultnm = db()->fetch("select character_name from ships where character_name= :character", [
                        'character' => $character,
                    ]);
                    $namecheck = $resultnm;
                    $nametry++;
                }
                // Create Ship Name
                $shipname = "Xenobe-" . $character;
                // Select Random Sector
                $sector = rand(1, $sector_max);
                // Display Confirmation Form
                echo "<TD><TABLE BORDER=0 CELLSPACING=0 CELLPADDING=5>";
                printf("<TR><TD>Xenobe Name</TD><TD><INPUT TYPE=TEXT SIZE=20 NAME=character VALUE=%s></TD>", $character);
                printf("<TD>Level <INPUT TYPE=TEXT SIZE=5 NAME=xenlevel VALUE=3></TD>");
                printf("<TD>Ship Name <INPUT TYPE=TEXT SIZE=20 NAME=shipname VALUE=%s></TD>", $shipname);
                echo "<TR><TD>Active?<INPUT TYPE=CHECKBOX NAME=active VALUE=ON CHECKED ></TD>";
                echo "<TD>Orders ";
                echo "<SELECT SIZE=1 NAME=orders>";
                echo "<OPTION SELECTED=0 VALUE=0>Sentinel</OPTION>";
                echo "<OPTION VALUE=1>Roam</OPTION>";
                echo "<OPTION VALUE=2>Roam and Trade</OPTION>";
                echo "<OPTION VALUE=3>Roam and Hunt</OPTION>";
                echo "</SELECT></TD>";
                printf("<TD>Sector <INPUT TYPE=TEXT SIZE=5 NAME=sector VALUE=%s></TD>", $sector);
                echo "<TD>Aggression ";
                echo "<SELECT SIZE=1 NAME=aggression>";
                echo "<OPTION SELECTED=0 VALUE=0>Peaceful</OPTION>";
                echo "<OPTION VALUE=1>Attack Sometimes</OPTION>";
                echo "<OPTION VALUE=2>Attack Always</OPTION>";
                echo "</SELECT></TD></TR>";
                echo "</TABLE>";
                echo "<HR>";
                echo "<INPUT TYPE=HIDDEN NAME=operation VALUE=createxenobe>";
                echo "<INPUT TYPE=SUBMIT VALUE=Create>";
            } elseif ($operation == "createxenobe") {
                $shipname = fromPOST('shipname');
                $character = fromPOST('character');
                $makepass = fromPOST('makepass');
                $emailname = fromPOST('emailname');
                $xenlevel = fromPOST('xenlevel');
                $maxtorps = fromPOST('maxtorps');
                $maxarmor = fromPOST('maxarmor');
                $sector = fromPOST('sector');
                $maxenergy = fromPOST('maxenergy');
                $maxfighters = fromPOST('maxfighters');
                $aggression = fromPOST('aggression');
                $orders = fromPOST('orders');
                $active = fromPOST('active');

                // update database
                $_active = empty($active) ? "N" : "Y";
                $errflag = 0;
                if ($character == '' || $shipname == '') {
                    echo "Ship name, and character name may not be blank.<BR>";
                    $errflag = 1;
                }
                // Change Spaces to Underscores in shipname
                $shipname = str_replace(" ", "_", $shipname);
                // Create emailname from character
                $emailname = str_replace(" ", "_", $character) . "@xenobe";

                $result = db()->fetchAll("select email, character_name, ship_name from ships where email= :email OR character_name= :character OR ship_name= :shipname", [
                    'email' => $emailname,
                    'character' => $character,
                    'shipname' => $shipname,
                ]);
                if (count($result) > 0) {
                    foreach ($result as $row) {
                        if ($row['email'] == $emailname) {
                            printf("ERROR: E-mail address %s, is already in use.  ", $emailname);
                            $errflag = 1;
                        }
                        if ($row['character_name'] == $character) {
                            printf("ERROR: Character name %s, is already in use.<BR>", $character);
                            $errflag = 1;
                        }
                        if ($row['ship_name'] == $shipname) {
                            printf("ERROR: Ship name %s, is already in use.<BR>", $shipname);
                            $errflag = 1;
                        }
                    }
                }

                if ($errflag == 0) {
                    $makepass = "";
                    $syllables = "er,in,tia,wol,fe,pre,vet,jo,nes,al,len,son,cha,ir,ler,bo,ok,tio,nar,sim,ple,bla,ten,toe,cho,co,lat,spe,ak,er,po,co,lor,pen,cil,li,ght,wh,at,the,he,ck,is,mam,bo,no,fi,ve,any,way,pol,iti,cs,ra,dio,sou,rce,sea,rch,pa,per,com,bo,sp,eak,st,fi,rst,gr,oup,boy,ea,gle,tr,ail,bi,ble,brb,pri,dee,kay,en,be,se";
                    $syllable_array = explode(",", $syllables);
                    srand(microtime() * 1000000);
                    for ($count = 1; $count <= 4; $count++) {
                        if (rand() % 10 == 1) {
                            $makepass .= sprintf("%0.0f", (rand() % 50) + 1);
                        } else {
                            $makepass .= sprintf("%s", $syllable_array[rand() % 62] ?? '');
                        }
                    }
                    $xenlevel = fromPOST('xenlevel', 0);
                    $maxenergy = NUM_ENERGY($xenlevel);
                    $maxarmor = NUM_ARMOUR($xenlevel);
                    $maxfighters = NUM_FIGHTERS($xenlevel);
                    $maxtorps = NUM_TORPEDOES($xenlevel);
                    $stamp = date("Y-m-d H:i:s");
                    // *****************************************************************************
                    // *** ADD Xenobe RECORD TO ships TABLE ... MODIFY IF ships SCHEMA CHANGES ***
                    // *****************************************************************************
                    $thesql = "INSERT INTO ships ( `ship_id` , `ship_name` , `ship_destroyed` , `character_name` , `password` , `email` , `hull` , `engines` , `power` , `computer` , `sensors` , `beams` , `torp_launchers` , `torps` , `shields` , `armor` , `armor_pts` , `cloak` , `credits` , `sector` , `ship_ore` , `ship_organics` , `ship_goods` , `ship_energy` , `ship_colonists` , `ship_fighters` , `ship_damage` , `turns` , `on_planet` , `dev_warpedit` , `dev_genesis` , `dev_beacon` , `dev_emerwarp` , `dev_escapepod` , `dev_fuelscoop` , `dev_minedeflector` , `turns_used` , `rating` , `score` , `team` , `team_invite` , `interface` , `token` , `planet_id` , `preset1` , `preset2` , `preset3` , `trade_colonists` , `trade_fighters` , `trade_torps` , `trade_energy` , `cleared_defences` , `lang` , `dhtml` , `dev_lssd` )
                                    VALUES (NULL, :shipname, 'N', :character, :makepass, :emailname, :xenlevel, :xenlevel, :xenlevel, :xenlevel, :xenlevel, :xenlevel, :xenlevel, :maxtorps, :xenlevel, :xenlevel, :maxarmor, :xenlevel, :start_credits, :sector, 0,0,0, :maxenergy, 0, :maxfighters, 0, :start_turns, 'N', 0,0,0,0, 'N', 'N', 0,0, 0,0,0,0, 'N', NULL, 0,0,0,0, 'Y', 'N', 'N', 'Y', NULL, :default_lang, 'N', 'Y')";

                    $result2 = db()->q($thesql, [
                        'shipname' => $shipname,
                        'character' => $character,
                        'makepass' => $makepass,
                        'emailname' => $emailname,
                        'xenlevel' => $xenlevel,
                        'maxtorps' => $maxtorps,
                        'maxarmor' => $maxarmor,
                        'start_credits' => $start_credits,
                        'sector' => $sector,
                        'maxenergy' => $maxenergy,
                        'maxfighters' => $maxfighters,
                        'start_turns' => $start_turns,
                        'default_lang' => $default_lang,
                    ]);

                    if (db()->ErrorMsg()) {
                        echo db()->ErrorMsg() . "<br>";
                    } else {
                        echo "Xenobe has been created.<BR><BR>";
                        echo "Password has been set.<BR><BR>";
                        echo "Ship Records have been updated.<BR><BR>";
                    }
                    db()->q("INSERT INTO xenobe (xenobe_id, active, aggression, orders) VALUES( :emailname, :active, :aggression, :orders)", [
                        'emailname' => $emailname,
                        'active' => $_active,
                        'aggression' => $aggression,
                        'orders' => $orders,
                    ]);
                    if (db()->ErrorMsg()) {
                        echo db()->ErrorMsg() . "<br>";
                    } else {
                        echo "Xenobe Records have been updated.<BR><BR>";
                    }
                }
                echo "<INPUT TYPE=SUBMIT VALUE=\"Return to Xenobe Creator \">";
                $button_main = false;
            } else {
                echo "Invalid operation";
            }
            printf("<INPUT TYPE=HIDDEN NAME=menu VALUE=createnew>");
            printf("<INPUT TYPE=HIDDEN NAME=swordfish VALUE=%s>", $swordfish);
            echo "</FORM>";
        } else {
            echo "Unknown function";
        }

        if ($button_main) {
            echo "<BR><BR>";
            echo "<FORM ACTION=xenobe_control.php METHOD=POST>";
            printf("<INPUT TYPE=HIDDEN NAME=swordfish VALUE=%s>", $swordfish);
            echo "<INPUT TYPE=SUBMIT VALUE=\"Return to main menu\">";
            echo "</FORM>";
        }
    }
}

include("footer.php");
