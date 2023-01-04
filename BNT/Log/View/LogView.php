<?php

declare(strict_types=1);

namespace BNT\Log\View;

use BNT\Log\Log;
use BNT\Log\LogWithIP;
use BNT\Log\LogWithPlayer;
use BNT\Log\LogAttackLose;

class LogView
{

    protected Log $log;

    public function __construct(Log $log)
    {
        $this->log = $log;
    }

    public function title(): string
    {
        global $l_log_title;

        return strtr($l_log_title[$this->log->type->value] ?? '', $this->prepareReplace());
    }

    public function text(): string
    {
        global $l_log_text;

        return strtr($l_log_text[$this->log->type->value] ?? '', $this->prepareReplace());
    }

    public function time(): string
    {
        return $this->log->time->format('Y-m-d H:i:s');
    }

    protected function prepareReplace(): array
    {
        global $l_log_pod;
        global $l_log_nopod;

        $log = $this->log;

        if ($log instanceof LogWithIP) {
            return [
                '[ip]' => $log->ip,
            ];
        }

        if ($log instanceof LogWithPlayer) {
            return [
                '[player]' => $log->player,
            ];
        }

        if ($log instanceof LogAttackLose) {
            return [
                '[player]' => $log->player,
                '[text]' => $log->escapepod ? $l_log_pod : $l_log_nopod,
            ];
        }

        return [];
    }

    public static function map(array $logs): array
    {
        return array_map(function ($log) {
            return new static($log);
        }, $logs);
    }

