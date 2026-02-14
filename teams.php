<?php

use BNT\Log\LogTypeConstants;

include 'config.php';

$title = $l_team_title;
include("header.php");

if (checklogin()) {
    die();
}
bigtitle();
$testing = false; // set to false to get rid of password when creating new alliance

/*
  Setting up some recordsets.

  I noticed before the rewriting of this page
  that in some case recordset may be fetched
  more thant once, which is NOT optimized.
 */

/* Get user info */
$playerinfo = db()->fetch("SELECT ships.*, teams.team_name, teams.description, teams.creator, teams.id
                        FROM ships
                        LEFT JOIN teams ON ships.team = teams.id
                        WHERE ships.email= :username", [
    'username' => $username
]);

/*
  We do not want to query the database
  if it is not necessary.
 */
if ($playerinfo['team_invite'] != "") {
    /* Get invite info */
    $invite_info = db()->fetch(" SELECT ships.ship_id, ships.team_invite, teams.team_name,teams.id
                        FROM ships
                        LEFT JOIN teams ON ships.team_invite = teams.id
                        WHERE ships.email= :username", [
        'username' => $username
    ]);
}
$whichteam = fromRequest('whichteam');
$teamwhat = fromRequest('teamwhat');
$invited = fromRequest('invited');
$confirmleave = fromRequest('confirmleave');
$update = fromRequest('update');
$swordfish = fromRequest('swordfish');
/*
  Get Team Info
 */
$whichteam = stripnum($whichteam);
if ($whichteam) {
    $team = db()->fetch("SELECT * FROM teams WHERE id= :whichteam", [
        'whichteam' => $whichteam
    ]);
} else {
    $team = db()->fetch("SELECT * FROM teams WHERE id= :team", [
        'team' => $playerinfo['team']
    ]);
}

function LINK_BACK()
{
    global $l_clickme, $l_team_menu;
    echo "<BR><BR><a href=\"teams.php\">$l_clickme</a> $l_team_menu.<BR><BR>";
}

/*
  Rewrited display of alliances list
 */

function DISPLAY_ALL_ALLIANCES()
{
    global $color, $color_header, $order, $type, $l_team_galax, $l_team_member, $l_team_coord, $l_score, $l_name;
    global $l_team_members;

    echo "<br><br>$l_team_galax<BR>";
    echo "<TABLE class=\"table\">";
    echo "<TR>";

    if ($type == "d") {
        $type = "a";
        $by = "ASC";
    } else {
        $type = "d";
        $by = "DESC";
    }
    echo "<TH><A HREF=teams.php?order=team_name&type=$type>$l_name</A></TH>";
    echo "<TH><A HREF=teams.php?order=number_of_members&type=$type>$l_team_members</A></TH>";
    echo "<TH><A HREF=teams.php?order=character_name&type=$type>$l_team_coord</A></TH>";
    echo "<TH><A HREF=teams.php?order=total_score&type=$type>$l_score</A></TH>";
    echo "</TR>";
    $sql_query = "SELECT ships.character_name,
                     COUNT(*) as number_of_members,
                     ROUND(SQRT(SUM(POW(ships.score,2)))) as total_score,
                     teams.id,
                     teams.team_name,
                     teams.creator
                  FROM ships
                  LEFT JOIN teams ON ships.team = teams.id
                  WHERE ships.team = teams.id
                  GROUP BY teams.team_name";
    /*
      Setting if the order is Ascending or descending, if any.
      Default is ordered by teams.team_name
     */
    if ($order) {
        $sql_query = $sql_query . " ORDER BY " . $order . " $by";
    }
    $res = db()->fetchAll($sql_query);
    foreach ($res as $row) {
        echo "<TR>";
        echo "<TD><a href=teams.php?teamwhat=1&whichteam=" . $row['id'] . ">" . $row['team_name'] . "</A></TD>";
        echo "<TD>" . $row['number_of_members'] . "</TD>";
        
        $row2 = db()->fetch("SELECT character_name FROM ships WHERE ship_id = :creator", [
            'creator' => $row['creator']
        ]);

        echo "<TD><a href=mailto2.php?name=" . $row2['character_name'] . ">" . $row2['character_name'] . "</A></TD>";
        echo "<TD>" . $row['total_score'] . "</TD>";
        echo "</TR>";
    }
    echo "</table><BR>";
}

