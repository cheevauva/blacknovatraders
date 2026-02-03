<?php

include 'config.php';

$title = $l_log_titlet;

if (checklogin()) {
    die();
}
if (fromRequest('swordfish') == $adminpass) { //check if called by admin script
    $player = fromRequest('player', 0);

    if ($player) {
        $playerinfo = shipById($player);
    }
}

$startdate = fromRequest('startdate', date('Y-m-d'));
$yesterday = date('Y-m-d', strtotime($startdate . ' -1 day'));
$yesterday2 = date('Y-m-d', strtotime($startdate . ' -2 day'));

$logs = logsByShipAndDate($playerinfo['ship_id'], $yesterday2);
$logs += logsByShipAndDate($playerinfo['ship_id'], $yesterday);
$logs += logsByShipAndDate($playerinfo['ship_id'], $startdate);

foreach ($logs as $index => $log) {
    $logs[$index] = logParse($log);
}

function logParse($entry)
{
    global $l_log_title;
    global $l_log_text;
    global $l_log_pod;
    global $l_log_nopod;
    global $space_plague_kills;

    if (!empty($l_log_title[$entry['type']])) {
        $title = $l_log_title[$entry['type']];
    } else {
        $title = $entry['type'];
    }

    if (!empty($l_log_text[$entry['type']])) {
        $text = $l_log_text[$entry['type']];
    } else {
        $text = $entry['data'];
    }

    switch ($entry['type']) {
        case LOG_LOGIN:
        case LOG_LOGOUT:
        case LOG_BADLOGIN:
        case LOG_HARAKIRI:
            $placeholders = [
                '[ip]' => $entry['data']
            ];
            break;
        case LOG_ATTACK_OUTMAN:
        case LOG_ATTACK_OUTSCAN:
        case LOG_ATTACK_EWD:
        case LOG_ATTACK_EWDFAIL:
        case LOG_SHIP_SCAN:
        case LOG_SHIP_SCAN_FAIL:
        case LOG_Xenobe_ATTACK:
        case LOG_TEAM_NOT_LEAVE:
            $placeholders = [
                '[player]' => $entry['data']
            ];
            break;
        case LOG_ATTACK_LOSE:
            list($name, $pod) = split("\|", $entry['data']);
            $text .= ($pod == 'Y' ? $l_log_pod : $l_log_nopod);
            $placeholders = [
                '[player]' => $name
            ];
            break;
        case LOG_ATTACKED_WIN:
            list($name, $armor, $fighters) = split("\|", $entry['data']);
            $placeholders = [
                '[player]' => $name,
                '[armor]' => $armor,
                '[fighters]' => $fighters
            ];
            break;
        case LOG_TOLL_PAID:
        case LOG_TOLL_RECV:
            list($toll, $sector) = split("\|", $entry['data']);
            $placeholders = [
                '[toll]' => $toll,
                '[sector]' => $sector
            ];
            break;
        case LOG_HIT_MINES:
            list($mines, $sector) = split("\|", $entry['data']);
            $placeholders = [
                '[mines]' => $mines,
                '[sector]' => $sector
            ];
            break;
        case LOG_SHIP_DESTROYED_MINES:
        case LOG_DEFS_KABOOM:
            list($sector, $pod) = split("\|", $entry['data']);
            $text .= ($pod == 'Y' ? $l_log_pod : $l_log_nopod);
            $placeholders = [
                '[sector]' => $sector
            ];
            break;
        case LOG_PLANET_DEFEATED_D:
        case LOG_PLANET_DEFEATED:
        case LOG_PLANET_SCAN:
        case LOG_PLANET_SCAN_FAIL:
            list($planet_name, $sector, $name) = split("\|", $entry['data']);
            $placeholders = [
                '[planet_name]' => $planet_name,
                '[sector]' => $sector,
                '[name]' => $name
            ];
            break;

        case LOG_PLANET_NOT_DEFEATED:
            list($planet_name, $sector, $name, $ore, $organics, $goods, $salvage, $credits) = split("\|", $entry['data']);
            $placeholders = [
                '[planet_name]' => $planet_name,
                '[sector]' => $sector,
                '[name]' => $name,
                '[ore]' => $ore,
                '[goods]' => $goods,
                '[organics]' => $organics,
                '[salvage]' => $salvage,
                '[credits]' => $credits
            ];
            break;

        case LOG_RAW:
            $text = $entry['data'];
            break;
        case LOG_DEFS_DESTROYED:
            list($quantity, $type, $sector) = split("\|", $entry['data']);
            $placeholders = [
                '[quantity]' => $quantity,
                '[type]' => $type,
                '[sector]' => $sector
            ];
            break;

        case LOG_PLANET_EJECT:
            list($sector, $name) = split("\|", $entry['data']);
            $placeholders = [
                '[sector]' => $sector,
                '[name]' => $name
            ];
            break;

        case LOG_STARVATION:
            list($sector, $starvation) = split("\|", $entry['data']);
            $placeholders = [
                '[sector]' => $sector,
                '[starvation]' => $starvation
            ];
            break;
        case LOG_TOW:
            list($sector, $newsector, $hull) = split("\|", $entry['data']);
            $placeholders = [
                '[sector]' => $sector,
                '[newsector]' => $newsector,
                '[hull]' => $hull
            ];
            break;
        case LOG_DEFS_DESTROYED_F:
            list($fighters, $sector) = split("\|", $entry['data']);
            $placeholders = [
                '[sector]' => $sector,
                '[fighters]' => $fighters
            ];
            break;
        case LOG_TEAM_REJECT:
            list($player, $teamname) = split("\|", $entry['data']);
            $placeholders = [
                '[player]' => $player,
                '[teamname]' => $teamname
            ];
            break;
        case LOG_TEAM_RENAME:
        case LOG_TEAM_M_RENAME:
        case LOG_TEAM_KICK:
        case LOG_TEAM_CREATE:
        case LOG_TEAM_LEAVE:
        case LOG_TEAM_LEAD:
        case LOG_TEAM_JOIN:
        case LOG_TEAM_INVITE:
            $placeholders = [
                '[team]' => $entry['data']
            ];
            break;
        case LOG_TEAM_NEWLEAD:
        case LOG_TEAM_NEWMEMBER:
            list($team, $name) = split("\|", $entry['data']);
            $placeholders = [
                '[team]' => $team,
                '[name]' => $name
            ];
            break;
        case LOG_ADMIN_HARAKIRI:
            list($player, $ip) = split("\|", $entry['data']);
            $placeholders = [
                '[player]' => $player,
                '[ip]' => $ip
            ];
            break;
        case LOG_ADMIN_ILLEGVALUE:
            list($player, $quantity, $type, $holds) = split("\|", $entry['data']);
            $placeholders = [
                '[player]' => $player,
                '[quantity]' => $quantity,
                '[type]' => $type,
                '[holds]' => $holds
            ];
            break;
        case LOG_ADMIN_PLANETDEL:
            list($attacker, $defender, $sector) = split("\|", $entry['data']);
            $placeholders = [
                '[attacker]' => $attacker,
                '[defender]' => $defender,
                '[sector]' => $sector
            ];
            break;
        case LOG_DEFENCE_DEGRADE:
            list($sector, $degrade) = split("\|", $entry['data']);
            $placeholders = [
                '[sector]' => $sector,
                '[degrade]' => $degrade
            ];
            break;
        case LOG_PLANET_CAPTURED:
            list($cols, $credits, $owner) = split("\|", $entry['data']);
            $placeholders = [
                '[cols]' => $cols,
                '[credits]' => $credits,
                '[owner]' => $owner
            ];
            break;
        case LOG_BOUNTY_CLAIMED:
            list($amount, $bounty_on, $placed_by) = split("\|", $entry['data']);
            $placeholders = [
                '[amount]' => $amount,
                '[bounty_on]' => $bounty_on,
                '[placed_by]' => $placed_by
            ];
            break;
        case LOG_BOUNTY_PAID:
            list($amount, $bounty_on) = split("\|", $entry['data']);
            $placeholders = [
                '[amount]' => $amount,
                '[bounty_on]' => $bounty_on
            ];
            break;
        case LOG_BOUNTY_CANCELLED:
            list($amount, $bounty_on) = split("\|", $entry['data']);
            $placeholders = [
                '[amount]' => $amount,
                '[bounty_on]' => $bounty_on
            ];
            break;
        case LOG_BOUNTY_FEDBOUNTY:
            $placeholders = [
                '[amount]' => $entry['data']
            ];
            break;
        case LOG_SPACE_PLAGUE:
            list($name, $sector) = split("\|", $entry['data']);
            $percentage = $space_plague_kills * 100;
            $placeholders = [
                '[name]' => $name,
                '[sector]' => $sector,
                '[percentage]' => $space_plague_kills
            ];
            break;
        case LOG_PLASMA_STORM:
            list($name, $sector, $percentage) = split("\|", $entry['data']);
            $placeholders = [
                '[name]' => $name,
                '[sector]' => $sector,
                '[percentage]' => $percentage
            ];
            break;
        case LOG_PLANET_BOMBED:
            list($planet_name, $sector, $name, $beams, $torps, $figs) = split("\|", $entry['data']);
            $placeholders = [
                '[planet_name]' => $planet_name,
                '[sector]' => $sector,
                '[name]' => $name,
                '[beams]' => $beams,
                '[torps]' => $torps,
                '[figs]' => $figs
            ];
            break;
        default:
            $text = $entry['data'];
            break;
    }

    $entry['text'] = strtr($text, $placeholders);
    $entry['title'] = $title;

    return $entry;
}

include_once 'tpls/log.tpl.php';
