<?


include("config.php");
updatecookie();

include_once($gameroot . "/languages/$lang");


$title=$l_warp_title;
include("header.php");

connectdb();

if(checklogin())
{
  die();
}

$result = $db->Execute("SELECT * FROM $dbtables[ships] WHERE email='$username'");
$playerinfo=$result->fields;

bigtitle();

if($playerinfo[turns] < 1)
{
  echo "$l_warp_turn<BR><BR>";
  TEXT_GOTOMAIN();
  include("footer.php");
  die();
}

if($playerinfo[dev_warpedit] < 1)
{
  echo "$l_warp_none<BR><BR>";
  TEXT_GOTOMAIN();
  include("footer.php");
  die();
}

$res = $db->Execute("SELECT allow_warpedit,$dbtables[universe].zone_id FROM $dbtables[zones],$dbtables[universe] WHERE sector_id=$playerinfo[sector] AND $dbtables[universe].zone_id=$dbtables[zones].zone_id");
$zoneinfo = $res->fields;
if($zoneinfo[allow_warpedit] == 'N')
{
  echo "$l_warp_forbid<BR><BR>";
  TEXT_GOTOMAIN();
  include("footer.php");
  die();
}

$result2 = $db->Execute("SELECT * FROM $dbtables[links] WHERE link_start=$playerinfo[sector] ORDER BY link_dest ASC");
if($result2 < 1)
{
  echo "$l_warp_nolink<BR><BR>";
}
else
{
  echo "$l_warp_linkto ";
  while(!$result2->EOF)
  {
    echo $result2->fields[link_dest] . " ";
    $result2->MoveNext();
  }
  echo "<BR><BR>";
}

echo "<form action=\"warpedit2.php\" method=\"post\">";
echo "<table>";
echo "<tr><td>$l_warp_query</td><td><input type=\"text\" name=\"target_sector\" size=\"6\" maxlength=\"6\" value=\"\"></td></tr>";
echo "<tr><td>$l_warp_oneway?</td><td><input type=\"checkbox\" name=\"oneway\" value=\"oneway\"></td></tr>";
echo "</table>";
echo "<input type=\"submit\" value=\"$l_submit\"><input type=\"reset\" value=\"$l_reset\">";
echo "</form>";
echo "<BR><BR>$l_warp_dest<BR><BR>";
echo "<form action=\"warpedit3.php\" method=\"post\">";
echo "<table>";
echo "<tr><td>$l_warp_destquery</td><td><input type=\"text\" name=\"target_sector\" size=\"6\" maxlength=\"6\" value=\"\"></td></tr>";
echo "<tr><td>$l_warp_bothway?</td><td><input type=\"checkbox\" name=\"bothway\" value=\"bothway\"></td></tr>";
echo "</table>";
echo "<input type=\"submit\" value=\"$l_submit\"><input type=\"reset\" value=\"$l_reset\">";
echo "</form>";

TEXT_GOTOMAIN();

include("footer.php");

?>