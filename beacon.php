<?
include("config.php");
updatecookie();

include_once($gameroot . "/languages/$lang");
$title=$l_beacon_title;
include("header.php");

connectdb();

if (checklogin()) {die();}

$result = $db->Execute ("SELECT * FROM $dbtables[ships] WHERE email='$username'");
$playerinfo=$result->fields;

$result2 = $db->Execute ("SELECT * FROM $dbtables[universe] WHERE sector_id='$playerinfo[sector]'");
$sectorinfo=$result2->fields;

bigtitle();

if($playerinfo[dev_beacon] > 0)
{
  $res = $db->Execute("SELECT allow_beacon FROM $dbtables[zones] WHERE zone_id='$sectorinfo[zone_id]'");
  $zoneinfo = $res->fields;
  if($zoneinfo[allow_beacon] == 'N')
  {
    echo "$l_beacon_notpermitted<BR><BR>";
  }
  else
  {
    if($beacon_text == "")
    {
      if($sectorinfo[beacon] != "")
      {
        echo "$l_beacon_reads: \"$sectorinfo[beacon]\"<BR><BR>";
      }
      else
      {
        echo "$l_beacon_none<BR><BR>";
      }
      echo"<form action=beacon.php method=post>";
      echo"<table>";
      echo"<tr><td>$l_beacon_enter:</td><td><input type=text name=beacon_text size=40 maxlength=80></td></tr>";
      echo"</table>";
      echo"<input type=submit value=$l_submit><input type=reset value=$l_reset>";
      echo"</form>";
    }
    else
    {
      $beacon_text = trim(strip_tags($beacon_text));
      echo "$l_beacon_nowreads: \"$beacon_text\".<BR><BR>";
      $update = $db->Execute("UPDATE $dbtables[universe] SET beacon='$beacon_text' WHERE sector_id=$sectorinfo[sector_id]");
      $update = $db->Execute("UPDATE $dbtables[ships] SET dev_beacon=dev_beacon-1 WHERE ship_id=$playerinfo[ship_id]");
    }
  }
}
else
{
  echo "$l_beacon_donthave<BR><BR>";
}

TEXT_GOTOMAIN();

include("footer.php");

?>
