<?php

//declare(strict_types=1;

namespace BNT\Log;

class LogConstants
{

    const LOG_LOGIN = 1;
    const LOG_LOGOUT = 2;
    const LOG_ATTACK_OUTMAN = 3;           //sent to target when better engines
    const LOG_ATTACK_OUTSCAN = 4;          //sent to target when better cloak
    const LOG_ATTACK_EWD = 5;              //sent to target when EWD engaged
    const LOG_ATTACK_EWDFAIL = 6;          //sent to target when EWD failed
    const LOG_ATTACK_LOSE = 7;             //sent to target when he lost
    const LOG_ATTACKED_WIN = 8;            //sent to target when he won
    const LOG_TOLL_PAID = 9;               //sent when paid a toll
    const LOG_HIT_MINES = 10;              //sent when hit mines
    const LOG_SHIP_DESTROYED_MINES = 11;   //sent when destroyed by mines
    const LOG_PLANET_DEFEATED_D = 12;      //sent when one of your defeated planets is destroyed instead of captured
    const LOG_PLANET_DEFEATED = 13;        //sent when a planet is defeated
    const LOG_PLANET_NOT_DEFEATED = 14;    //sent when a planet survives
    const LOG_RAW = 15;                    //this log is sent as-is
    const LOG_TOLL_RECV = 16;              //sent when you receive toll money
    const LOG_DEFS_DESTROYED = 17;         //sent for destroyed sector defenses
    const LOG_PLANET_EJECT = 18;           //sent when ejected from a planet due to alliance switch
    const LOG_BADLOGIN = 19;               //sent when bad login
    const LOG_PLANET_SCAN = 20;            //sent when a planet has been scanned
    const LOG_PLANET_SCAN_FAIL = 21;       //sent when a planet scan failed
    const LOG_PLANET_CAPTURE = 22;         //sent when a planet is captured
    const LOG_SHIP_SCAN = 23;              //sent when a ship is scanned
    const LOG_SHIP_SCAN_FAIL = 24;         //sent when a ship scan fails
    const LOG_Xenobe_ATTACK = 25;        //xenobes send this to themselves
    const LOG_STARVATION = 26;             //sent when colonists are starving... Is this actually used in the game?
    const LOG_TOW = 27;                    //sent when a player is towed
    const LOG_DEFS_DESTROYED_F = 28;       //sent when a player destroys fighters
    const LOG_DEFS_KABOOM = 29;            //sent when sector fighters destroy you
    const LOG_HARAKIRI = 30;               //sent when self-destructed
    const LOG_TEAM_REJECT = 31;            //sent when player refuses invitation
    const LOG_TEAM_RENAME = 32;            //sent when renaming a team
    const LOG_TEAM_M_RENAME = 33;          //sent to members on team rename
    const LOG_TEAM_KICK = 34;              //sent to booted player
    const LOG_TEAM_CREATE = 35;            //sent when created a team
    const LOG_TEAM_LEAVE = 36;             //sent when leaving a team
    const LOG_TEAM_NEWLEAD = 37;           //sent when leaving a team, appointing a new leader
    const LOG_TEAM_LEAD = 38;              //sent to the new team leader
    const LOG_TEAM_JOIN = 39;              //sent when joining a team
    const LOG_TEAM_NEWMEMBER = 40;         //sent to leader on join
    const LOG_TEAM_INVITE = 41;            //sent to invited player
    const LOG_TEAM_NOT_LEAVE = 42;         //sent to leader on leave
    const LOG_ADMIN_HARAKIRI = 43;         //sent to admin on self-destruct
    const LOG_ADMIN_PLANETDEL = 44;        //sent to admin on planet destruction instead of capture
    const LOG_DEFENCE_DEGRADE = 45;        //sent sector fighters have no supporting planet
    const LOG_PLANET_CAPTURED = 46;            //sent to player when he captures a planet
    const LOG_BOUNTY_CLAIMED = 47;            //sent to player when they claim a bounty
    const LOG_BOUNTY_PAID = 48;            //sent to player when their bounty on someone is paid
    const LOG_BOUNTY_CANCELLED = 49;            //sent to player when their bounty is refunded
    const LOG_SPACE_PLAGUE = 50;            // sent when space plague attacks a planet
    const LOG_PLASMA_STORM = 51;           // sent when a plasma storm attacks a planet
    const LOG_BOUNTY_FEDBOUNTY = 52;       // Sent when the federation places a bounty on a player
    const LOG_PLANET_BOMBED = 53;     //Sent after bombing a planet
    const LOG_ADMIN_ILLEGVALUE = 54;        //sent to admin on planet destruction instead of capture
}
