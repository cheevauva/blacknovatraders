<?

include("config.php");
updatecookie();

include("languages/$lang");

$title=$l_igb_title;
$no_body = 1;
include("header.php");

connectdb();
if (checklogin()) {die();}

$result = $db->Execute("SELECT * FROM $dbtables[ships] WHERE email='$username'");
$playerinfo = $result->fields;

$result = $db->Execute("SELECT * FROM $dbtables[ibank_accounts] WHERE ship_id=$playerinfo[ship_id]");
$account = $result->fields;

echo "<BODY bgcolor=#666666 text=\"#F0F0F0\" link=\"#00ff00\" vlink=\"#00ff00\" alink=\"#ff0000\">";
?>

<center>
<img src=images/div1.gif>
<table width=600 height=350 border=0>
<tr><td align=center background=images/IGBscreen.gif>
<table background="" width=520 height=300 border=0>

<?

if(!$allow_ibank)
  IGB_error($l_igb_malfunction, "main.php");

if($command == 'login') //main menu
  IGB_login();
elseif($command == 'withdraw') //withdraw menu
  IGB_withdraw();
elseif($command == 'withdraw2') //withdraw operation
  IGB_withdraw2();
elseif($command == 'deposit') //deposit menu
  IGB_deposit();
elseif($command == 'deposit2') //deposit operation
  IGB_deposit2();
elseif($command == 'transfer') //main transfer menu
  IGB_transfer();
elseif($command == 'transfer2') //specific transfer menu (ship or planet)
  IGB_transfer2();
elseif($command == 'transfer3') //transfer operation
  IGB_transfer3();
else
{
  echo "
  <tr><td width=25% valign=bottom><a href=\"main.php\"><font size=2 face=\"courier new\" color=#00FF00>$l_igb_quit</a></td><td width=50%>
  <font size=2 face=\"courier new\" color=#00FF00>
  <pre>
  IIIIIIIIII          GGGGGGGGGGGGG    BBBBBBBBBBBBBBBBB
  I::::::::I       GGG::::::::::::G    B::::::::::::::::B
  I::::::::I     GG:::::::::::::::G    B::::::BBBBBB:::::B
  II::::::II    G:::::GGGGGGGG::::G    BB:::::B     B:::::B
    I::::I     G:::::G       GGGGGG      B::::B     B:::::B
    I::::I    G:::::G                    B::::B     B:::::B
    I::::I    G:::::G                    B::::BBBBBB:::::B
    I::::I    G:::::G    GGGGGGGGGG      B:::::::::::::BB
    I::::I    G:::::G    G::::::::G      B::::BBBBBB:::::B
    I::::I    G:::::G    GGGGG::::G      B::::B     B:::::B
    I::::I    G:::::G        G::::G      B::::B     B:::::B
    I::::I     G:::::G       G::::G      B::::B     B:::::B
  II::::::II    G:::::GGGGGGGG::::G    BB:::::BBBBBB::::::B
  I::::::::I     GG:::::::::::::::G    B:::::::::::::::::B
  I::::::::I       GGG::::::GGG:::G    B::::::::::::::::B
  IIIIIIIIII          GGGGGG   GGGG    BBBBBBBBBBBBBBBBB
  </pre>
  <center>
  <p>";
  echo $l_igb_title;
  echo "(tm)<br>";
  echo $l_igb_humor;
  echo "<br>&nbsp;
  </center></td>
  <td width=25% valign=bottom align=right><font size=2 color=#00FF00 face=\"courier new\"><a href=\"IGB.php?command=login\">$l_igb_login</a></td>
  ";
}

?>

</table>
</td></tr>
</table>
<img src=images/div2.gif>
</center>

<?
include("footer.php");