function DISPLAY_INVITE_INFO()
{
    global $playerinfo, $invite_info, $l_team_noinvite, $l_team_ifyouwant, $l_team_tocreate, $l_clickme, $l_team_injoin, $l_team_tojoin, $l_team_reject, $l_team_or;
    if (!$playerinfo['team_invite']) {
        echo "<br><br><p>$l_team_noinvite</p><BR>";
        echo "$l_team_ifyouwant<BR>";
        echo "<a href=\"teams.php?teamwhat=6\">$l_clickme</a> $l_team_tocreate<BR><BR>";
    } else {
        echo "<br><br><p>$l_team_injoin ";
        echo "<a href=teams.php?teamwhat=1&whichteam=" . $playerinfo['team_invite'] . ">" . $invite_info['team_name'] . "</A>.</p><BR>";
        echo "<A HREF=teams.php?teamwhat=3&whichteam=" . $playerinfo['team_invite'] . ">$l_clickme</A> $l_team_tojoin <strong>" . $invite_info['team_name'] . "</strong> $l_team_or <A HREF=teams.php?teamwhat=8&whichteam=" . $playerinfo['team_invite'] . ">$l_clickme</A> $l_team_reject<BR><BR>";
    }
}

function showinfo($whichteam, $isowner)
{
    global $playerinfo, $invite_info, $team, $l_team_coord, $l_team_member, $l_options, $l_team_ed, $l_team_inv, $l_team_leave, $l_team_members, $l_score, $l_team_noinvites, $l_team_pending;
    global $l_team_eject;

    /* Heading */
    echo "<div>";
    echo "<h3><strong>" . $team['team_name'] . "</strong>";
    echo "<br><span>\"<em>" . $team['description'] . "</em>\"</span></h3>";
    if ($playerinfo['team'] == $team['id']) {
        if ($playerinfo['ship_id'] == $team['creator']) {
            echo "$l_team_coord ";
        } else {
            echo "$l_team_member ";
        }
        echo "$l_options<br>";
        if ($playerinfo['ship_id'] == $team['creator']) {
            echo "[<a href=teams.php?teamwhat=9&whichteam=" . $playerinfo['team'] . ">$l_team_ed</a>] - ";
        }
        echo "[<a href=teams.php?teamwhat=7&whichteam=" . $playerinfo['team'] . ">$l_team_inv</a>] - [<a href=teams.php?teamwhat=2&whichteam=" . $playerinfo['team'] . ">$l_team_leave</a>]";
    }
    DISPLAY_INVITE_INFO();
    echo "</div>";

    /* Main table */
    echo "<table class=\"table\">";
    echo "<tr><th>$l_team_members</th></tr>";
    $members = db()->fetchAll("SELECT * FROM ships WHERE team= :whichteam", [
        'whichteam' => $whichteam
    ]);
    foreach ($members as $member) {
        echo "<tr><td> - " . $member['character_name'] . " ($l_score " . $member['score'] . ")";
        if ($isowner && ($member['ship_id'] != $playerinfo['ship_id'])) {
            echo " - [<a href=\"teams.php?teamwhat=5&who=" . $member['ship_id'] . "\">$l_team_eject</A>]</td>";
        } else {
            if ($member['ship_id'] == $team['creator']) {
                echo " - $l_team_coord</td>";
            }
        }
        echo "</tr>";
    }
    /* Displays for members name */
    $pending = db()->fetchAll("SELECT ship_id,character_name FROM ships WHERE team_invite= :whichteam", [
        'whichteam' => $whichteam
    ]);
    echo "<tr><th>$l_team_pending <strong>" . $team['team_name'] . "</strong></th></tr>";
    if (count($pending) > 0) {
        foreach ($pending as $who) {
            echo "<tr><td> - " . $who['character_name'] . "</td></tr>";
        }
    } else {
        echo "<tr><td>$l_team_noinvites <strong>" . $team['team_name'] . "</strong>.</td></tr>";
    }
    echo "</table>";
}

