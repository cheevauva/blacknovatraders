<?
include("config.php");
updatecookie();


include("languages/$lang");
$title = $l_title_port;
include("header.php");

connectdb();

if(checklogin())
{
  die();
}

//-------------------------------------------------------------------------------------------------


$res = $db->Execute("SELECT * FROM $dbtables[players] WHERE email='$username'");
$playerinfo = $res->fields;

$res = $db->Execute("SELECT * FROM $dbtables[ships] WHERE player_id=$playerinfo[player_id] AND ship_id=$playerinfo[currentship]");
$shipinfo = $res->fields;

$res = $db->Execute("SELECT * FROM $dbtables[ship_types] WHERE type_id=$shipinfo[class]");
$classinfo = $res->fields;

// fix negative quantities, i guess theres a better way to do but i'm in a hurry
// i dont know how the quantities acutally get negative ...

if ($shipinfo[ore]<0)
{
  $fixres = $db->Execute("UPDATE $dbtables[ships] set ore=0 WHERE ship_id=$shipinfo[ship_id]");
  $shipinfo[ore] = 0;
}

if ($shipinfo[organics]<0)
{
  $fixres = $db->Execute("UPDATE $dbtables[ships] set organics=0 WHERE ship_id=$shipinfo[ship_id]");
  $shipinfo[organics] = 0;
}

if ($shipinfo[energy]<0)
{
  $fixres = $db->Execute("UPDATE $dbtables[ships] set energy=0 WHERE ship_id=$shipinfo[ship_id]");
  $shipinfo[energy] = 0;
}

if ($shipinfo[goods]<0)
{
  $fixres = $db->Execute("UPDATE $dbtables[ships] set goods=0 WHERE ship_id=$shipinfo[ship_id]");
  $shipinfo[goods] = 0;
}

$res = $db->Execute("SELECT * FROM $dbtables[universe] WHERE sector_id='$shipinfo[sector_id]'");
$sectorinfo = $res->fields;

if ($sectorinfo[port_ore]<0)
{
  $fixres = $db->Execute("UPDATE $dbtables[universe] set port_ore=0 WHERE sector_id=$shipinfo[sector_id]");
  $sectorinfo[port_ore] = 0;
}

if ($sectorinfo[port_goods]<0)
{
  $fixres = $db->Execute("UPDATE $dbtables[universe] set port_goods=0 WHERE sector_id=$shipinfo[sector_id]");
  $sectorinfo[port_goods] = 0;
}

if ($sectorinfo[port_organics]<0)
{
  $fixres = $db->Execute("UPDATE $dbtables[universe] set port_organics=0 WHERE sector_id=$shipinfo[sector_id]");
  $sectorinfo[port_organics] = 0;
}

if ($sectorinfo[port_energy]<0)
{
  $fixres = $db->Execute("UPDATE $dbtables[universe] set port_energy=0 WHERE sector_id=$shipinfo[sector_id]");
  $sectorinfo[port_energy] = 0;
}


$res = $db->Execute("SELECT * FROM $dbtables[zones] WHERE zone_id=$sectorinfo[zone_id]");

$zoneinfo = $res->fields;

if($zoneinfo[zone_id] == 4)
{
  $title=$l_sector_war;
  bigtitle();
  echo "$l_war_info <p>";
  TEXT_GOTOMAIN();
  include("footer.php");
  die();
}
elseif($zoneinfo[allow_trade] == 'N')
{
  $title="Trade forbidden";
  bigtitle();
  echo "$l_no_trade_info<p>";
  TEXT_GOTOMAIN();
  include("footer.php");
  die();
}

elseif($zoneinfo[allow_trade] == 'L')
{
  if($zoneinfo[corp_zone] == 'N')
  {
    $res = $db->Execute("SELECT team FROM $dbtables[players] WHERE player_id=$zoneinfo[owner]");
    $ownerinfo = $res->fields;

    if($playerinfo[player_id] != $zoneinfo[owner] && $playerinfo[team] == 0 || $playerinfo[team] != $ownerinfo[team])
    {
      $title="Trade forbidden";
      bigtitle();
      echo "Trading at this port is not allowed for outsiders<p>";
      TEXT_GOTOMAIN();
      include("footer.php");
      die();
    }
  }
  else
  {
    if($playerinfo[team] != $zoneinfo[owner])
    {
      $title=$l_no_trade;
      bigtitle();
      echo "$l_no_trade_out<p>";
      TEXT_GOTOMAIN();
      include("footer.php");
      die();
    }
  }
}

//-------------------------------------------------------------------------------------------------