function IGB_login()
{
  global $playerinfo;
  global $account;
  global $l_igb_welcometoigb, $l_igb_accountholder, $l_igb_back, $l_igb_logout;
  global $l_igb_igbaccount, $l_igb_shipaccount, $l_igb_withdraw, $l_igb_transfer;
  global $l_igb_deposit, $l_igb_credit_symbol; $l_igb_operations;

  echo "<tr><td colspan=2 align=center valign=top><font size=2 face=\"courier new\" color=#00FF00>$l_igb_welcometoigb<br>---------------------------------</td></tr>" .
       "<tr valign=top>" .
       "<td><font size=2 face=\"courier new\" color=#00FF00>$l_igb_accountholder :<br><br>$l_igb_shipaccount :<br>$l_igb_igbaccount&nbsp;&nbsp;:</td>" .
       "<td align=right><font size=2 face=\"courier new\" color=#00FF00>$playerinfo[character_name]&nbsp;&nbsp;<br><br>".NUMBER($playerinfo[credits]) . " $l_igb_credit_symbol<br>" . NUMBER($account[balance]) . " $l_igb_credit_symbol<br></td>" .
       "</tr>" .
       "<tr><td colspan=2 align=center><font size=2 face=\"courier new\" color=#00FF00>$l_igb_operations<br>---------------------------------<br><br><a href=\"IGB.php?command=withdraw\">$l_igb_withdraw</a><br><a href=\"IGB.php?command=deposit\">$l_igb_deposit</a><br><a href=\"IGB.php?command=transfer\">$l_igb_transfer</a><br>&nbsp;</td></tr>" .
       "<tr valign=bottom>" .
       "<td><font size=2 face=\"courier new\" color=#00FF00><a href=IGB.php>$l_igb_back</a></td><td align=right><font size=2 face=\"courier new\" color=#00FF00>&nbsp;<br><a href=\"main.php\">$l_igb_logout</a></td>" .
       "</tr>";
}

function IGB_withdraw()
{
  global $playerinfo;
  global $account;
  global $l_igb_withdrawfunds, $l_igb_fundsavailable, $l_igb_selwithdrawamount;
  global $l_igb_withdraw, $l_igb_back, $l_igb_logout;

  echo "<tr><td colspan=2 align=center valign=top><font size=2 face=\"courier new\" color=#00FF00>$l_igb_withdrawfunds<br>---------------------------------</td></tr>" .
       "<tr valign=top>" .
       "<td><font size=2 face=\"courier new\" color=#00FF00>$l_igb_fundsavailable :</td>" .
       "<td align=right><font size=2 face=\"courier new\" color=#00FF00>" . NUMBER($account[balance]) ." C<br></td>" .
       "</tr><tr valign=top>" .
       "<td><font size=2 face=\"courier new\" color=#00FF00>$l_igb_selwithdrawamount :</td><td align=right>" .
       "<form action=IGB.php?command=withdraw2 method=POST>" .
       "<input style=\"background-color: #000000; color: #00FF00; font-family:Courier New; font-size:10pt\" type=text size=15 maxlength=20 name=amount value=0>" .
       "<br><br><input style=\"background-color: #000000; color: #00FF00; font-family:Courier New; font-size:10pt\" type=submit value=$l_igb_withdraw>" .
       "</form></td></tr>" .
       "<tr valign=bottom>" .
       "<td><font size=2 face=\"courier new\" color=#00FF00><a href=IGB.php?command=login>$l_igb_back</a></td><td align=right><font size=2 face=\"courier new\" color=#00FF00>&nbsp;<br><a href=\"main.php\">$l_igb_logout</a></td>" .
       "</tr>";

}

function IGB_deposit()
{
  global $playerinfo;
  global $account;
  global $l_igb_depositfunds, $l_igb_fundsavailable, $l_igb_seldepositamount;
  global $l_igb_deposit, $l_igb_back, $l_igb_logout;

  echo "<tr><td colspan=2 align=center valign=top><font size=2 face=\"courier new\" color=#00FF00>$l_igb_depositfunds<br>---------------------------------</td></tr>" .
       "<tr valign=top>" .
       "<td><font size=2 face=\"courier new\" color=#00FF00>$l_igb_fundsavailable :</td>" .
       "<td align=right><font size=2 face=\"courier new\" color=#00FF00>" . NUMBER($playerinfo[credits]) ." C<br></td>" .
       "</tr><tr valign=top>" .
       "<td><font size=2 face=\"courier new\" color=#00FF00>$l_igb_seldepositamount :</td><td align=right>" .
       "<form action=IGB.php?command=deposit2 method=POST>" .
       "<input style=\"background-color: #000000; color: #00FF00; font-family:Courier New; font-size:10pt\" type=text size=15 maxlength=20 name=amount value=0>" .
       "<br><br><input style=\"background-color: #000000; color: #00FF00; font-family:Courier New; font-size:10pt\" type=submit value=$l_igb_deposit>" .
       "</form></td></tr>" .
       "<tr valign=bottom>" .
       "<td><font size=2 face=\"courier new\" color=#00FF00><a href=IGB.php?command=login>$l_igb_back</a></td><td align=right><font size=2 face=\"courier new\" color=#00FF00>&nbsp;<br><a href=\"main.php\">$l_igb_logout</a></td>" .
       "</tr>";

}

