<?php

include("config.php");
include("languages/$lang");

$title = "$l_log_titlet";
include("header.php");

connectdb();

if (checklogin()) {
    die();
}
if (fromRequest('swordfish') == $adminpass) { //check if called by admin script
    $player = fromGet('player', 0);
    
    if ($player) {
        $playerinfo = shipById($player);
    }
}

$mode = 'compat';

$yres = 558;

echo str_replace("[player]", "$playerinfo[character_name]", $l_log_log);


if (empty($startdate))
    $startdate = date("Y-m-d");


$res = $db->Execute("SELECT * FROM $dbtables[logs] WHERE ship_id=$playerinfo[ship_id] AND time LIKE '$startdate%' ORDER BY time DESC, type DESC");
//echo "SELECT * FROM $dbtables[logs] WHERE ship_id=$playerinfo[ship_id] AND time LIKE '$startdate%' ORDER BY time DESC, type DESC";
while (!$res->EOF) {
    $logs[] = $res->fields;
    $res->MoveNext();
}

$entry = $l_log_months[substr($startdate, 6, 2) - 1] . " " . substr($startdate, 8, 2) . " " . substr($startdate, 0, 4);

echo '<table class="table table-hover">';

if (!empty($logs)) {
    foreach ($logs as $log) {
        $event = log_parse($log);
        $time = $l_log_months[substr($log[time], 6, 2) - 1] . " " . substr($log[time], 8, 2) . " " . substr($log[time], 0, 4) . " " . substr($log[time], 11);

        echo "<tr><td>", $event['title'], '</td><td>', $time, '</td><td>' . $event['text'] . '</td></tr>';
    }
}

echo "</table>";

$month = substr($startdate, 6, 2);
$day = substr($startdate, 8, 2) - 1;
$year = substr($startdate, 0, 4);

$yesterday = mktime(0, 0, 0, $month, $day, $year);
$yesterday = date("Y-m-d", $yesterday);

$day = substr($startdate, 8, 2) - 2;

$yesterday2 = mktime(0, 0, 0, $month, $day, $year);
$yesterday2 = date("Y-m-d", $yesterday2);

$entry = $l_log_months[substr($yesterday, 6, 2) - 1] . " " . substr($yesterday, 8, 2) . " " . substr($yesterday, 0, 4);

unset($logs);
$res = $db->Execute("SELECT * FROM $dbtables[logs] WHERE ship_id=$playerinfo[ship_id] AND time LIKE '$yesterday%' ORDER BY time DESC, type DESC");
while (!$res->EOF) {
    $logs[] = $res->fields;
    $res->MoveNext();
}

echo '<table class="table table-hover">';

if (!empty($logs)) {
    foreach ($logs as $log) {
        $event = log_parse($log);
        $time = $l_log_months[substr($log[time], 6, 2) - 1] . " " . substr($log[time], 8, 2) . " " . substr($log[time], 0, 4) . " " . substr($log[time], 11);

        echo "<tr><td>", $event['title'], '</td><td>', $time, '</td><td>' . $event['text'] . '</td></tr>';
    }
}

echo "</table>";

$entry = $l_log_months[substr($yesterday2, 6, 2) - 1] . " " . substr($yesterday2, 8, 2) . " " . substr($yesterday2, 0, 4);

unset($logs);
$res = $db->Execute("SELECT * FROM $dbtables[logs] WHERE ship_id=$playerinfo[ship_id] AND time LIKE '$yesterday2%' ORDER BY time DESC, type DESC");
while (!$res->EOF) {
    $logs[] = $res->fields;
    $res->MoveNext();
}

echo $l_log_start, $entry;

echo '<table class="table table-hover">';
foreach ($logs as $log) {
    $event = log_parse($log);
    $time = $l_log_months[substr($log[time], 6, 2) - 1] . " " . substr($log[time], 8, 2) . " " . substr($log[time], 0, 4) . " " . substr($log[time], 11);

    echo "<tr><td>", $event['title'], '</td><td>', $time, '</td><td>' . $event['text'] . '</td></tr>';
}

echo "</table>";

$date1 = $l_log_months_short[substr($startdate, 6, 2) - 1] . " " . substr($startdate, 8, 2);
$date2 = $l_log_months_short[substr($yesterday, 6, 2) - 1] . " " . substr($yesterday, 8, 2);
$date3 = $l_log_months_short[substr($yesterday2, 6, 2) - 1] . " " . substr($yesterday2, 8, 2);

