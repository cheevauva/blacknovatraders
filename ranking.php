<?php
use BNT\Cache;
include("config.php");
updatecookie();

loadlanguage($lang);
$title=$l_ranks_title;
include("header.php");

connectdb();
bigtitle();
$item = Cache::instance()->getItem('test');
if ($item->isHit()) {
    var_dump($item->get());
} else {
    $item->set(12);
    Cache::instance()->save($item);
    die;
}
//-------------------------------------------------------------------------------------------------

$res = $db->Execute("SELECT COUNT(*) AS num_players FROM ships WHERE ship_destroyed='N' and email NOT LIKE '%@xenobe'");
$row = $res->fields;
$num_players = $row['num_players'];

if($sort=="turns")
{
  $by="turns_used DESC,character_name ASC";
}
elseif($sort=="login")
{
  $by="last_login DESC,character_name ASC";
}
elseif($sort=="good")
{
  $by="rating DESC,character_name ASC";
}
elseif($sort=="bad")
{
  $by="rating ASC,character_name ASC";
}
elseif($sort=="alliance")
{
  $by="teams.team_name DESC, character_name ASC";
}
elseif($sort=="efficiency")
{
  $by="efficiency DESC";
}
else
{
  $by="score DESC,character_name ASC";
}

$res = $db->Execute("SELECT ships.email,ships.score,ships.character_name,ships.turns_used,ships.last_login,UNIX_TIMESTAMP(ships.last_login) as online,ships.rating, teams.team_name, IF(ships.turns_used<150,0,ROUND(ships.score/ships.turns_used)) AS efficiency FROM ships LEFT JOIN teams ON ships.team = teams.id  WHERE ship_destroyed='N' and email NOT LIKE '%@xenobe' ORDER BY $by LIMIT $max_rank");

//-------------------------------------------------------------------------------------------------

if(!$res)
{
  echo "$l_ranks_none<BR>";
}
else
{
  echo "<BR>$l_ranks_pnum: " . NUMBER($num_players);
  echo "<BR>$l_ranks_dships<BR><BR>";
  echo "<TABLE>";
  echo "<TR ><TD><B>$l_ranks_rank</B></TD><TD><B><A HREF=\"ranking.php\">$l_score</A></B></TD><TD><B>$l_player</B></TD><TD><B><A HREF=\"ranking.php?sort=turns\">$l_turns_used</A></B></TD><TD><B><A HREF=\"ranking.php?sort=login\">$l_ranks_lastlog</A></B></TD><TD><B><A HREF=\"ranking.php?sort=good\">$l_ranks_good</A>/<A HREF=\"ranking.php?sort=bad\">$l_ranks_evil</A></B></TD><TD><B><A HREF=\"ranking.php?sort=alliance\">$l_team_alliance</A></B></TD><TD><B><A HREF=\"ranking.php?sort=online\">Online</A></B></TD><TD><B><A HREF=\"ranking.php?sort=efficiency\">Eff. Rating.</A></B></TD></TR>\n";
  $color = $color_line1;
  $i = 0;
  while(!$res->EOF)
  {
    $row = $res->fields;
    $i++;
    $rating=round(sqrt( abs($row['rating']) ));
    if(abs($row['rating'])!=$row['rating'])
    {
      $rating=-1*$rating;
    }
    $curtime = TIME();
    $time = $row['online'];
    $difftime = ($curtime - $time) / 60;
    $temp_turns = $row['turns_used'];
    if ($temp_turns <= 0)
    {
    $temp_turns = 1;
    }
    $online = " ";
    if($difftime <= 5) $online = "Online";
    echo "<TR><TD>" . NUMBER($i) . "</TD><TD>" . NUMBER($row['score']) . "</TD><TD>";
    echo "&nbsp;";
    echo player_insignia_name($row['email']);
    echo "&nbsp;";
    echo "<b>$row[character_name]</b></TD><TD>" . NUMBER($row[turns_used]) . "</TD><TD>$row[last_login]</TD><TD>&nbsp;&nbsp;" . NUMBER($rating) . "</TD><TD>$row[team_name]&nbsp;</TD><TD>$online</TD><TD>$row[efficiency]</TD></TR>\n";
    if($color == $color_line1)
    {
      $color = $color_line2;
    }
    else
    {
      $color = $color_line1;
    }
    $res->MoveNext();
  }
  echo "</TABLE>";
}

echo "<BR>";

if(empty($username))
{
  TEXT_GOTOLOGIN();
}
else
{
  TEXT_GOTOMAIN();
}

include("footer.php");

?>