function IGB_transfer()
{
  global $playerinfo;
  global $account;
  global $l_igb_transfertype, $l_igb_toanothership, $l_igb_shiptransfer, $l_igb_fromplanet, $l_igb_source;
  global $l_igb_unnamed, $l_igb_in, $l_igb_none, $l_igb_planettransfer, $l_igb_back, $l_igb_logout, $l_igb_destination;
  global $db, $dbtables;

  $res = $db->Execute("SELECT character_name, ship_id FROM $dbtables[ships] ORDER BY character_name ASC");
  while(!$res->EOF)
  {
    $ships[]=$res->fields;
    $res->MoveNext();
  }

  $res = $db->Execute("SELECT name, planet_id, sector_id FROM $dbtables[planets] WHERE owner=$playerinfo[ship_id] ORDER BY sector_id ASC");
  while(!$res->EOF)
  {
    $planets[]=$res->fields;
    $res->MoveNext();
  }


  echo "<tr><td colspan=2 align=center valign=top><font size=2 face=\"courier new\" color=#00FF00>$l_igb_transfertype<br>---------------------------------</td></tr>" .
       "<tr valign=top>" .
       "<form action=IGB.php?command=transfer2 method=POST>" .
       "<td><font size=2 face=\"courier new\" color=#00FF00>$l_igb_toanothership :<br><br>" .
       "<select style=\"background-color: #000000; color: #00FF00; font-family:Courier New; font-size:10pt\" name=ship_id>";

  foreach($ships as $ship)
  {
    echo "<option value=$ship[ship_id]>$ship[character_name]</option>";
  }

  echo "</select></td><td valign=center align=right>" .
       "<input style=\"background-color: #000000; color: #00FF00; font-family:Courier New; font-size:10pt\" type=submit name=shipt value=\" $l_igb_shiptransfer \">" .
       "</form>" .
       "</td></tr>" .
       "<tr valign=top>" .
       "<td><br><font size=2 face=\"courier new\" color=#00FF00>$l_igb_fromplanet :<br><br>" .
       "<form action=IGB.php?command=transfer2 method=POST>" .
       "$l_igb_source&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<select style=\"background-color: #000000; color: #00FF00; font-family:Courier New; font-size:10pt\" name=splanet_id>";

  if(isset($planets))
  {
    foreach($planets as $planet)
    {
      if(empty($planet[name]))
        $planet[name] = $l_igb_unnamed;
      echo "<option value=$planet[planet_id]>$planet[name] $l_igb_in $planet[sector_id]</option>";
    }
  }
  else
  {
     echo "<option value=none>$l_igb_none</option>";
  }

  echo "</select><br>$l_igb_destination <select style=\"background-color: #000000; color: #00FF00; font-family:Courier New; font-size:10pt\" name=dplanet_id>";

  if(isset($planets))
  {
    foreach($planets as $planet)
    {
      if(empty($planet[name]))
        $planet[name] = $l_igb_unnamed;
      echo "<option value=$planet[planet_id]>$planet[name] $l_igb_in $planet[sector_id]</option>";
    }
  }
  else
  {
     echo "<option value=none>$l_igb_none</option>";
  }


  echo "</select></td><td valign=center align=right>" .
       "<br><input style=\"background-color: #000000; color: #00FF00; font-family:Courier New; font-size:10pt\" type=submit name=planett value=\"$l_igb_planettransfer\">" .
       "</td></tr>" .
       "</form>";

// ---- begin Consol Credits form    // ---- added by Torr 
  echo "<tr valign=top>" .
       "<td><br><font size=2 face=\"courier new\" color=#00FF00>Consolidate Credits to a single planet :" .
       "<form action=IGB.php?command=transfer2 method=POST>" .
       "$l_igb_destination <select style=\"background-color: #000000; color: #00FF00; font-family:Courier New; font-size:10pt\" name=dplanet_id>";

  unset($splanet_id);

  if(isset($planets))
  {
    foreach($planets as $planet)
    {
      if(empty($planet[name]))
        $planet[name] = $l_igb_unnamed;
      echo "<option value=$planet[planet_id]>$planet[name] $l_igb_in $planet[sector_id]</option>";
    }
  }
  else
  {
     echo "<option value=none>$l_igb_none</option>";
  }

  echo "</select></td><td valign=center align=right>" .
       "<br><br><br><input style=\"background-color: #000000; color: #00FF00; font-family:Courier New; font-size:10pt\" type=submit name=planetc value=\"  Consolidate  \">" .
       "</td></tr>" .
       "</form>";
// ---- End Consol Credits form ---

  echo "</form><tr valign=bottom>" .
       "<td><font size=2 face=\"courier new\" color=#00FF00><a href=IGB.php?command=login>$l_igb_back</a></td><td align=right><font size=2 face=\"courier new\" color=#00FF00>&nbsp;<br><a href=\"main.php\">$l_igb_logout</a></td>" .
       "</tr>";
}