if($sectorinfo[port_type] != "none" && $sectorinfo[port_type] != "special")
{
  $title=$l_title_trade;
  bigtitle();

  if($sectorinfo[port_type] == "ore")
  {
    $ore_price = $ore_price - $ore_delta * $sectorinfo[port_ore] / $ore_limit * $inventory_factor;
    $sb_ore = $l_selling;
  }
  else
  {
    $ore_price = $ore_price + $ore_delta * $sectorinfo[port_ore] / $ore_limit * $inventory_factor;
    $sb_ore = $l_buying;
  }

  if($sectorinfo[port_type] == "organics")
  {
    $organics_price = $organics_price - $organics_delta * $sectorinfo[port_organics] / $organics_limit * $inventory_factor;
    $sb_organics = $l_selling;
  }
  else
  {
    $organics_price = $organics_price + $organics_delta * $sectorinfo[port_organics] / $organics_limit * $inventory_factor;
    $sb_organics = $l_buying;
  }

  if($sectorinfo[port_type] == "goods")
  {
    $goods_price = $goods_price - $goods_delta * $sectorinfo[port_goods] / $goods_limit * $inventory_factor;
    $sb_goods = $l_selling;
  }
  else
  {
    $goods_price = $goods_price + $goods_delta * $sectorinfo[port_goods] / $goods_limit * $inventory_factor;
    $sb_goods = $l_buying;
  }

  if($sectorinfo[port_type] == "energy")
  {
    $energy_price = $energy_price - $energy_delta * $sectorinfo[port_energy] / $energy_limit * $inventory_factor;
    $sb_energy = $l_selling;
  }
  else
  {
    $energy_price = $energy_price + $energy_delta * $sectorinfo[port_energy] / $energy_limit * $inventory_factor;
    $sb_energy = $l_buying;
  }

  // establish default amounts for each commodity
  if($sb_ore == $l_buying)
  {
    $amount_ore = $shipinfo[ore];
  }
  else
  {
    $amount_ore = NUM_HOLDS($shipinfo[hull]) - $shipinfo[ore] - $shipinfo[colonists];
  }

  if($sb_organics == $l_buying)
  {
    $amount_organics = $shipinfo[organics];
  }
  else
  {
    $amount_organics = NUM_HOLDS($shipinfo[hull]) - $shipinfo[organics] - $shipinfo[colonists];
  }

  if($sb_goods == $l_buying)
  {
    $amount_goods = $shipinfo[goods];
  }
  else
  {
    $amount_goods = NUM_HOLDS($shipinfo[hull]) - $shipinfo[goods] - $shipinfo[colonists];
  }

  if($sb_energy == $l_buying)
  {
    $amount_energy = $shipinfo[energy];
  }
  else
  {
    $amount_energy = NUM_ENERGY($shipinfo[power]) - $shipinfo[energy];
  }

  // limit amounts to port quantities
  $amount_ore = min($amount_ore, $sectorinfo[port_ore]);
  $amount_organics = min($amount_organics, $sectorinfo[port_organics]);
  $amount_goods = min($amount_goods, $sectorinfo[port_goods]);
  $amount_energy = min($amount_energy, $sectorinfo[port_energy]);

  // limit amounts to what the player can afford
  if($sb_ore == $l_selling)
  {
    $amount_ore = min($amount_ore, floor(($playerinfo[credits] + $amount_organics * $organics_price + $amount_goods * $goods_price + $amount_energy * $energy_price) / $ore_price));
  }
  if($sb_organics == $l_selling)
  {
    $amount_organics = min($amount_organics, floor(($playerinfo[credits] + $amount_ore * $ore_price + $amount_goods * $goods_price + $amount_energy * $energy_price) / $organics_price));
  }
  if($sb_goods == $l_selling)
  {
    $amount_goods = min($amount_goods, floor(($playerinfo[credits] + $amount_ore * $ore_price + $amount_organics * $organics_price + $amount_energy * $energy_price) / $goods_price));
  }
  if($sb_energy == $l_selling)
  {
    $amount_energy = min($amount_energy, floor(($playerinfo[credits] + $amount_ore * $ore_price + $amount_organics * $organics_price + $amount_goods * $goods_price) / $energy_price));
  }

  echo "<FORM ACTION=port2.php METHOD=POST>";
  echo "<TABLE WIDTH=\"100%\" BORDER=0 CELLSPACING=0 CELLPADDING=0>";
  echo "<TR BGCOLOR=\"$color_header\"><TD><B>$l_commodity</B></TD><TD><B>$l_buying/$l_selling</B></TD><TD><B>$l_amount</B></TD><TD><B>$l_price</B></TD><TD><B>$l_buy/$l_sell</B></TD><TD><B>$l_cargo</B></TD></TR>";
  echo "<TR BGCOLOR=\"$color_line1\"><TD>$l_ore</TD><TD>$sb_ore</TD><TD>" . NUMBER($sectorinfo[port_ore]) . "</TD><TD>$ore_price</TD><TD><INPUT TYPE=TEXT NAME=trade_ore SIZE=10 MAXLENGTH=20 VALUE=$amount_ore></TD><TD>" . NUMBER($shipinfo[ore]) . "</TD></TR>";
  echo "<TR BGCOLOR=\"$color_line2\"><TD>$l_organics</TD><TD>$sb_organics</TD><TD>" . NUMBER($sectorinfo[port_organics]) . "</TD><TD>$organics_price</TD><TD><INPUT TYPE=TEXT NAME=trade_organics SIZE=10 MAXLENGTH=20 VALUE=$amount_organics></TD><TD>" . NUMBER($shipinfo[organics]) . "</TD></TR>";
  echo "<TR BGCOLOR=\"$color_line1\"><TD>$l_goods</TD><TD>$sb_goods</TD><TD>" . NUMBER($sectorinfo[port_goods]) . "</TD><TD>$goods_price</TD><TD><INPUT TYPE=TEXT NAME=trade_goods SIZE=10 MAXLENGTH=20 VALUE=$amount_goods></TD><TD>" . NUMBER($shipinfo[goods]) . "</TD></TR>";
  echo "<TR BGCOLOR=\"$color_line2\"><TD>$l_energy</TD><TD>$sb_energy</TD><TD>" . NUMBER($sectorinfo[port_energy]) . "</TD><TD>$energy_price</TD><TD><INPUT TYPE=TEXT NAME=trade_energy SIZE=10 MAXLENGTH=20 VALUE=$amount_energy></TD><TD>" . NUMBER($shipinfo[energy]) . "</TD></TR>";
  echo "</TABLE><BR>";
  echo "<INPUT TYPE=SUBMIT VALUE=$l_trade>";
  echo "</FORM>";

  $free_holds = NUM_HOLDS($shipinfo[hull]) - $shipinfo[ore] - $shipinfo[organics] - $shipinfo[goods] - $shipinfo[colonists];
  $free_power = NUM_ENERGY($shipinfo[power]) - $shipinfo[energy];

 $l_trade_st_info=str_replace("[free_holds]",NUMBER($free_holds),$l_trade_st_info);
 $l_trade_st_info=str_replace("[free_power]",NUMBER($free_power),$l_trade_st_info);
 $l_trade_st_info=str_replace("[credits]",NUMBER($playerinfo[credits]),$l_trade_st_info);

 echo $l_trade_st_info;

}
elseif($sectorinfo[port_type] == "special")
{
  $title=$l_special_port;
  bigtitle();
  if(isLoanPending($playerinfo[player_id]))
  {
    echo "$l_port_loannotrade<p>";
    echo "<A HREF=IGB.php>$l_igb_term</a><p>";
    TEXT_GOTOMAIN();
    include("footer.php");
    die();
  }

  $res2 = $db->Execute("SELECT SUM(amount) as total_bounty FROM $dbtables[bounty] WHERE placed_by = 0 AND bounty_on = $playerinfo[player_id]");
  if($res2)
  {
     $bty = $res2->fields;
     if($bty[total_bounty] > 0)
     {
        if($pay <> 1)
        {
           echo $l_port_bounty;
           $l_port_bounty2 = str_replace("[amount]",NUMBER($bty[total_bounty]),$l_port_bounty2);
           echo $l_port_bounty2 . "<BR>";
           echo "<A HREF=\"bounty.php\">$l_by_placebounty</A><BR><BR>";
           TEXT_GOTOMAIN();
           die(); 
        }
        else
        {
           if($playerinfo[credits] < $bty[total_bounty])
           {
              $l_port_btynotenough = str_replace("[amount]",NUMBER($bty[total_bounty]),$l_port_btynotenough);
              echo $l_port_btynotenough;
              TEXT_GOTOMAIN();
              die();
           }
           else
           {
              $db->Execute("UPDATE $dbtables[players] SET credits=credits-$bty[total_bounty] WHERE player_id = $playerinfo[player_id]");
              $db->Execute("DELETE from $dbtables[bounty] WHERE bounty_on = $playerinfo[player_id] AND placed_by = 0");
              echo $l_port_bountypaid;
              die();
           }
        }
     }
  }

  $emerwarp_free = $max_emerwarp - $shipinfo[dev_emerwarp];
  $fighter_max = NUM_FIGHTERS($shipinfo[computer]);
  $fighter_free = $fighter_max - $shipinfo[fighters];
  $torpedo_max = NUM_TORPEDOES($shipinfo[torp_launchers]);
  $torpedo_free = $torpedo_max - $shipinfo[torps];
  $armour_max = NUM_ARMOUR($shipinfo[armour]);
  $armour_free = $armour_max - $shipinfo[armour_pts];
  $colonist_max = NUM_HOLDS($shipinfo[hull]) - $shipinfo[ore] - $shipinfo[organics] - $shipinfo[goods];
  $colonist_free = $colonist_max - $shipinfo[colonists];

  TEXT_JAVASCRIPT_BEGIN();

echo "function MakeMax(name, val)\n";
echo "{\n";
echo " if (document.forms[0].elements[name].value != val)\n";
echo " {\n";
echo "  if (val != 0)\n";
echo "  {\n";
echo "  document.forms[0].elements[name].value = val;\n";
echo "  }\n";
echo " }\n";
echo "}\n";

// changeDelta function //
echo "function changeDelta(desiredvalue,currentvalue)\n";
echo "{\n";
echo "  Delta=0; DeltaCost=0;\n";
echo "  Delta = desiredvalue - currentvalue;\n";
echo "\n";    
echo "    while(Delta>0) \n";
echo "    {\n";
echo "     DeltaCost=DeltaCost + Math.pow(2,desiredvalue-Delta); \n";
echo "     Delta=Delta-1;\n";
echo "    }\n";
echo "\n";    
echo "  DeltaCost=DeltaCost * $upgrade_cost\n";
echo "  return DeltaCost;\n";
echo "}\n";

echo "function countTotal()\n";
echo "{\n";
echo "// Here we cycle through all form values (other than buy, or full), and regexp out all non-numerics. (1,000 = 1000)\n";
echo "// Then, if its become a null value (type in just a, it would be a blank value. blank is bad.) we set it to zero.\n";
echo "var form = document.forms[0];\n";
echo "var i = form.elements.length;\n";
echo "while (i > 0)\n";
echo " {\n";
echo " if ((form.elements[i-1].value != 'Buy') && (form.elements[i-1].value != 'Full'))\n";
echo "  {\n";
echo "  var tmpval = form.elements[i-1].value.replace(/\D+/g, \"\");\n";
echo "  if (tmpval != form.elements[i-1].value)\n";
echo "   {\n";
echo "   form.elements[i-1].value = form.elements[i-1].value.replace(/\D+/g, \"\");\n";
echo "   }\n";
echo "  }\n";
echo " if (form.elements[i-1].value == '')\n";
echo "  {\n";
echo "  form.elements[i-1].value ='0';\n";
echo "  }\n";
echo " i--;\n";
echo "}\n";
echo "// Here we set all 'Max' items to 0 if they are over max - player amt.\n";
echo "if (($emerwarp_free < form.dev_emerwarp_number.value) && (form.dev_emerwarp_number.value != 'Full'))\n";
echo " {\n";
echo " form.dev_emerwarp_number.value=0\n";
echo " }\n";
echo "if (($fighter_free < form.fighter_number.value) && (form.fighter_number.value != 'Full'))\n";
echo " {\n";
echo " form.fighter_number.value=0\n";
echo " }\n";
echo "if (($torpedo_free < form.torpedo_number.value) && (form.torpedo_number.value != 'Full'))\n";
echo "  {\n";
echo "  form.torpedo_number.value=0\n";
echo "  }\n";
echo "if (($armour_free < form.armour_number.value) && (form.armour_number.value != 'Full'))\n";
echo "  {\n";
echo "  form.armour_number.value=0\n";
echo "  }\n";
echo "if (($colonist_free < form.colonist_number.value) && (form.colonist_number.value != 'Full' ))\n";
echo "  {\n";
echo "  form.colonist_number.value=0\n";
echo "  }\n";
echo "// Done with the bounds checking\n";
echo "// Pluses must be first, or if empty will produce a javascript error\n";
echo "form.total_cost.value = form.dev_genesis_number.value * $dev_genesis_price \n";
echo "+ form.dev_beacon_number.value * $dev_beacon_price\n";
if($emerwarp_free > 0)
{
  echo "+ form.dev_emerwarp_number.value * $dev_emerwarp_price\n";
}
echo "+ form.dev_warpedit_number.value * $dev_warpedit_price\n";
echo "+ form.elements['dev_minedeflector_number'].value * $dev_minedeflector_price\n";
if($shipinfo[dev_escapepod] == 'N')
{
  echo "+ (form.escapepod_purchase.checked ?  $dev_escapepod_price : 0)\n";
}
if($shipinfo[dev_fuelscoop] == 'N')
{
  echo "+ (form.fuelscoop_purchase.checked ?  $dev_fuelscoop_price : 0)\n";
}
if($shipinfo[dev_lssd] == 'N')
{
  echo "+ (form.lssd_purchase.checked ?  $dev_lssd_price : 0)\n";
}

echo "+ changeDelta(form.hull_upgrade.value,$shipinfo[hull])\n";
echo "+ changeDelta(form.engine_upgrade.value,$shipinfo[engines])\n";
echo "+ changeDelta(form.power_upgrade.value,$shipinfo[power])\n";
echo "+ changeDelta(form.computer_upgrade.value,$shipinfo[computer])\n";
echo "+ changeDelta(form.sensors_upgrade.value,$shipinfo[sensors])\n";
echo "+ changeDelta(form.beams_upgrade.value,$shipinfo[beams])\n";
echo "+ changeDelta(form.armour_upgrade.value,$shipinfo[armour])\n";
echo "+ changeDelta(form.cloak_upgrade.value,$shipinfo[cloak])\n";
echo "+ changeDelta(form.torp_launchers_upgrade.value,$shipinfo[torp_launchers])\n";
echo "+ changeDelta(form.shields_upgrade.value,$shipinfo[shields])\n";

  if($shipinfo[fighters] != $fighter_max)
  {
    echo "+ form.fighter_number.value * $fighter_price ";
  }
  if($shipinfo[torps] != $torpedo_max)
  {
    echo "+ form.torpedo_number.value * $torpedo_price ";
  }
  if($shipinfo[armour_pts] != $armour_max)
  {
    echo "+ form.armour_number.value * $armour_price ";
  }
  if($shipinfo[colonists] != $colonist_max)
  {
    echo "+ form.colonist_number.value * $colonist_price ";
  }
  echo ";\n";
  echo "  if (form.total_cost.value > $playerinfo[credits])\n";
  echo "  {\n";
  echo "    form.total_cost.value = '$l_no_credits';\n";
//  echo "    form.total_cost.value = 'You are short '+(form.total_cost.value - $playerinfo[credits]) +' credits';\n";
  echo "  }\n";
  echo "  form.total_cost.length = form.total_cost.value.length;\n";
  echo "\n";
  echo "form.engine_costper.value=changeDelta(form.engine_upgrade.value,$shipinfo[engines]);\n";
  echo "form.power_costper.value=changeDelta(form.power_upgrade.value,$shipinfo[power]);\n";
  echo "form.computer_costper.value=changeDelta(form.computer_upgrade.value,$shipinfo[computer]);\n";
  echo "form.sensors_costper.value=changeDelta(form.sensors_upgrade.value,$shipinfo[sensors]);\n";
  echo "form.beams_costper.value=changeDelta(form.beams_upgrade.value,$shipinfo[beams]);\n";
  echo "form.armour_costper.value=changeDelta(form.armour_upgrade.value,$shipinfo[armour]);\n";
  echo "form.cloak_costper.value=changeDelta(form.cloak_upgrade.value,$shipinfo[cloak]);\n";
  echo "form.torp_launchers_costper.value=changeDelta(form.torp_launchers_upgrade.value,$shipinfo[torp_launchers]);\n";
  echo "form.hull_costper.value=changeDelta(form.hull_upgrade.value,$shipinfo[hull]);\n";
  echo "form.shields_costper.value=changeDelta(form.shields_upgrade.value,$shipinfo[shields]);\n";
  echo "}";
  TEXT_JAVASCRIPT_END();

  $onblur = "ONBLUR=\"countTotal()\"";
  $onfocus =  "ONFOCUS=\"countTotal()\"";
  $onchange =  "ONCHANGE=\"countTotal()\"";
  $onclick =  "ONCLICK=\"countTotal()\"";

// Create dropdowns when called
function dropdown($element_name,$current_value, $max_value)
{
global $onchange;
$i = $current_value;
$dropdownvar = "<select size='1' name='$element_name'";
$dropdownvar = "$dropdownvar $onchange>\n";
while ($i <= $max_value)
 {
 if ($current_value == $i)
  {
  $dropdownvar = "$dropdownvar        <option value='$i' selected>$i</option>\n";
  }
 else
  {
  $dropdownvar = "$dropdownvar        <option value='$i'>$i</option>\n";
  }
 $i++;
 }
$dropdownvar = "$dropdownvar       </select>\n";
return $dropdownvar;
}


  echo "<P>\n";
  $l_creds_to_spend=str_replace("[credits]",NUMBER($playerinfo[credits]),$l_creds_to_spend);
  echo "$l_creds_to_spend<BR>\n";
  if($allow_ibank)
  {
    $igblink = "\n<A HREF=IGB.php>$l_igb_term</a>";
    $l_ifyouneedmore=str_replace("[igb]",$igblink,$l_ifyouneedmore);

    echo "$l_ifyouneedmore<BR>";
  }
  echo "\n";
  echo "<A HREF=\"bounty.php\">$l_by_placebounty</A><BR>\n";
  echo " <FORM ACTION=port2.php METHOD=POST>\n";
  echo "  <TABLE WIDTH=\"100%\" BORDER=0 CELLSPACING=0 CELLPADDING=0>\n";
  echo "   <TR BGCOLOR=\"$color_header\">\n";
  echo "    <TD><B>$l_device</B></TD>\n";
  echo "    <TD><B>$l_cost</B></TD>\n";
  echo "    <TD><B>$l_current</B></TD>\n";
  echo "    <TD><B>$l_max</B></TD>\n";
  echo "    <TD><B>$l_qty</B></TD>\n";
  echo "    <TD><B>$l_ship_levels</B></TD>\n";
  echo "    <TD><B>$l_cost</B></TD>\n";
  echo "    <TD><B>$l_current</B></TD>\n";
  echo "    <TD><B>$l_upgrade</B></TD>\n";
  echo "   </TR>\n";
  echo "   <TR BGCOLOR=\"$color_line1\">\n";
  echo "    <TD>$l_genesis</TD>\n";
  echo "    <TD>" . NUMBER($dev_genesis_price) . "</TD>\n";
  echo "    <TD>" . NUMBER($shipinfo[dev_genesis]) . "</TD>\n";
  echo "    <TD>$l_unlimited</TD>\n";
  echo "    <TD><INPUT TYPE=TEXT NAME=dev_genesis_number SIZE=4 MAXLENGTH=4 VALUE=0 $onblur></TD>\n";
  echo "    <TD>$l_hull</TD>\n";
  echo "    <TD><input type=text readonly class='portcosts1' name=hull_costper VALUE='0' tabindex='-1' $onblur></TD>\n";
  echo "    <TD>" . NUMBER($shipinfo[hull]) . "</TD>\n";
  echo "    <TD>\n       ";
  echo dropdown("hull_upgrade",$shipinfo[hull], $classinfo[maxhull]);
  echo "    </TD>\n";
  echo "   </TR>\n";
  echo "   <TR BGCOLOR=\"$color_line2\">\n";
  echo "    <TD>$l_beacons</TD>\n";
  echo "    <TD>" . NUMBER($dev_beacon_price) . "</TD>\n";
  echo "    <TD>" . NUMBER($shipinfo[dev_beacon]) . "</TD>\n";
  echo "    <TD>$l_unlimited</TD>\n";
  echo "    <TD><INPUT TYPE=TEXT NAME=dev_beacon_number SIZE=4 MAXLENGTH=4 VALUE=0 $onblur></TD>\n";
  echo "    <TD>$l_engines</TD>\n";
  echo "    <TD><input type=text readonly class='portcosts2' size=10 name=engine_costper VALUE='0' tabindex='-1' $onblur></TD>\n";
  echo "    <TD>" . NUMBER($shipinfo[engines]) . "</TD>\n";
  echo "    <TD>\n       ";
  echo dropdown("engine_upgrade",$shipinfo[engines], $classinfo[maxengines]);
  echo "    </TD>\n";
  echo "   </TR>\n";
  echo "   <TR BGCOLOR=\"$color_line1\">\n";
  echo "    <TD>$l_ewd</TD>\n";
  echo "    <TD>" . NUMBER($dev_emerwarp_price) . "</TD>\n";
  echo "    <TD>" . NUMBER($shipinfo[dev_emerwarp]) . "</TD>\n";
  echo "    <TD>";
  if($shipinfo[dev_emerwarp] != $max_emerwarp)
  {
    echo"<a href='#' onClick=\"MakeMax('dev_emerwarp_number', $emerwarp_free);countTotal();return false;\">";
    echo NUMBER($emerwarp_free) . "</a></TD>\n";
    echo"    <TD><INPUT TYPE=TEXT NAME=dev_emerwarp_number SIZE=4 MAXLENGTH=4 VALUE=0 $onblur>";
  }
  else
  {
    echo "0</TD>\n";
    echo "    <TD><input type=text readonly class='portcosts1' NAME=dev_emerwarp_number MAXLENGTH=10 VALUE=$l_full $onblur tabindex='-1'>";
  }
  echo "</TD>\n";
  echo "    <TD>$l_power</TD>\n";
  echo "    <TD><input type=text readonly class='portcosts1' name=power_costper VALUE='0' tabindex='-1' $onblur></td>\n";
  echo "    <TD>" . NUMBER($shipinfo[power]) . "</TD>\n";
  echo "    <TD>\n       ";
  echo dropdown("power_upgrade",$shipinfo[power], $classinfo[maxpower]);
  echo "    </TD>\n";
  echo "  </TR>\n";
  echo "  <TR BGCOLOR=\"$color_line2\">\n";
  echo "    <TD>$l_warpedit</TD>\n";
  echo "    <TD>" . NUMBER($dev_warpedit_price) . "</TD>\n";
  echo "    <TD>" . NUMBER($shipinfo[dev_warpedit]) . "</TD><TD>$l_unlimited</TD><TD><INPUT TYPE=TEXT NAME=dev_warpedit_number SIZE=4 MAXLENGTH=4 VALUE=0 $onblur></TD>";
  echo "    <TD>$l_computer</TD>\n";
  echo "    <TD><input type=text readonly class='portcosts2' name=computer_costper VALUE='0' tabindex='-1' $onblur></TD>\n";
  echo "    <TD>" . NUMBER($shipinfo[computer]) . "</TD>\n";
  echo "    <TD>\n       ";
  echo dropdown("computer_upgrade",$shipinfo[computer], $classinfo[maxcomputer]);
  echo "    </TD>\n";
  echo "  </TR>\n";
  echo "  <TR BGCOLOR=\"$color_line1\">\n";
  echo "    <TD>&nbsp;</TD>\n";
  echo "    <TD>&nbsp;</TD>\n";
  echo "    <TD>&nbsp;</TD>\n";
  echo "    <TD>&nbsp;</TD>\n";
  echo "    <TD>&nbsp;</TD>\n";
  echo "    <TD>$l_sensors</TD>\n";
  echo "    <TD><input type=text readonly class='portcosts1' name=sensors_costper VALUE='0' tabindex='-1' $onblur></td>\n";
  echo "    <TD>" . NUMBER($shipinfo[sensors]) . "</TD>\n";
  echo "    <TD>\n       ";
  echo dropdown("sensors_upgrade",$shipinfo[sensors], $classinfo[maxsensors]);
  echo "    </TD>\n";
  echo "  </TR>";
  echo "  <TR BGCOLOR=\"$color_line2\">\n";
  echo "    <TD>$l_deflect</TD>\n";
  echo "    <TD>" . NUMBER($dev_minedeflector_price) . "</TD>\n";
  echo "    <TD>" . NUMBER($shipinfo[dev_minedeflector]) . "</TD>\n";
  echo "    <TD>$l_unlimited</TD>\n";
  echo "    <TD><INPUT TYPE=TEXT NAME=dev_minedeflector_number SIZE=4 MAXLENGTH=10 VALUE=0 $onblur></TD>\n";
  echo "    <TD>$l_beams</TD>\n";
  echo "    <TD><input type=text readonly class='portcosts2' name=beams_costper VALUE='0' tabindex='-1' $onblur></td>";
  echo "    <TD>" . NUMBER($shipinfo[beams]) . "</TD>\n";
  echo "    <TD>\n       ";
  echo dropdown("beams_upgrade",$shipinfo[beams], $classinfo[maxbeams]);
  echo "    </TD>\n";
  echo "  </TR>\n";
  echo "  <TR BGCOLOR=\"$color_line1\">\n";
  echo "    <TD>$l_escape_pod</TD>\n";
  echo "    <TD>" . NUMBER($dev_escapepod_price) . "</TD>\n";
  if($shipinfo[dev_escapepod] == "N")
  {
    echo "    <TD>$l_none</TD>\n";
    echo "    <TD>&nbsp;</TD>\n";
    echo "    <TD><INPUT TYPE=CHECKBOX NAME=escapepod_purchase VALUE=1 $onclick></TD>\n";
  }
  else
  {
    echo "    <TD>$l_equipped</TD>\n";
    echo "    <TD></TD>\n";
    echo "    <TD>$l_n_a</TD>\n";
  }
  echo "    <TD>$l_armour</TD>\n";
  echo "    <TD><input type=text readonly class='portcosts1' name=armour_costper VALUE='0' tabindex='-1' $onblur></TD>\n";
  echo "    <TD>" . NUMBER($shipinfo[armour]) . "</TD>\n";
  echo "    <TD>\n       ";
  echo dropdown("armour_upgrade",$shipinfo[armour], $classinfo[maxarmour]);
  echo "    </TD>\n";
  echo "  </TR>\n";
  echo "  <TR BGCOLOR=\"$color_line2\">\n";
  echo "    <TD>$l_fuel_scoop</TD>\n";
  echo "    <TD>" . NUMBER($dev_fuelscoop_price) . "</TD>\n";
  if($shipinfo[dev_fuelscoop] == "N")
  {
    echo "    <TD>$l_none</TD>\n";
    echo "    <TD>&nbsp;</TD>\n";
    echo "    <TD><INPUT TYPE=CHECKBOX NAME=fuelscoop_purchase VALUE=1 $onclick></TD>\n";
  }
  else
  {
    echo "    <TD>$l_equipped</TD>\n";
    echo "    <TD></TD>\n";
    echo "    <TD>$l_n_a</TD>\n";
  }
  echo "    <TD>$l_cloak</TD>\n";
  echo "    <TD><input type=text readonly class='portcosts2' name=cloak_costper VALUE='0' tabindex='-1' $onblur $onfocus></TD>\n";
  echo "    <TD>" . NUMBER($shipinfo[cloak]) . "</TD>\n";
  echo "    <TD>\n       ";
  echo dropdown("cloak_upgrade",$shipinfo[cloak], $classinfo[maxcloak]);
  echo "    </TD>\n";
  echo "  </TR>\n";
  echo "  <TR BGCOLOR=\"$color_line1\">\n";
  echo "    <TD>$l_lssd</TD>\n";
  echo "    <TD>" . NUMBER($dev_lssd_price) . "</TD>\n";
  if($shipinfo[dev_lssd] == "N")
  {
    echo "    <TD>$l_none</TD>\n";
    echo "    <TD>&nbsp;</TD>\n";
    echo "    <TD><INPUT TYPE=CHECKBOX NAME=lssd_purchase VALUE=1 $onclick></TD>\n";
  }
  else
  {
    echo "    <TD>$l_equipped</TD>\n";
    echo "    <TD></TD>\n";
    echo "    <TD>$l_n_a</TD>\n";
  }
  echo "    <TD>$l_torp_launch</TD>\n";
  echo "    <TD><input type=text readonly class='portcosts1' name=torp_launchers_costper VALUE='0' tabindex='-1' $onblur></TD>\n";
  echo "    <TD>" . NUMBER($shipinfo[torp_launchers]) . "</TD>\n";
  echo "    <TD>\n       ";
  echo dropdown("torp_launchers_upgrade",$shipinfo[torp_launchers], $classinfo[maxtorp_launchers]);
  echo "    </TD>\n";
  echo "  </TR>\n";
  echo "  <TR BGCOLOR=\"$color_line2\">\n";
  echo "    <TD>&nbsp;</TD>\n";
  echo "    <TD>&nbsp;</TD>\n";
  echo "    <TD>&nbsp;</TD>\n";
  echo "    <TD>&nbsp;</TD>\n";
  echo "    <TD>&nbsp;</TD>\n";
  echo "    <TD>$l_shields</TD>\n";
  echo "    <TD><input type=text readonly class='portcosts2' name=shields_costper VALUE='0' tabindex='-1' $onblur></TD>\n";
  echo "    <TD>" . NUMBER($shipinfo[shields]) . "</TD>\n";
  echo "    <TD>\n       ";
  echo dropdown("shields_upgrade",$shipinfo[shields], $classinfo[maxshields]);
  echo "    </TD>\n";
  echo "  </TR>\n";
  echo " </TABLE>\n";
  echo " <BR>\n";
  echo " <TABLE WIDTH=\"100%\" BORDER=0 CELLSPACING=0 CELLPADDING=0>\n";
  echo "  <TR BGCOLOR=\"$color_header\">\n";
  echo "    <TD><B>$l_item</B></TD>\n";
  echo "    <TD><B>$l_cost</B></TD>\n";
  echo "    <TD><B>$l_current</B></TD>\n";
  echo "    <TD><B>$l_max</B></TD>\n";
  echo "    <TD><B>$l_qty</B></TD>\n";
  echo "    <TD><B>$l_item</B></TD>\n";
  echo "    <TD><B>$l_cost</B></TD>\n";
  echo "    <TD><B>$l_current</B></TD>\n";
  echo "    <TD><B>$l_max</B></TD>\n";
  echo "    <TD><B>$l_qty</B></TD>\n";
  echo "  </TR>\n";
  echo "  <TR BGCOLOR=\"$color_line1\">\n";
  echo "    <TD>$l_fighters</TD>\n";
  echo "    <TD>" . NUMBER($fighter_price) . "</TD>\n";
  echo "    <TD>" . NUMBER($shipinfo[fighters]) . " / " . NUMBER($fighter_max) . "</TD>\n";
  echo "    <TD>";
  if($shipinfo[fighters] != $fighter_max)
  {
    echo "<a href='#' onClick=\"MakeMax('fighter_number', $fighter_free);countTotal();return false;\"; $onblur>" . NUMBER($fighter_free) . "</a></TD>\n";
    echo "    <TD><INPUT TYPE=TEXT NAME=fighter_number SIZE=6 MAXLENGTH=10 VALUE=0 $onblur>";
  }
  else
  {
    echo "0<TD><input type=text readonly class='portcosts1' NAME=fighter_number MAXLENGTH=10 VALUE=$l_full $onblur tabindex='-1'>";
  }
  echo "    </TD>\n";
  echo "    <TD>$l_torps</TD>\n";
  echo "    <TD>" . NUMBER($torpedo_price) . "</TD>\n";
  echo "    <TD>" . NUMBER($shipinfo[torps]) . " / " . NUMBER($torpedo_max) . "</TD>\n";
  echo "    <TD>";
  if($shipinfo[torps] != $torpedo_max)
  {
    echo "<a href='#' onClick=\"MakeMax('torpedo_number', $torpedo_free);countTotal();return false;\"; $onblur>" . NUMBER($torpedo_free) . "</a></TD>\n";
    echo "    <TD><INPUT TYPE=TEXT NAME=torpedo_number SIZE=6 MAXLENGTH=10 VALUE=0 $onblur>";
  }
  else
  {
    echo "0<TD><input type=text readonly class='portcosts1' NAME=torpedo_number MAXLENGTH=10 VALUE=$l_full $onblur tabindex='-1'>";
  }
  echo "</TD>\n";
  echo "  </TR>\n";
  echo "  <TR BGCOLOR=\"$color_line2\">\n";
  echo "    <TD>$l_armourpts</TD>\n";
  echo "    <TD>" . NUMBER($armour_price) . "</TD>\n";
  echo "    <TD>" . NUMBER($shipinfo[armour_pts]) . " / " . NUMBER($armour_max) . "</TD>\n";
  echo "    <TD>";
  if($shipinfo[armour_pts] != $armour_max)
  {
    echo "<a href='#' onClick=\"MakeMax('armour_number', $armour_free);countTotal();return false;\"; $onblur>" . NUMBER($armour_free) . "</a></TD>\n";
    echo "    <TD><INPUT TYPE=TEXT NAME=armour_number SIZE=6 MAXLENGTH=10 VALUE=0 $onblur>";
  }
  else
  {
    echo "0<TD><input type=text readonly class='portcosts2' NAME=armour_number MAXLENGTH=10 VALUE=$l_full tabindex='-1' $onblur>";
  }
  echo "</TD>\n";
  echo "    <TD>$l_colonists</TD>\n";
  echo "    <TD>" . NUMBER($colonist_price) . "</TD>\n";
  echo "    <TD>" . NUMBER($shipinfo[colonists]) . " / ". NUMBER($colonist_max). "</TD>\n";
  echo "    <TD>";
  if($shipinfo[colonists] != $colonist_max)
  {
    echo "<a href='#' onClick=\"MakeMax('colonist_number', $colonist_free);countTotal();return false;\"; $onblur>" . NUMBER($colonist_free) . "</a></TD>\n";
    echo "    <TD><INPUT TYPE=TEXT NAME=colonist_number SIZE=6 MAXLENGTH=10 VALUE=0 $onblur>";
  }
  else
  {
    echo "0<TD><input type=text readonly class='portcosts2' NAME=colonist_number MAXLENGTH=10 VALUE=$l_full tabindex='-1' $onblur>";
  }
  echo "    </TD>\n";
  echo "  </TR>\n";
  echo " </TABLE><BR>\n";
  echo " <TABLE WIDTH=\"100%\" BORDER=0 CELLSPACING=0 CELLPADDING=0>\n";
  echo "  <TR>\n";
  echo "    <TD><INPUT TYPE=SUBMIT VALUE=$l_buy $onclick></TD>\n";
  echo "    <TD ALIGN=RIGHT>$l_totalcost: <INPUT TYPE=TEXT style=\"text-align:right\" NAME=total_cost SIZE=22 VALUE=0 $onfocus $onblur $onchange $onclick></td>\n";
  echo "  </TR>\n";
  echo " </TABLE>\n";
  echo "</FORM>\n";
  echo "$l_would_dump <A HREF=dump.php>$l_here</A>.\n";
}
else
{
  echo "$l_noport!\n";
}

echo "\n";
echo "<BR><BR>\n";
TEXT_GOTOMAIN();
echo "\n";

include("footer.php");

?>
