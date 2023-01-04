<?php

declare(strict_types = 1);

namespace BNT\Log;

enum LogTypeEnum: int
{
    case UNDEFINED = 0;
    case LOGIN = 1;
    case LOGOUT = 2;
    case ATTACK_OUTMAN = 3; //sent to target when better engines
    case ATTACK_OUTSCAN = 4; //sent to target when better cloak
    case ATTACK_EWD = 5; //sent to target when EWD engaged
    case ATTACK_EWDFAIL = 6; //sent to target when EWD failed
    case ATTACK_LOSE = 7; //sent to target when he lost
    case ATTACKED_WIN = 8; //sent to target when he won
    case TOLL_PAID = 9; //sent when paid a toll
    case HIT_MINES = 10; //sent when hit mines
    case SHIP_DESTROYED_MINES = 11; //sent when destroyed by mines
    case PLANET_DEFEATED_D = 12; //sent when one of your defeated planets is destroyed instead of captured
    case PLANET_DEFEATED = 13; //sent when a planet is defeated
    case PLANET_NOT_DEFEATED = 14; //sent when a planet survives
    case RAW = 15;  //this log is sent as-is
    case TOLL_RECV = 16; //sent when you receive toll money
    case DEFS_DESTROYED = 17; //sent for destroyed sector defenses
    case PLANET_EJECT = 18; //sent when ejected from a planet due to alliance switch
    case BADLOGIN = 19; //sent when bad login
    case PLANET_SCAN = 20; //sent when a planet has been scanned
    case PLANET_SCAN_FAIL = 21; //sent when a planet scan failed
    case PLANET_CAPTURE = 22; //sent when a planet is captured
    case SHIP_SCAN = 23; //sent when a ship is scanned
    case SHIP_SCAN_FAIL = 24; //sent when a ship scan fails
    case Xenobe_ATTACK = 25; //xenobes send this to themselves
    case STARVATION = 26; //sent when colonists are starving... Is this actually used in the game?
    case TOW = 27;  //sent when a player is towed
    case DEFS_DESTROYED_F = 28; //sent when a player destroys fighters
    case DEFS_KABOOM = 29; //sent when sector fighters destroy you
    case HARAKIRI = 30; //sent when self-destructed
    case TEAM_REJECT = 31; //sent when player refuses invitation
    case TEAM_RENAME = 32; //sent when renaming a team
    case TEAM_M_RENAME = 33; //sent to members on team rename
    case TEAM_KICK = 34; //sent to booted player
    case TEAM_CREATE = 35; //sent when created a team
    case TEAM_LEAVE = 36; //sent when leaving a team
    case TEAM_NEWLEAD = 37; //sent when leaving a team=  appointing a new leader
    case TEAM_LEAD = 38; //sent to the new team leader
    case TEAM_JOIN = 39; //sent when joining a team
    case TEAM_NEWMEMBER = 40; //sent to leader on join
    case TEAM_INVITE = 41; //sent to invited player
    case TEAM_NOT_LEAVE = 42; //sent to leader on leave
    case ADMIN_HARAKIRI = 43; //sent to admin on self-destruct
    case ADMIN_PLANETDEL = 44; //sent to admin on planet destruction instead of capture
    case DEFENCE_DEGRADE = 45; //sent sector fighters have no supporting planet
    case PLANET_CAPTURED = 46; //sent to player when he captures a planet
    case BOUNTY_CLAIMED = 47; //sent to player when they claim a bounty
    case BOUNTY_PAID = 48; //sent to player when their bounty on someone is paid
    case BOUNTY_CANCELLED = 49; //sent to player when their bounty is refunded
    case SPACE_PLAGUE = 50; // sent when space plague attacks a planet
    case PLASMA_STORM = 51; // sent when a plasma storm attacks a planet
    case BOUNTY_FEDBOUNTY = 52; // Sent when the federation places a bounty on a player
    case PLANET_BOMBED = 53; //Sent after bombing a planet
    case ADMIN_ILLEGVALUE = 54; //sent to admin on planet destruction instead of capture

}