$month = substr($startdate, 6, 2);
$day = substr($startdate, 8, 2) - 3;
$year = substr($startdate, 0, 4);

$backlink = mktime(0, 0, 0, $month, $day, $year);
$backlink = date("Y-m-d", $backlink);

$day = substr($startdate, 8, 2) + 3;

$nextlink = mktime(0, 0, 0, $month, $day, $year);
if ($nextlink > time())
    $nextlink = time();
$nextlink = date("Y-m-d", $nextlink);

if ($startdate == date("Y-m-d"))
    $nonext = 1;

if ($swordfish == $adminpass) //fix for admin log view
    $postlink = "&swordfish=" . urlencode($swordfish) . "&player=$player";
else
    $postlink = "";


echo "<td valign=bottom>" .
 "<tr><td><td align=right>" .
 "<img src=images/bottom_panel.gif>" .
 "<br>" .
 "<div style=\"position:relative; top:-23px;\">" .
 "<font size=2><b>" .
 "<a href=log.php?startdate=${backlink}$postlink><<</a>&nbsp;&nbsp;&nbsp;" .
 "<a href=\"#\" onclick=\"activate(2); return false;\" onfocus=\"if(this.blur)this.blur()\">$date3</a>" .
 " | " .
 "<a href=\"#\" onclick=\"activate(1); return false;\" onfocus=\"if(this.blur)this.blur()\">$date2</a>" .
 " | " .
 "<a href=\"#\" onclick=\"activate(0); return false;\" onfocus=\"if(this.blur)this.blur()\">$date1</a>";

if ($nonext != 1)
    echo "&nbsp;&nbsp;&nbsp;<a href=log.php?startdate=${nextlink}$postlink>>>></a>";

echo "&nbsp;&nbsp;&nbsp;";

if ($swordfish == $adminpass) {
    echo "<tr><td><td>" .
    "<FORM action=admin.php method=POST>" .
    "<input type=hidden name=swordfish value=\"$swordfish\">" .
    "<input type=hidden name=menu value=logview>" .
    "<input type=submit value=\"Return to Admin\"></td></tr>";
}