function IGB_transfer2()
{
  global $playerinfo;
  global $account;
  global $ship_id;
  global $splanet_id;
  global $dplanet_id;
  global $IGB_min_turns;
  global $IGB_svalue;
  global $ibank_paymentfee;
  global $IGB_trate;
  global $l_igb_sendyourself, $l_igb_unknowntargetship, $l_igb_min_turns, $l_igb_min_turns2;
  global $l_igb_mustwait, $l_igb_shiptransfer, $l_igb_igbaccount, $l_igb_maxtransfer;
  global $l_igb_unlimited, $l_igb_maxtransferpercent, $l_igb_transferrate, $l_igb_recipient;
  global $l_igb_seltransferamount, $l_igb_transfer, $l_igb_back, $l_igb_logout, $l_igb_in;
  global $l_igb_errplanetsrcanddest, $l_igb_errunknownplanet, $l_igb_unnamed;
  global $l_igb_errnotyourplanet, $l_igb_planettransfer, $l_igb_srcplanet, $l_igb_destplanet;
  global $l_igb_transferrate2, $l_igb_seltransferamount;
  global $db, $dbtables;

  if(isset($ship_id)) //ship transfer
  {
    $res = $db->Execute("SELECT * FROM $dbtables[ships] WHERE ship_id=$ship_id");

    if($playerinfo[ship_id] == $ship_id)
      IGB_error($l_igb_sendyourself, "IGB.php?command=transfer");

    if(!$res || $res->EOF)
      IGB_error($l_igb_unknowntargetship, "IGB.php?command=transfer");

    $target = $res->fields;

    if($target[turns_used] < $IGB_min_turns)
    {
      $l_igb_min_turns = str_replace("[igb_min_turns]", $IGB_min_turns, $l_igb_min_turns);
      $l_igb_min_turns = str_replace("[igb_target_char_name]", $target[character_name], $l_igb_min_turns);
      IGB_error($l_igb_min_turns, "IGB.php?command=transfer");
    }

    if($playerinfo[turns_used] < $IGB_min_turns)
    {
      $l_igb_min_turns2 = str_replace("[igb_min_turns]", $IGB_min_turns, $l_igb_min_turns2);
      IGB_error($l_igb_min_turns2, "IGB.php?command=transfer");
    }

    if($IGB_trate > 0)
    {
      $curtime = time();
      $curtime -= $IGB_trate * 60;
      $res = $db->Execute("SELECT UNIX_TIMESTAMP(time) as time FROM $dbtables[IGB_transfers] WHERE UNIX_TIMESTAMP(time) > $curtime AND source_id=$playerinfo[ship_id] AND dest_id=$target[ship_id]");
      if(!$res->EOF)
      {
        $time = $res->fields;
        $difftime = ($time[time] - $curtime) / 60;
        $l_igb_mustwait = str_replace("[igb_target_char_name]", $target[character_name], $l_igb_mustwait);
        $l_igb_mustwait = str_replace("[igb_trate]", NUMBER($IGB_trate), $l_igb_mustwait);
        $l_igb_mustwait = str_replace("[igb_difftime]", NUMBER($difftime), $l_igb_mustwait);
        IGB_error($l_igb_mustwait, "IGB.php?command=transfer");
      }
    }

    echo "<tr><td colspan=2 align=center valign=top><font size=2 face=\"courier new\" color=#00FF00>$l_igb_shiptransfer<br>---------------------------------</td></tr>" .
         "<tr valign=top><td><font size=2 face=\"courier new\" color=#00FF00>$l_igb_igbaccount :</td><td align=right><font size=2 face=\"courier new\" color=#00FF00>" . NUMBER($account[balance]) . " C</td></tr>";

    if($IGB_svalue == 0)
      echo "<tr valign=top><td><font size=2 face=\"courier new\" color=#00FF00>$l_igb_maxtransfer :</td><td align=right><font size=2 face=\"courier new\" color=#00FF00>$l_igb_unlimited</td></tr>";
    else
    {
      $percent = $IGB_svalue * 100;
      $score = gen_score($playerinfo[ship_id]);
      $maxtrans = $score * $score * $IGB_svalue;

      $l_igb_maxtransferpercent = str_replace("[igb_percent]", $percent, $l_igb_maxtransferpercent);
      echo "<tr valign=top><td nowrap><font size=2 face=\"courier new\" color=#00FF00>$l_igb_maxtransferpercent :</td><td align=right><font size=2 face=\"courier new\" color=#00FF00>" . NUMBER($maxtrans) . " C</td></tr>";
    }

    $percent = $ibank_paymentfee * 100;

    $l_igb_transferrate = str_replace("[igb_num_percent]", NUMBER($percent,1), $l_igb_transferrate);
    echo "<tr valign=top><td><font size=2 face=\"courier new\" color=#00FF00>$l_igb_recipient :</td><td align=right><font size=2 face=\"courier new\" color=#00FF00>$target[character_name]&nbsp;&nbsp;</td></tr>" .
         "<form action=IGB.php?command=transfer3 method=POST>" .
         "<tr valign=top>" .
         "<td><br><font size=2 face=\"courier new\" color=#00FF00>$l_igb_seltransferamount :</td>" .
         "<td align=right><br><input style=\"background-color: #000000; color: #00FF00; font-family:Courier New; font-size:10pt\" type=text size=15 maxlength=20 name=amount value=0><br>" .
         "<br><input style=\"background-color: #000000; color: #00FF00; font-family:Courier New; font-size:10pt\" type=submit value=$l_igb_transfer></td>" .
         "<input type=hidden name=ship_id value=$ship_id>" .
         "</form>" .
         "<tr><td colspan=2 align=center><font size=2 face=\"courier new\" color=#00FF00>" .
         "$l_igb_transferrate" .
         "<tr valign=bottom>" .
         "<td><font size=2 face=\"courier new\" color=#00FF00><a href=IGB.php?command=transfer>$l_igb_back</a></td><td align=right><font size=2 face=\"courier new\" color=#00FF00>&nbsp;<br><a href=\"main.php\">$l_igb_logout</a></td>" .
         "</tr>";
  }
// /-------------------- consolidate credit if stated
// for this to work we must "unset($splanet_id) when the consolidate button is clicked --- this may be a problem"
//
  elseif(isset($dplanet_id) && !isset($splanet_id))
  {
    $splanet_id = -1;
    $res = $db->Execute("SELECT name, credits, owner, sector_id FROM $dbtables[planets] WHERE planet_id=$dplanet_id");
    if(!$res || $res->EOF)
      IGB_error($l_igb_errunknownplanet, "IGB.php?command=transfer");
    $dest = $res->fields;

    if(empty($dest[name]))
      $dest[name]=$l_igb_unnamed;

    if($dest[owner] != $playerinfo[ship_id])
      IGB_error($l_igb_errnotyourplanet, "IGB.php?command=transfer");

    $percent = $ibank_paymentfee * 100;

    $l_igb_transferrate2 = str_replace("[igb_num_percent]", NUMBER($percent,1), $l_igb_transferrate2);

    // credit count fom all the planets you own
       // set a few local variabls to use
    $destplanetcreds  = $dest[credits];
    $totalplanetcreds = 0;

       // populate a structure and run a loop to  to calcualte total credist to transfer minus the destination planets credits
    $res = $db->Execute("SELECT name, planet_id, sector_id, credits FROM $dbtables[planets] WHERE owner=$playerinfo[ship_id] ORDER BY sector_id ASC");
    while(!$res->EOF)
    {
      $planets[]=$res->fields;
      $res->MoveNext();
    }

    foreach($planets as $planet)
    {
      $totalplanetcreds = $totalplanetcreds + $planet[credits];
    }

    $totaltranscreds  = $totalplanetcreds - $destplanetcreds;
 
    echo "<tr><td colspan=2 align=center valign=top><font size=2 face=\"courier new\" color=#00FF00>$l_igb_planettransfer<br>---------------------------------</td></tr>" .
         "<tr valign=top>" .
         "<td><font size=2 face=\"courier new\" color=#00FF00>Src -> Total Credits to consolidate  :" .
         "<td align=right><font size=2 face=\"courier new\" color=#00FF00>" . NUMBER($totaltranscreds) . " C" .
         "<tr valign=top>" .
         "<td><font size=2 face=\"courier new\" color=#00FF00>$l_igb_destplanet $dest[name] $l_igb_in $dest[sector_id] :" .
         "<td align=right><font size=2 face=\"courier new\" color=#00FF00>" . NUMBER($dest[credits]) . " C" .
         "<form action=IGB.php?command=transfer3 method=POST>" .
         "<tr valign=top>" .
         "<td><br><font size=2 face=\"courier new\" color=#00FF00>Are you sure you wish to consolidate your credits to $dest[name] $l_igb_in $dest[sector_id] :</td>" .
         "<td align=right><br><br>" .
         "<br><input style=\"background-color: #000000; color: #00FF00; font-family:Courier New; font-size:10pt\" type=submit value=\"  Consolidate  \"></td>" .
         "<input type=hidden name=splanet_id value=$splanet_id>" .
         "<input type=hidden name=dplanet_id value=$dplanet_id>" .
         "</form>" .
         "<tr><td colspan=2 align=center><font size=2 face=\"courier new\" color=#00FF00>" .
         "$l_igb_transferrate2" .
         "<tr valign=bottom>" .
         "<td><font size=2 face=\"courier new\" color=#00FF00><a href=IGB.php?command=transfer>$l_igb_back</a></td><td align=right><font size=2 face=\"courier new\" color=#00FF00>&nbsp;<br><a href=\"main.php\">$l_igb_logout</a></td>" .
         "</tr>";
   }
// /--------------------
  else
  {
    if($splanet_id == $dplanet_id)
      IGB_error($l_igb_errplanetsrcanddest, "IGB.php?command=transfer");

    $res = $db->Execute("SELECT name, credits, owner, sector_id FROM $dbtables[planets] WHERE planet_id=$splanet_id");
    if(!$res || $res->EOF)
      IGB_error($l_igb_errunknownplanet, "IGB.php?command=transfer");
    $source = $res->fields;
    

    if(empty($source[name]))
      $source[name]=$l_igb_unnamed;

    $res = $db->Execute("SELECT name, credits, owner, sector_id, base FROM $dbtables[planets] WHERE planet_id=$dplanet_id");
    if(!$res || $res->EOF)
      IGB_error($l_igb_errunknownplanet, "IGB.php?command=