<?
include("config.php3");
updatecookie();

include($gameroot . $default_lang);
$title=$l_md_title;
include("header.php3");

connectdb();

if(checklogin())
{
  die();
}

//-------------------------------------------------------------------------------------------------

If(!isset($defence_id))
{
   echo "$l_md_invalid<BR>";
   TEXT_GOTOMAIN();
   die();
}

$res = mysql_query("SELECT * FROM ships WHERE email='$username'");
$playerinfo = mysql_fetch_array($res);
mysql_free_result($res);
$res = mysql_query("SELECT * from universe WHERE sector_id=$playerinfo[sector]");
$sectorinfo = mysql_fetch_array($res);
mysql_free_result($res);

$result3 = mysql_query ("SELECT * FROM sector_defence WHERE defence_id=$defence_id ");
//Put the defence information into the array "defenceinfo"

if($result3 == 0)
{
   echo "$l_md_nolonger<BR>";
   TEXT_GOTOMAIN();
   die();
}
$defenceinfo = mysql_fetch_array($result3);
if($defenceinfo['ship_id'] == $playerinfo['ship_id'])
{
   $defence_owner = $l_md_you;
}
else
{
   $defence_ship_id = $defenceinfo['ship_id'];
   $resulta = mysql_query ("SELECT * FROM ships WHERE ship_id = $defence_ship_id ");
   $ownerinfo = mysql_fetch_array($resulta);
   $defence_owner = $ownerinfo['character_name'];
}
$defence_type = $defenceinfo['defence_type'] == 'F' ? $l_fighters : $l_mines;
$qty = $defenceinfo['quantity'];
if($defenceinfo['fm_setting'] == 'attack')
{
   $set_attack = 'CHECKED';
   $set_toll = '';
}
else
{
   $set_attack = '';
   $set_toll = 'CHECKED';
}