function log_parse($entry)
{
    global $l_log_title;
    global $l_log_text;
    global $l_log_pod;
    global $l_log_nopod;

    switch ($entry[type]) {
        case LOG_LOGIN: //data args are : [ip]
        case LOG_LOGOUT:
        case LOG_BADLOGIN:
        case LOG_HARAKIRI:
            $retvalue[text] = str_replace("[ip]", "<b>$entry[data]</b>", $l_log_text[$entry[type]]);
            $retvalue[title] = $l_log_title[$entry[type]];
            break;

        case LOG_ATTACK_OUTMAN: //data args are : [player]
        case LOG_ATTACK_OUTSCAN:
        case LOG_ATTACK_EWD:
        case LOG_ATTACK_EWDFAIL:
        case LOG_SHIP_SCAN:
        case LOG_SHIP_SCAN_FAIL:
        case LOG_Xenobe_ATTACK:
        case LOG_TEAM_NOT_LEAVE:
            $retvalue[text] = str_replace("[player]", "<b>$entry[data]</b>", $l_log_text[$entry[type]]);
            $retvalue[title] = $l_log_title[$entry[type]];
            break;

        case LOG_ATTACK_LOSE: //data args are : [player] [pod]
            list($name, $pod) = split("\|", $entry['data']);

            $retvalue['text'] = str_replace("[player]", "<b>$name</b>", $l_log_text[$entry['type']]);
            $retvalue['title'] = $l_log_title[$entry['type']];
            if ($pod == 'Y')
                $retvalue['text'] = $retvalue['text'] . $l_log_pod;
            else
                $retvalue['text'] = $retvalue['text'] . $l_log_nopod;
            break;

        case LOG_ATTACKED_WIN: //data args are : [player] [armor] [fighters]
            list($name, $armor, $fighters) = split("\|", $entry[data]);
            $retvalue[text] = str_replace("[player]", "<b>$name</b>", $l_log_text[$entry[type]]);
            $retvalue[text] = str_replace("[armor]", "<b>$armor</b>", $retvalue[text]);
            $retvalue[text] = str_replace("[fighters]", "<b>$fighters</b>", $retvalue[text]);
            $retvalue[title] = $l_log_title[$entry[type]];
            break;

        case LOG_TOLL_PAID: //data args are : [toll] [sector]
        case LOG_TOLL_RECV:
            list($toll, $sector) = split("\|", $entry[data]);
            $retvalue[text] = str_replace("[toll]", "<b>$toll</b>", $l_log_text[$entry[type]]);
            $retvalue[text] = str_replace("[sector]", "<b>$sector</b>", $retvalue[text]);
            $retvalue[title] = $l_log_title[$entry[type]];
            break;

        case LOG_HIT_MINES: //data args are : [mines] [sector]
            list($mines, $sector) = split("\|", $entry[data]);
            $retvalue[text] = str_replace("[mines]", "<b>$mines</b>", $l_log_text[$entry[type]]);
            $retvalue[text] = str_replace("[sector]", "<b>$sector</b>", $retvalue[text]);
            $retvalue[title] = $l_log_title[$entry[type]];
            break;

        case LOG_SHIP_DESTROYED_MINES: //data args are : [sector] [pod]
        case LOG_DEFS_KABOOM:
            list($sector, $pod) = split("\|", $entry[data]);
            $retvalue[text] = str_replace("[sector]", "<b>$sector</b>", $l_log_text[$entry[type]]);
            $retvalue[title] = $l_log_title[$entry[type]];
            if ($pod == 'Y')
                $retvalue[text] = $retvalue[text] . $l_log_pod;
            else
                $retvalue[text] = $retvalue[text] . $l_log_nopod;
            break;

        case LOG_PLANET_DEFEATED_D: //data args are :[planet_name] [sector] [name]
        case LOG_PLANET_DEFEATED:
        case LOG_PLANET_SCAN:
        case LOG_PLANET_SCAN_FAIL:
            list($planet_name, $sector, $name) = split("\|", $entry[data]);
            $retvalue[text] = str_replace("[planet_name]", "<b>$planet_name</b>", $l_log_text[$entry[type]]);
            $retvalue[text] = str_replace("[sector]", "<b>$sector</b>", $retvalue[text]);
            $retvalue[text] = str_replace("[name]", "<b>$name</b>", $retvalue[text]);
            $retvalue[title] = $l_log_title[$entry[type]];
            break;

        case LOG_PLANET_NOT_DEFEATED: //data args are : [planet_name] [sector] [name] [ore] [organics] [goods] [salvage] [credits]
            list($planet_name, $sector, $name, $ore, $organics, $goods, $salvage, $credits) = split("\|", $entry[data]);
            $retvalue[text] = str_replace("[planet_name]", "<b>$planet_name</b>", $l_log_text[$entry[type]]);
            $retvalue[text] = str_replace("[sector]", "<b>$sector</b>", $retvalue[text]);
            $retvalue[text] = str_replace("[name]", "<b>$name</b>", $retvalue[text]);
            $retvalue[text] = str_replace("[ore]", "<b>$ore</b>", $retvalue[text]);
            $retvalue[text] = str_replace("[goods]", "<b>$goods</b>", $retvalue[text]);
            $retvalue[text] = str_replace("[organics]", "<b>$organics</b>", $retvalue[text]);
            $retvalue[text] = str_replace("[salvage]", "<b>$salvage</b>", $retvalue[text]);
            $retvalue[text] = str_replace("[credits]", "<b>$credits</b>", $retvalue[text]);
            $retvalue[title] = $l_log_title[$entry[type]];
            break;

        case LOG_RAW: //data is stored as a message
            $retvalue[title] = $l_log_title[$entry[type]];
            $retvalue[text] = $entry[data];
            break;

        case LOG_DEFS_DESTROYED: //data args are : [quantity] [type] [sector]
            list($quantity, $type, $sector) = split("\|", $entry[data]);
            $retvalue[text] = str_replace("[quantity]", "<b>$quantity</b>", $l_log_text[$entry[type]]);
            $retvalue[text] = str_replace("[type]", "<b>$type</b>", $retvalue[text]);
            $retvalue[text] = str_replace("[sector]", "<b>$sector</b>", $retvalue[text]);
            $retvalue[title] = $l_log_title[$entry[type]];
            break;

        case LOG_PLANET_EJECT: //data args are : [sector] [player]
            list($sector, $name) = split("\|", $entry[data]);
            $retvalue[text] = str_replace("[sector]", "<b>$sector</b>", $l_log_text[$entry[type]]);
            $retvalue[text] = str_replace("[name]", "<b>$name</b>", $retvalue[text]);
            $retvalue[title] = $l_log_title[$entry[type]];
            break;

        case LOG_STARVATION: //data args are : [sector] [starvation]
            list($sector, $starvation) = split("\|", $entry[data]);
            $retvalue[text] = str_replace("[sector]", "<b>$sector</b>", $l_log_text[$entry[type]]);
            $retvalue[text] = str_replace("[starvation]", "<b>$starvation</b>", $retvalue[text]);
            $retvalue[title] = $l_log_title[$entry[type]];
            break;

        case LOG_TOW: //data args are : [sector] [newsector] [hull]
            list($sector, $newsector, $hull) = split("\|", $entry[data]);
            $retvalue[text] = str_replace("[sector]", "<b>$sector</b>", $l_log_text[$entry[type]]);
            $retvalue[text] = str_replace("[newsector]", "<b>$newsector</b>", $retvalue[text]);
            $retvalue[text] = str_replace("[hull]", "<b>$hull</b>", $retvalue[text]);
            $retvalue[title] = $l_log_title[$entry[type]];
            break;

        case LOG_DEFS_DESTROYED_F: //data args are : [fighters] [sector]
            list($fighters, $sector) = split("\|", $entry[data]);
            $retvalue[text] = str_replace("[sector]", "<b>$sector</b>", $l_log_text[$entry[type]]);
            $retvalue[text] = str_replace("[fighters]", "<b>$fighters</b>", $retvalue[text]);
            $retvalue[title] = $l_log_title[$entry[type]];
            break;

        case LOG_TEAM_REJECT: //data args are : [player] [teamname]
            list($player, $teamname) = split("\|", $entry[data]);
            $retvalue[text] = str_replace("[player]", "<b>$player</b>", $l_log_text[$entry[type]]);
            $retvalue[text] = str_replace("[teamname]", "<b>$teamname</b>", $retvalue[text]);
            $retvalue[title] = $l_log_title[$entry[type]];
            break;

        case LOG_TEAM_RENAME: //data args are : [team]
        case LOG_TEAM_M_RENAME:
        case LOG_TEAM_KICK:
        case LOG_TEAM_CREATE:
        case LOG_TEAM_LEAVE:
        case LOG_TEAM_LEAD:
        case LOG_TEAM_JOIN:
        case LOG_TEAM_INVITE:
            $retvalue[text] = str_replace("[team]", "<b>$entry[data]</b>", $l_log_text[$entry[type]]);
            $retvalue[title] = $l_log_title[$entry[type]];
            break;

        case LOG_TEAM_NEWLEAD: //data args are : [team] [name]
        case LOG_TEAM_NEWMEMBER:
            list($team, $name) = split("\|", $entry[data]);
            $retvalue[text] = str_replace("[team]", "<b>$team</b>", $l_log_text[$entry[type]]);
            $retvalue[text] = str_replace("[name]", "<b>$name</b>", $retvalue[text]);
            $retvalue[title] = $l_log_title[$entry[type]];
            break;

        case LOG_ADMIN_HARAKIRI: //data args are : [player] [ip]
            list($player, $ip) = split("\|", $entry[data]);
            $retvalue[text] = str_replace("[player]", "<b>$player</b>", $l_log_text[$entry[type]]);
            $retvalue[text] = str_replace("[ip]", "<b>$ip</b>", $retvalue[text]);
            $retvalue[title] = $l_log_title[$entry[type]];
            break;

        case LOG_ADMIN_ILLEGVALUE: //data args are : [player] [quantity] [type] [holds]
            list($player, $quantity, $type, $holds) = split("\|", $entry[data]);
            $retvalue[text] = str_replace("[player]", "<b>$player</b>", $l_log_text[$entry[type]]);
            $retvalue[text] = str_replace("[quantity]", "<b>$quantity</b>", $retvalue[text]);
            $retvalue[text] = str_replace("[type]", "<b>$type</b>", $retvalue[text]);
            $retvalue[text] = str_replace("[holds]", "<b>$holds</b>", $retvalue[text]);
            $retvalue[title] = $l_log_title[$entry[type]];
            break;

        case LOG_ADMIN_PLANETDEL: //data args are : [attacker] [defender] [sector]
            list($attacker, $defender, $sector) = split("\|", $entry[data]);
            $retvalue[text] = str_replace("[attacker]", "<b>$attacker</b>", $l_log_text[$entry[type]]);
            $retvalue[text] = str_replace("[defender]", "<b>$defender</b>", $retvalue[text]);
            $retvalue[text] = str_replace("[sector]", "<b>$sector</b>", $retvalue[text]);
            $retvalue[title] = $l_log_title[$entry[type]];
            break;

        case LOG_DEFENCE_DEGRADE: //data args are : [sector] [degrade]
            list($sector, $degrade) = split("\|", $entry[data]);
            $retvalue[text] = str_replace("[sector]", "<b>$sector</b>", $l_log_text[$entry[type]]);
            $retvalue[text] = str_replace("[degrade]", "<b>$degrade</b>", $retvalue[text]);
            $retvalue[title] = $l_log_title[$entry[type]];
            break;

        case LOG_PLANET_CAPTURED: //data args are : [cols] [credits] [owner]
            list($cols, $credits, $owner) = split("\|", $entry[data]);
            $retvalue[text] = str_replace("[cols]", "<b>$cols</b>", $l_log_text[$entry[type]]);
            $retvalue[text] = str_replace("[credits]", "<b>$credits</b>", $retvalue[text]);
            $retvalue[text] = str_replace("[owner]", "<b>$owner</b>", $retvalue[text]);
            $retvalue[title] = $l_log_title[$entry[type]];
            break;
        case LOG_BOUNTY_CLAIMED:
            list($amount, $bounty_on, $placed_by) = split("\|", $entry[data]);
            $retvalue[text] = str_replace("[amount]", "<b>$amount</b>", $l_log_text[$entry[type]]);
            $retvalue[text] = str_replace("[bounty_on]", "<b>$bounty_on</b>", $retvalue[text]);
            $retvalue[text] = str_replace("[placed_by]", "<b>$placed_by</b>", $retvalue[text]);
            $retvalue[title] = $l_log_title[$entry[type]];
            break;
        case LOG_BOUNTY_PAID:
            list($amount, $bounty_on) = split("\|", $entry[data]);
            $retvalue[text] = str_replace("[amount]", "<b>$amount</b>", $l_log_text[$entry[type]]);
            $retvalue[text] = str_replace("[bounty_on]", "<b>$bounty_on</b>", $retvalue[text]);
            $retvalue[title] = $l_log_title[$entry[type]];
            break;
        case LOG_BOUNTY_CANCELLED:
            list($amount, $bounty_on) = split("\|", $entry[data]);
            $retvalue[text] = str_replace("[amount]", "<b>$amount</b>", $l_log_text[$entry[type]]);
            $retvalue[text] = str_replace("[bounty_on]", "<b>$bounty_on</b>", $retvalue[text]);
            $retvalue[title] = $l_log_title[$entry[type]];
            break;
        case LOG_BOUNTY_FEDBOUNTY:
            $retvalue[text] = str_replace("[amount]", "<b>$entry[data]</b>", $l_log_text[$entry[type]]);
            $retvalue[title] = $l_log_title[$entry[type]];
            break;
        case LOG_SPACE_PLAGUE:
            list($name, $sector) = split("\|", $entry[data]);
            $retvalue[text] = str_replace("[name]", "<b>$name</b>", $l_log_text[$entry[type]]);
            $retvalue[text] = str_replace("[sector]", "<b>$sector</b>", $retvalue[text]);
            $percentage = $space_plague_kills * 100;
            $retvalue[text] = str_replace("[percentage]", "$space_plague_kills", $retvalue[text]);
            $retvalue[title] = $l_log_title[$entry[type]];
            break;
        case LOG_PLASMA_STORM:
            list($name, $sector, $percentage) = split("\|", $entry[data]);
            $retvalue[text] = str_replace("[name]", "<b>$name</b>", $l_log_text[$entry[type]]);
            $retvalue[text] = str_replace("[sector]", "<b>$sector</b>", $retvalue[text]);
            $retvalue[text] = str_replace("[percentage]", "<b>$percentage</b>", $retvalue[text]);
            $retvalue[title] = $l_log_title[$entry[type]];
            break;
        case LOG_PLANET_BOMBED:
            list($planet_name, $sector, $name, $beams, $torps, $figs) = split("\|", $entry[data]);
            $retvalue[text] = str_replace("[planet_name]", "<b>$planet_name</b>", $l_log_text[$entry[type]]);
            $retvalue[text] = str_replace("[sector]", "<b>$sector</b>", $retvalue[text]);
            $retvalue[text] = str_replace("[name]", "<b>$name</b>", $retvalue[text]);
            $retvalue[text] = str_replace("[beams]", "<b>$beams</b>", $retvalue[text]);
            $retvalue[text] = str_replace("[torps]", "<b>$torps</b>", $retvalue[text]);
            $retvalue[text] = str_replace("[figs]", "<b>$figs</b>", $retvalue[text]);
            $retvalue[title] = $l_log_title[$entry[type]];
            break;
    }
    return $retvalue;
}