switch ($teamwhat) {
    case 1: // INFO on sigle alliance
        showinfo($whichteam, 0);
        LINK_BACK();
        break;
    case 2: // LEAVE
        if (!$confirmleave) {
            echo sprintf(...[
                "%s <strong>%s</strong> ? <a href=\"teams.php?teamwhat=%s&confirmleave=1&whichteam=%s\">%s</a> - <A HREF=\"teams.php\">%s</A><BR><BR>",
                $l_team_confirmleave,
                $team['team_name'],
                $teamwhat,
                $whichteam,
                $l_yes,
                $l_no
            ]);
        } elseif ($confirmleave == 1) {
            if ($team['number_of_members'] == 1) {
                db()->q("DELETE FROM teams WHERE id= :whichteam", [
                    'whichteam' => $whichteam
                ]);
                db()->q("UPDATE ships SET team='0' WHERE ship_id= :ship_id", [
                    'ship_id' => $playerinfo['ship_id']
                ]);
                db()->q("UPDATE ships SET team_invite=0 WHERE team_invite= :whichteam", [
                    'whichteam' => $whichteam
                ]);

                $sectors_res = db()->fetchAll("SELECT DISTINCT sector_id FROM planets WHERE owner= :owner AND base='Y'", [
                    'owner' => $playerinfo['ship_id']
                ]);
                $sectors = array();
                foreach ($sectors_res as $row) {
                    $sectors[] = $row['sector_id'];
                }

                db()->q("UPDATE planets SET corp=0 WHERE owner= :owner", [
                    'owner' => $playerinfo['ship_id']
                ]);
                if (!empty($sectors)) {
                    foreach ($sectors as $sector) {
                        calc_ownership($sector);
                    }
                }
                defence_vs_defence($playerinfo['ship_id']);
                kick_off_planet($playerinfo['ship_id'], $whichteam);

                $l_team_onlymember = str_replace("[team_name]", "<strong>" . $team['team_name'] . "</strong>", $l_team_onlymember);
                echo "$l_team_onlymember<BR><BR>";
                playerlog($playerinfo['ship_id'], LogTypeConstants::LOG_TEAM_LEAVE, $team['team_name']);
            } else {
                if ($team['creator'] == $playerinfo['ship_id']) {
                    echo sprintf(...[
                        "%s <strong>%s</strong>. %s<BR><BR>",
                        $l_team_youarecoord,
                        $team['team_name'],
                        $l_team_relinq
                    ]);
                    echo "<FORM ACTION='teams.php' METHOD=POST>";
                    echo "<INPUT TYPE=hidden name=teamwhat value=$teamwhat><INPUT TYPE=hidden name=confirmleave value=2><INPUT TYPE=hidden name=whichteam value=$whichteam>";
                    echo "<label>$l_team_newc</label> <SELECT NAME=newcreator>";
                    $members = db()->fetchAll("SELECT character_name,ship_id FROM ships WHERE team= :whichteam ORDER BY character_name ASC", [
                        'whichteam' => $whichteam
                    ]);
                    foreach ($members as $row) {
                        if ($row['ship_id'] != $team['creator']) {
                            echo "<OPTION VALUE=" . $row['ship_id'] . ">" . $row['character_name'];
                        }
                    }
                    echo "</SELECT>";
                    echo "<INPUT TYPE=SUBMIT VALUE=$l_submit>";
                    echo "</FORM>";
                } else {
                    db()->q("UPDATE ships SET team='0' WHERE ship_id= :ship_id", [
                        'ship_id' => $playerinfo['ship_id']
                    ]);
                    db()->q("UPDATE teams SET number_of_members=number_of_members-1 WHERE id= :whichteam", [
                        'whichteam' => $whichteam
                    ]);

                    $sectors_res = db()->fetchAll("SELECT DISTINCT sector_id FROM planets WHERE owner= :owner AND base='Y' AND corp!=0", [
                        'owner' => $playerinfo['ship_id']
                    ]);
                    $sectors = array();
                    foreach ($sectors_res as $row) {
                        $sectors[] = $row['sector_id'];
                    }

                    db()->q("UPDATE planets SET corp=0 WHERE owner= :owner", [
                        'owner' => $playerinfo['ship_id']
                    ]);
                    if (!empty($sectors)) {
                        foreach ($sectors as $sector) {
                            calc_ownership($sector);
                        }
                    }

                    echo sprintf(...[
                        "%s <strong>%s</strong>.<BR><BR>",
                        $l_team_youveleft,
                        $team['team_name']
                    ]);
                    defence_vs_defence($playerinfo['ship_id']);
                    kick_off_planet($playerinfo['ship_id'], $whichteam);
                    playerlog($playerinfo['ship_id'], LogTypeConstants::LOG_TEAM_LEAVE, $team['team_name']);
                    playerlog($team['creator'], LogTypeConstants::LOG_TEAM_NOT_LEAVE, $playerinfo['character_name']);
                }
            }
        } elseif ($confirmleave == 2) { // owner of a team is leaving and set a new owner
            $newcreatorname = db()->fetch("SELECT character_name FROM ships WHERE ship_id= :newcreator", [
                'newcreator' => $newcreator
            ]);
            echo sprintf(...[
                "%s <strong>%s</strong> %s %s.<BR><BR>",
                $l_team_youveleft,
                $team['team_name'],
                $l_team_relto,
                $newcreatorname['character_name']
            ]);
            db()->q("UPDATE ships SET team='0' WHERE ship_id= :ship_id", [
                'ship_id' => $playerinfo['ship_id']
            ]);
            db()->q("UPDATE ships SET team= :newcreator WHERE team= :creator", [
                'newcreator' => $newcreator,
                'creator' => $creator
            ]);
            db()->q("UPDATE teams SET number_of_members=number_of_members-1,creator= :newcreator WHERE id= :whichteam", [
                'newcreator' => $newcreator,
                'whichteam' => $whichteam
            ]);

            $sectors_res = db()->fetchAll("SELECT DISTINCT sector_id FROM planets WHERE owner= :owner AND base='Y' AND corp!=0", [
                'owner' => $playerinfo['ship_id']
            ]);
            $sectors = array();
            foreach ($sectors_res as $row) {
                $sectors[] = $row['sector_id'];
            }

            db()->q("UPDATE planets SET corp=0 WHERE owner= :owner", [
                'owner' => $playerinfo['ship_id']
            ]);
            if (!empty($sectors)) {
                foreach ($sectors as $sector) {
                    calc_ownership($sector);
                }
            }

            playerlog($playerinfo['ship_id'], LogTypeConstants::LOG_TEAM_NEWLEAD, sprintf("%s|%s", $team['team_name'], $newcreatorname['character_name']));
            playerlog($newcreator, LogTypeConstants::LOG_TEAM_LEAD, $team['team_name']);
        }

        LINK_BACK();
        break;
    case 3: // JOIN
        if ($playerinfo['team'] <> 0) {
            echo $l_team_leavefirst . "<BR>";
        } else {
            if ($playerinfo['team_invite'] == $whichteam) {
                db()->q("UPDATE ships SET team= :whichteam, team_invite=0 WHERE ship_id= :ship_id", [
                    'whichteam' => $whichteam,
                    'ship_id' => $playerinfo['ship_id']
                ]);
                db()->q("UPDATE teams SET number_of_members=number_of_members+1 WHERE id= :whichteam", [
                    'whichteam' => $whichteam
                ]);
                echo sprintf(...[
                    "%s <strong>%s</strong>.<BR><BR>",
                    $l_team_welcome,
                    $team['team_name']
                ]);
                playerlog($playerinfo['ship_id'], LogTypeConstants::LOG_TEAM_JOIN, $team['team_name']);
                playerlog($team['creator'], LogTypeConstants::LOG_TEAM_NEWMEMBER, sprintf("%s|%s", $team['team_name'], $playerinfo['character_name']));
            } else {
                echo "$l_team_noinviteto<BR>";
            }
        }
        LINK_BACK();
        break;
    case 4:
        /*
          Can you comment in english please ??

          // LEAVE + JOIN - anche per coordinatori - caso speciale ?
          // mettere nel 2 e senza break -> 3
          // CREATOR LEAVE - mettere come caso speciale si 3

         */
        echo "Not implemented yet. LEAVE+JOIN WE ARE A LAZY BUNCH sorry! :)<BR><BR>";
        LINK_BACK();
        break;

    case 5: // Eject member
        if ($playerinfo['team'] == $team['id']) {
            $who = stripnum($who);
            $whotoexpel = db()->fetch("SELECT * FROM ships WHERE ship_id= :who", [
                'who' => $who
            ]);
            if (!$confirmed) {
                echo sprintf(...[
                    "%s %s? <A HREF=\"teams.php?teamwhat=%s&confirmed=1&who=%s\">%s</A> - <a href=\"teams.php\">%s</a><BR>",
                    $l_team_ejectsure,
                    $whotoexpel['character_name'],
                    $teamwhat,
                    $who,
                    $l_yes,
                    $l_no
                ]);
            } else {
                db()->q("UPDATE planets SET corp='0' WHERE owner= :who", [
                    'who' => $who
                ]);
                db()->q("UPDATE ships SET team='0' WHERE ship_id= :who", [
                    'who' => $who
                ]);
                playerlog($who, LogTypeConstants::LOG_TEAM_KICK, $team['team_name']);
                echo $whotoexpel['character_name'] . " $l_team_ejected<BR>";
            }
            LINK_BACK();
        }
        break;

    case 6: // Create Team
        $teamname = fromRequest('teamname');
        if ($testing) {
            if ($swordfish != $adminpass) {
                echo "<FORM ACTION=\"teams.php\" METHOD=POST>";
                echo "$l_team_testing<BR><BR>";
                echo "$l_team_pw: <INPUT TYPE=PASSWORD NAME=swordfish SIZE=20 MAXLENGTH=20><BR><BR>";
                echo "<INPUT TYPE=hidden name=teamwhat value=$teamwhat>";
                echo "<INPUT TYPE=SUBMIT VALUE=$l_submit><INPUT TYPE=RESET VALUE=$l_reset>";
                echo "</FORM>";
                echo "<BR><BR>";

                include("footer.php");
                die();
            }
        }
        if (!$teamname) {
            echo "<FORM ACTION=\"teams.php\" METHOD=POST>";
            echo "$l_team_entername: ";
            if ($testing) {
                echo "<INPUT TYPE=hidden NAME=swordfish value='$swordfish'>";
            }
            echo "<INPUT TYPE=hidden name=teamwhat value=$teamwhat>";
            echo "<INPUT TYPE=TEXT NAME=teamname SIZE=40 MAXLENGTH=40><BR>";
            echo "$l_team_enterdesc: ";
            echo "<INPUT TYPE=TEXT NAME=teamdesc SIZE=40 MAXLENGTH=254><BR>";
            echo "<INPUT TYPE=SUBMIT VALUE=$l_submit><INPUT TYPE=RESET VALUE=$l_reset>";
            echo "</FORM>";
            echo "<BR><BR>";
        } else {
            $teamname = htmlspecialchars($teamname);
            $teamdesc = htmlspecialchars($teamdesc);
            db()->q("INSERT INTO teams (creator,team_name,number_of_members,description) VALUES (:ship_id,:teamname,'1',:teamdesc)", [
                'ship_id' => $playerinfo['ship_id'],
                'teamname' => $teamname,
                'teamdesc' => $teamdesc
            ]);
            db()->q("INSERT INTO zones VALUES(NULL, :zone_name, :ship_id, 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 0)", [
                'zone_name' => sprintf("%s's Empire", $teamname),
                'ship_id' => $playerinfo['ship_id']
            ]);
            db()->q("UPDATE ships SET team= :ship_id WHERE ship_id= :ship_id", [
                'ship_id' => $playerinfo['ship_id']
            ]);
            echo sprintf("%s <strong>%s</strong> %s<BR><BR>", $l_team_alliance, $teamname, $l_team_hcreated);
            playerlog($playerinfo['ship_id'], LogTypeConstants::LOG_TEAM_CREATE, $teamname);
        }
        LINK_BACK();
        break;
    case 7: // INVITE player
        if (!$invited) {
            echo "<FORM ACTION='teams.php' METHOD=POST>";
            echo "<INPUT TYPE=hidden name=teamwhat value=$teamwhat><INPUT TYPE=hidden name=invited value=1><INPUT TYPE=hidden name=whichteam value=$whichteam>";
            echo "<label>$l_team_selectp:</label> <SELECT NAME=who>";
            $players = db()->fetchAll("SELECT character_name,ship_id FROM ships WHERE team<> :whichteam ORDER BY character_name ASC", [
                'whichteam' => $whichteam
            ]);
            foreach ($players as $row) {
                if ($row['ship_id'] != $team['creator']) {
                    echo "<OPTION VALUE=" . $row['ship_id'] . ">" . $row['character_name'];
                }
            }
            echo "</SELECT>";
            echo "<INPUT TYPE=SUBMIT VALUE=$l_submit>";
            echo "</FORM>";
        } else {
            if ($playerinfo['team'] == $whichteam) {
                $newpl = db()->fetch("SELECT character_name,team_invite FROM ships WHERE ship_id= :who", [
                    'who' => $who
                ]);
                if ($newpl['team_invite']) {
                    $l_team_isorry = str_replace("[name]", $newpl['character_name'], $l_team_isorry);
                    echo "$l_team_isorry<BR><BR>";
                } else {
                    db()->q("UPDATE ships SET team_invite= :whichteam WHERE ship_id= :who", [
                        'whichteam' => $whichteam,
                        'who' => $who
                    ]);
                    echo("$l_team_plinvted<BR>");
                    playerlog($who, LogTypeConstants::LOG_TEAM_INVITE, $team['team_name']);
                }
            } else {
                echo "$l_team_notyours<BR>";
            }
        }
        echo "<BR><BR><a href=\"teams.php\">$l_clickme</a> $l_team_menu<BR><BR>";
        break;
    case 8: // REFUSE invitation
        echo sprintf("%s <strong>%s</strong>.<BR><BR>", $l_team_refuse, $invite_info['team_name']);
        db()->q("UPDATE ships SET team_invite=0 WHERE ship_id= :ship_id", [
            'ship_id' => $playerinfo['ship_id']
        ]);
        playerlog($team['creator'], LogTypeConstants::LOG_TEAM_REJECT, sprintf("%s|%s", $playerinfo['character_name'], $invite_info['team_name']));
        LINK_BACK();
        break;
    case 9: // Edit Team
        if ($testing) {
            if ($swordfish != $adminpass) {
                echo "<FORM ACTION=\"teams.php\" METHOD=POST>";
                echo "$l_team_testing<BR><BR>";
                echo "$l_team_pw: <INPUT TYPE=PASSWORD NAME=swordfish SIZE=20 MAXLENGTH=20><BR><BR>";
                echo "<INPUT TYPE=hidden name=teamwhat value=$teamwhat>";
                echo "<INPUT TYPE=SUBMIT VALUE=$l_submit><INPUT TYPE=RESET VALUE=$l_reset>";
                echo "</FORM>";
                echo "<BR><BR>";

                include("footer.php");
                die();
            }
        }
        if ($playerinfo['team'] == $whichteam) {
            if (!$update) {
                echo "<FORM ACTION=\"teams.php\" METHOD=POST>";
                echo "$l_team_edname: <BR>";
                echo "<INPUT TYPE=hidden NAME=swordfish value='$swordfish'>";
                echo "<INPUT TYPE=hidden name=teamwhat value=$teamwhat>";
                echo "<INPUT TYPE=hidden name=whichteam value=$whichteam>";
                echo "<INPUT TYPE=hidden name=update value=true>";
                echo "<INPUT TYPE=TEXT NAME=teamname SIZE=40 MAXLENGTH=40 VALUE=\"" . $team['team_name'] . "\"><BR>";
                echo "$l_team_eddesc: <BR>";
                echo "<INPUT TYPE=TEXT NAME=teamdesc SIZE=40 MAXLENGTH=254 VALUE=\"" . $team['description'] . "\"><BR>";
                echo "<INPUT TYPE=SUBMIT VALUE=$l_submit><INPUT TYPE=RESET VALUE=$l_reset>";
                echo "</FORM>";
                echo "<BR><BR>";
            } else {
                $teamname = htmlspecialchars($teamname);
                $teamdesc = htmlspecialchars($teamdesc);
                db()->q("UPDATE teams SET team_name= :teamname, description= :teamdesc WHERE id= :whichteam", [
                    'teamname' => $teamname,
                    'teamdesc' => $teamdesc,
                    'whichteam' => $whichteam
                ]);
                echo sprintf("%s <strong>%s</strong> %s<BR><BR>", $l_team_alliance, $teamname, $l_team_hasbeenr);
                /*
                  Adding a log entry to all members of the renamed alliance
                 */
                $team_members = db()->fetchAll("SELECT ship_id FROM ships WHERE team= :whichteam AND ship_id<> :ship_id", [
                    'whichteam' => $whichteam,
                    'ship_id' => $playerinfo['ship_id']
                ]);
                playerlog($playerinfo['ship_id'], LogTypeConstants::LOG_TEAM_RENAME, $teamname);
                foreach ($team_members as $teamname_array) {
                    playerlog($teamname_array['ship_id'], LogTypeConstants::LOG_TEAM_M_RENAME, $teamname);
                }
            }
            LINK_BACK();
            break;
        } else {
            echo $l_team_error;
            LINK_BACK();
            break;
        }
    default:
        if (!$playerinfo['team']) {
            echo "$l_team_notmember";
            DISPLAY_INVITE_INFO();
        } else {
            if ($playerinfo['team'] < 0) {
                $playerinfo['team'] = -$playerinfo['team'];
                $whichteam = db()->fetch("SELECT * FROM teams WHERE id= :team", [
                    'team' => $playerinfo['team']
                ]);
                echo sprintf("%s <strong>%s</strong><BR><BR>", $l_team_urejected, $whichteam['team_name']);
                LINK_BACK();
                break;
            }
            $whichteam = db()->fetch("SELECT * FROM teams WHERE id= :team", [
                'team' => $playerinfo['team']
            ]);
            if ($playerinfo['team_invite']) {
                $whichinvitingteam = db()->fetch("SELECT * FROM teams WHERE id= :team_invite", [
                    'team_invite' => $playerinfo['team_invite']
                ]);
            }
            $isowner = $playerinfo['ship_id'] == $whichteam['creator'];
            showinfo($playerinfo['team'], $isowner);
        }
        $num_res = db()->fetch("SELECT COUNT(*) as TOTAL FROM teams");
        if ($num_res['TOTAL'] > 0) {
            DISPLAY_ALL_ALLIANCES();
        } else {
            echo "$l_team_noalliances<BR><BR>";
        }
        break;
} // switch ($teamwhat)

echo "<BR><BR>";

include("footer.php");