switch($response) {
   case "fight":
      bigtitle();
      $sector = $playerinfo[sector] ;
      if($defenceinfo['defence_type'] == 'F')
      {
         $countres = mysql_query("SELECT SUM(quantity) as totalfighters FROM sector_defence where sector_id = $sector and defence_type = 'F'");
         $ttl = mysql_fetch_array($countres);
         $total_sector_fighters = $ttl['totalfighters'];
         include("sector_fighters.php3");
      }
      else
      {
          // Attack mines goes here
         $countres = mysql_query("SELECT SUM(quantity) as totalmines FROM sector_defence where sector_id = $sector and defence_type = 'M'");
         $ttl = mysql_fetch_array($countres);
         $total_sector_mines = $ttl['totalmines'];
         $playerbeams = NUM_BEAMS($playerinfo[beams]);
         if($playerbeams>$playerinfo[ship_energy])
         {
            $playerbeams=$playerinfo[ship_energy];
         }
         if($playerbeams>$total_sector_mines)
         {
            $playerbeams=$total_sector_mines;
         }
         echo "$l_md_bmines $playerbeams $l_mines<BR>";
         $update4b = mysql_query ("UPDATE ships SET ship_energy=energy-$playerbeams WHERE ship_id=$playerinfo[ship_id]");
         explode_mines($sector,$playerbeams);
         $char_name = $playerinfo['character_name'];
         $l_md_msgdownerb=str_replace("[sector]",$sector,$l_md_msgdownerb);
         $l_md_msgdownerb=str_replace("[mines]",$playerbeams,$l_md_msgdownerb);
         $l_md_msgdownerb=str_replace("[name]",$char_name,$l_md_msgdownerb);
         message_defence_owner($sector,"$l_md_msgdownerb");
         TEXT_GOTOMAIN();
         die();
      }
      break;
   case "retrieve":
      if($quantity < 0) $quantity = 0;
      if($quantity > $defenceinfo['quantity'])
      {
         $quantity = $defenceinfo['quantity'];
      }
      $torpedo_max = NUM_TORPEDOES($playerinfo[torp_launchers]) - $playerinfo[torps];
      $fighter_max = NUM_FIGHTERS($playerinfo[computer]) - $playerinfo[ship_fighters];
      if($defenceinfo['defence_type'] == 'F')
      {
         if($quantity > $fighter_max)
         {
            $quantity = $fighter_max;
         }
      }
      if($defenceinfo['defence_type'] == 'M')
      {
         if($quantity > $torpedo_max)
         {
            $quantity = $torpedo_max;
         }
      }
      $ship_id = $playerinfo[ship_id];
      if($quantity > 0)
      {
         mysql_query("UPDATE sector_defence SET quantity=quantity - $quantity WHERE defence_id = $defence_id");
         if($defenceinfo['defence_type'] == 'M')
         {
            mysql_query("UPDATE ships set torps=torps + $quantity where ship_id = $ship_id");
         }
         else
         {
            mysql_query("UPDATE ships set ship_fighters=ship_fighters + $quantity where ship_id = $ship_id");
         }
         mysql_query("DELETE FROM sector_defence WHERE quantity <= 0");
      }
      $stamp = date("Y-m-d H-i-s");

      mysql_query("UPDATE ships SET last_login='$stamp',turns=turns-1, turns_used=turns_used+1, sector=$playerinfo[sector] where ship_id=$playerinfo[ship_id]");
      bigtitle();
      echo "$l_md_retr $quantity $defence_type.<BR>";
      TEXT_GOTOMAIN();
      die();
      break;
   case "change":
      bigtitle();
      mysql_query("UPDATE sector_defence SET fm_setting = '$mode' where defence_id = $defence_id");
      $stamp = date("Y-m-d H-i-s");
      mysql_query("UPDATE ships SET last_login='$stamp',turns=turns-1, turns_used=turns_used+1, sector=$playerinfo[sector] where ship_id=$playerinfo[ship_id]");
      $l_md_mode=str_replace("[mode]",$mode,$l_md_mode);
      echo "$l_md_mode<BR>";
      TEXT_GOTOMAIN();
      die();
      break;
   default:
      bigtitle();
      $l_md_consist=str_replace("[qty]",$qty,$l_md_consist);
      $l_md_consist=str_replace("[type]",$defence_type,$l_md_consist);
      $l_md_consist=str_replace("[owner]",$defence_owner,$l_md_consist);
      echo "$l_md_consist<BR>";

      if($defenceinfo['ship_id'] == $playerinfo['ship_id'])
      {
         echo "$l_md_youcan:<BR>";
         echo "<FORM ACTION=modify-defences.php METHOD=POST>";
         echo "$l_md_retrieve <INPUT TYPE=TEST NAME=quantity SIZE=10 MAXLENGTH=10 VALUE=0></INPUT> $defence_type<BR>";
         echo "<input type=hidden name=response value=retrieve>";
         echo "<input type=hidden name=defence_id value=$defence_id>";
         echo "<INPUT TYPE=SUBMIT VALUE=$l_submit><BR><BR>";
         echo "</FORM>";
         if($defenceinfo['defence_type'] == 'F')
         {
            echo "$l_md_change:<BR>";
            echo "<FORM ACTION=modify-defences.php METHOD=POST>";
            echo "$l_md_cmode <INPUT TYPE=RADIO NAME=mode $set_attack VALUE=attack>$l_md_attack</INPUT>";
            echo "<INPUT TYPE=RADIO NAME=mode $set_toll VALUE=toll>$l_md_toll</INPUT><BR>";
            echo "<INPUT TYPE=SUBMIT VALUE=$l_submit><BR><BR>";
            echo "<input type=hidden name=response value=change>";
            echo "<input type=hidden name=defence_id value=$defence_id>";
            echo "</FORM>";
         }
      }
      else
      {
         $ship_id = $defenceinfo['ship_id'];
         $result2 = mysql_query("SELECT * from ships where ship_id=$ship_id");
         $fighters_owner = mysql_fetch_array($result2);
         mysql_free_result($result2);

         if($fighters_owner[team] != $playerinfo[team] || $playerinfo[team] == 0)
         {
            echo "$l_youcan:<BR>";
            echo "<FORM ACTION=modify-defences.php METHOD=POST>";
            echo "$l_md_attdef<BR><INPUT TYPE=SUBMIT VALUE=$l_md_attack></INPUT><BR>";
            echo "<input type=hidden name=response value=fight>";
            echo "<input type=hidden name=defence_id value=$defence_id>";
            echo "</FORM>";
         }
      }
      TEXT_GOTOMAIN();
      die();
      break;
}


//-------------------------------------------------------------------------------------------------

TEXT_GOTOMAIN();

include("footer.php3");

?>