    protected function old()
    {

        switch ($this->log->type) {
            case LogTypeEnum::LOGIN: //data args are : [ip]
            case LogTypeEnum::LOGOUT:
            case LogTypeEnum::BADLOGIN:
            case LogTypeEnum::HARAKIRI:
                $retvalue[text] = str_replace("[ip]", "<font color=white><b>$entry[data]</b></font>", $l_log_text[$entry[type]]);
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
                $retvalue[text] = str_replace("[player]", "<font color=white><b>$entry[data]</b></font>", $l_log_text[$entry[type]]);
                $retvalue[title] = $l_log_title[$entry[type]];
                break;
            case LOG_ATTACK_LOSE: //data args are : [player] [pod]
                list($name, $pod) = split("\|", $entry['data']);

                $retvalue['text'] = str_replace("[player]", "<font color=white><b>$name</b></font>", $l_log_text[$entry['type']]);
                $retvalue['title'] = $l_log_title[$entry['type']];
                if ($pod == 'Y')
                    $retvalue['text'] = $retvalue['text'] . $l_log_pod;
                else
                    $retvalue['text'] = $retvalue['text'] . $l_log_nopod;
                break;
            case LOG_ATTACKED_WIN: //data args are : [player] [armor] [fighters]
                list($name, $armor, $fighters) = split("\|", $entry[data]);
                $retvalue[text] = str_replace("[player]", "<font color=white><b>$name</b></font>", $l_log_text[$entry[type]]);
                $retvalue[text] = str_replace("[armor]", "<font color=white><b>$armor</b></font>", $retvalue[text]);
                $retvalue[text] = str_replace("[fighters]", "<font color=white><b>$fighters</b></font>", $retvalue[text]);
                $retvalue[title] = $l_log_title[$entry[type]];
                break;

            case LOG_TOLL_PAID: //data args are : [toll] [sector]
            case LOG_TOLL_RECV:
                list($toll, $sector) = split("\|", $entry[data]);
                $retvalue[text] = str_replace("[toll]", "<font color=white><b>$toll</b></font>", $l_log_text[$entry[type]]);
                $retvalue[text] = str_replace("[sector]", "<font color=white><b>$sector</b></font>", $retvalue[text]);
                $retvalue[title] = $l_log_title[$entry[type]];
                break;

            case LOG_HIT_MINES: //data args are : [mines] [sector]
                list($mines, $sector) = split("\|", $entry[data]);
                $retvalue[text] = str_replace("[mines]", "<font color=white><b>$mines</b></font>", $l_log_text[$entry[type]]);
                $retvalue[text] = str_replace("[sector]", "<font color=white><b>$sector</b></font>", $retvalue[text]);
                $retvalue[title] = $l_log_title[$entry[type]];
                break;

            case LOG_SHIP_DESTROYED_MINES: //data args are : [sector] [pod]
            case LOG_DEFS_KABOOM:
                list($sector, $pod) = split("\|", $entry[data]);
                $retvalue[text] = str_replace("[sector]", "<font color=white><b>$sector</b></font>", $l_log_text[$entry[type]]);
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
                $retvalue[text] = str_replace("[planet_name]", "<font color=white><b>$planet_name</b></font>", $l_log_text[$entry[type]]);
                $retvalue[text] = str_replace("[sector]", "<font color=white><b>$sector</b></font>", $retvalue[text]);
                $retvalue[text] = str_replace("[name]", "<font color=white><b>$name</b></font>", $retvalue[text]);
                $retvalue[title] = $l_log_title[$entry[type]];
                break;

            case LOG_PLANET_NOT_DEFEATED: //data args are : [planet_name] [sector] [name] [ore] [organics] [goods] [salvage] [credits]
                list($planet_name, $sector, $name, $ore, $organics, $goods, $salvage, $credits) = split("\|", $entry[data]);
                $retvalue[text] = str_replace("[planet_name]", "<font color=white><b>$planet_name</b></font>", $l_log_text[$entry[type]]);
                $retvalue[text] = str_replace("[sector]", "<font color=white><b>$sector</b></font>", $retvalue[text]);
                $retvalue[text] = str_replace("[name]", "<font color=white><b>$name</b></font>", $retvalue[text]);
                $retvalue[text] = str_replace("[ore]", "<font color=white><b>$ore</b></font>", $retvalue[text]);
                $retvalue[text] = str_replace("[goods]", "<font color=white><b>$goods</b></font>", $retvalue[text]);
                $retvalue[text] = str_replace("[organics]", "<font color=white><b>$organics</b></font>", $retvalue[text]);
                $retvalue[text] = str_replace("[salvage]", "<font color=white><b>$salvage</b></font>", $retvalue[text]);
                $retvalue[text] = str_replace("[credits]", "<font color=white><b>$credits</b></font>", $retvalue[text]);
                $retvalue[title] = $l_log_title[$entry[type]];
                break;

            case LOG_RAW: //data is stored as a message
                $retvalue[title] = $l_log_title[$entry[type]];
                $retvalue[text] = $entry[data];
                break;

            case LOG_DEFS_DESTROYED: //data args are : [quantity] [type] [sector]
                list($quantity, $type, $sector) = split("\|", $entry[data]);
                $retvalue[text] = str_replace("[quantity]", "<font color=white><b>$quantity</b></font>", $l_log_text[$entry[type]]);
                $retvalue[text] = str_replace("[type]", "<font color=white><b>$type</b></font>", $retvalue[text]);
                $retvalue[text] = str_replace("[sector]", "<font color=white><b>$sector</b></font>", $retvalue[text]);
                $retvalue[title] = $l_log_title[$entry[type]];
                break;

            case LOG_PLANET_EJECT: //data args are : [sector] [player]
                list($sector, $name) = split("\|", $entry[data]);
                $retvalue[text] = str_replace("[sector]", "<font color=white><b>$sector</b></font>", $l_log_text[$entry[type]]);
                $retvalue[text] = str_replace("[name]", "<font color=white><b>$name</b></font>", $retvalue[text]);
                $retvalue[title] = $l_log_title[$entry[type]];
                break;

            case LOG_STARVATION: //data args are : [sector] [starvation]
                list($sector, $starvation) = split("\|", $entry[data]);
                $retvalue[text] = str_replace("[sector]", "<font color=white><b>$sector</b></font>", $l_log_text[$entry[type]]);
                $retvalue[text] = str_replace("[starvation]", "<font color=white><b>$starvation</b></font>", $retvalue[text]);
                $retvalue[title] = $l_log_title[$entry[type]];
                break;

            case LOG_TOW: //data args are : [sector] [newsector] [hull]
                list($sector, $newsector, $hull) = split("\|", $entry[data]);
                $retvalue[text] = str_replace("[sector]", "<font color=white><b>$sector</b></font>", $l_log_text[$entry[type]]);
                $retvalue[text] = str_replace("[newsector]", "<font color=white><b>$newsector</b></font>", $retvalue[text]);
                $retvalue[text] = str_replace("[hull]", "<font color=white><b>$hull</b></font>", $retvalue[text]);
                $retvalue[title] = $l_log_title[$entry[type]];
                break;

            case LOG_DEFS_DESTROYED_F: //data args are : [fighters] [sector]
                list($fighters, $sector) = split("\|", $entry[data]);
                $retvalue[text] = str_replace("[sector]", "<font color=white><b>$sector</b></font>", $l_log_text[$entry[type]]);
                $retvalue[text] = str_replace("[fighters]", "<font color=white><b>$fighters</b></font>", $retvalue[text]);
                $retvalue[title] = $l_log_title[$entry[type]];
                break;

            case LOG_TEAM_REJECT: //data args are : [player] [teamname]
                list($player, $teamname) = split("\|", $entry[data]);
                $retvalue[text] = str_replace("[player]", "<font color=white><b>$player</b></font>", $l_log_text[$entry[type]]);
                $retvalue[text] = str_replace("[teamname]", "<font color=white><b>$teamname</b></font>", $retvalue[text]);
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
                $retvalue[text] = str_replace("[team]", "<font color=white><b>$entry[data]</b></font>", $l_log_text[$entry[type]]);
                $retvalue[title] = $l_log_title[$entry[type]];
                break;

            case LOG_TEAM_NEWLEAD: //data args are : [team] [name]
            case LOG_TEAM_NEWMEMBER:
                list($team, $name) = split("\|", $entry[data]);
                $retvalue[text] = str_replace("[team]", "<font color=white><b>$team</b></font>", $l_log_text[$entry[type]]);
                $retvalue[text] = str_replace("[name]", "<font color=white><b>$name</b></font>", $retvalue[text]);
                $retvalue[title] = $l_log_title[$entry[type]];
                break;

            case LOG_ADMIN_HARAKIRI: //data args are : [player] [ip]
                list($player, $ip) = split("\|", $entry[data]);
                $retvalue[text] = str_replace("[player]", "<font color=white><b>$player</b></font>", $l_log_text[$entry[type]]);
                $retvalue[text] = str_replace("[ip]", "<font color=white><b>$ip</b></font>", $retvalue[text]);
                $retvalue[title] = $l_log_title[$entry[type]];
                break;

            case LOG_ADMIN_ILLEGVALUE: //data args are : [player] [quantity] [type] [holds]
                list($player, $quantity, $type, $holds) = split("\|", $entry[data]);
                $retvalue[text] = str_replace("[player]", "<font color=white><b>$player</b></font>", $l_log_text[$entry[type]]);
                $retvalue[text] = str_replace("[quantity]", "<font color=white><b>$quantity</b></font>", $retvalue[text]);
                $retvalue[text] = str_replace("[type]", "<font color=white><b>$type</b></font>", $retvalue[text]);
                $retvalue[text] = str_replace("[holds]", "<font color=white><b>$holds</b></font>", $retvalue[text]);
                $retvalue[title] = $l_log_title[$entry[type]];
                break;

            case LOG_ADMIN_PLANETDEL: //data args are : [attacker] [defender] [sector]
                list($attacker, $defender, $sector) = split("\|", $entry[data]);
                $retvalue[text] = str_replace("[attacker]", "<font color=white><b>$attacker</b></font>", $l_log_text[$entry[type]]);
                $retvalue[text] = str_replace("[defender]", "<font color=white><b>$defender</b></font>", $retvalue[text]);
                $retvalue[text] = str_replace("[sector]", "<font color=white><b>$sector</b></font>", $retvalue[text]);
                $retvalue[title] = $l_log_title[$entry[type]];
                break;

            case LOG_DEFENCE_DEGRADE: //data args are : [sector] [degrade]
                list($sector, $degrade) = split("\|", $entry[data]);
                $retvalue[text] = str_replace("[sector]", "<font color=white><b>$sector</b></font>", $l_log_text[$entry[type]]);
                $retvalue[text] = str_replace("[degrade]", "<font color=white><b>$degrade</b></font>", $retvalue[text]);
                $retvalue[title] = $l_log_title[$entry[type]];
                break;

            case LOG_PLANET_CAPTURED: //data args are : [cols] [credits] [owner]
                list($cols, $credits, $owner) = split("\|", $entry[data]);
                $retvalue[text] = str_replace("[cols]", "<font color=white><b>$cols</b></font>", $l_log_text[$entry[type]]);
                $retvalue[text] = str_replace("[credits]", "<font color=white><b>$credits</b></font>", $retvalue[text]);
                $retvalue[text] = str_replace("[owner]", "<font color=white><b>$owner</b></font>", $retvalue[text]);
                $retvalue[title] = $l_log_title[$entry[type]];
                break;
            case LOG_BOUNTY_CLAIMED:
                list($amount, $bounty_on, $placed_by) = split("\|", $entry[data]);
                $retvalue[text] = str_replace("[amount]", "<font color=white><b>$amount</b></font>", $l_log_text[$entry[type]]);
                $retvalue[text] = str_replace("[bounty_on]", "<font color=white><b>$bounty_on</b></font>", $retvalue[text]);
                $retvalue[text] = str_replace("[placed_by]", "<font color=white><b>$placed_by</b></font>", $retvalue[text]);
                $retvalue[title] = $l_log_title[$entry[type]];
                break;
            case LOG_BOUNTY_PAID:
                list($amount, $bounty_on) = split("\|", $entry[data]);
                $retvalue[text] = str_replace("[amount]", "<font color=white><b>$amount</b></font>", $l_log_text[$entry[type]]);
                $retvalue[text] = str_replace("[bounty_on]", "<font color=white><b>$bounty_on</b></font>", $retvalue[text]);
                $retvalue[title] = $l_log_title[$entry[type]];
                break;
            case LOG_BOUNTY_CANCELLED:
                list($amount, $bounty_on) = split("\|", $entry[data]);
                $retvalue[text] = str_replace("[amount]", "<font color=white><b>$amount</b></font>", $l_log_text[$entry[type]]);
                $retvalue[text] = str_replace("[bounty_on]", "<font color=white><b>$bounty_on</b></font>", $retvalue[text]);
                $retvalue[title] = $l_log_title[$entry[type]];
                break;
            case LOG_BOUNTY_FEDBOUNTY:
                $retvalue[text] = str_replace("[amount]", "<font color=white><b>$entry[data]</b></font>", $l_log_text[$entry[type]]);
                $retvalue[title] = $l_log_title[$entry[type]];
                break;
            case LOG_SPACE_PLAGUE:
                list($name, $sector) = split("\|", $entry[data]);
                $retvalue[text] = str_replace("[name]", "<font color=white><b>$name</b></font>", $l_log_text[$entry[type]]);
                $retvalue[text] = str_replace("[sector]", "<font color=white><b>$sector</b></font>", $retvalue[text]);
                $percentage = $space_plague_kills * 100;
                $retvalue[text] = str_replace("[percentage]", "$space_plague_kills", $retvalue[text]);
                $retvalue[title] = $l_log_title[$entry[type]];
                break;
            case LOG_PLASMA_STORM:
                list($name, $sector, $percentage) = split("\|", $entry[data]);
                $retvalue[text] = str_replace("[name]", "<font color=white><b>$name</b></font>", $l_log_text[$entry[type]]);
                $retvalue[text] = str_replace("[sector]", "<font color=white><b>$sector</b></font>", $retvalue[text]);
                $retvalue[text] = str_replace("[percentage]", "<font color=white><b>$percentage</b></font>", $retvalue[text]);
                $retvalue[title] = $l_log_title[$entry[type]];
                break;
            case LOG_PLANET_BOMBED:
                list($planet_name, $sector, $name, $beams, $torps, $figs) = split("\|", $entry[data]);
                $retvalue[text] = str_replace("[planet_name]", "<font color=white><b>$planet_name</b></font>", $l_log_text[$entry[type]]);
                $retvalue[text] = str_replace("[sector]", "<font color=white><b>$sector</b></font>", $retvalue[text]);
                $retvalue[text] = str_replace("[name]", "<font color=white><b>$name</b></font>", $retvalue[text]);
                $retvalue[text] = str_replace("[beams]", "<font color=white><b>$beams</b></font>", $retvalue[text]);
                $retvalue[text] = str_replace("[torps]", "<font color=white><b>$torps</b></font>", $retvalue[text]);
                $retvalue[text] = str_replace("[figs]", "<font color=white><b>$figs</b></font>", $retvalue[text]);
                $retvalue[title] = $l_log_title[$entry[type]];
                break;
        }
        return $retvalue;
    }

}
