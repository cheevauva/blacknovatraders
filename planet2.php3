<?

include("config.php3");
updatecookie();

$title="Planetary Transfer";
include("header.php3");

connectdb();
if(checklogin())
{
  die();
}

//-------------------------------------------------------------------------------------------------
mysql_query("LOCK TABLES ships WRITE, universe WRITE, planets WRITE");

$result = mysql_query("SELECT * FROM ships WHERE email='$username'");
$playerinfo = mysql_fetch_array($result);

$result2 = mysql_query("SELECT * FROM planets WHERE planet_id=$planet_id");
if($result2)
  $planetinfo=mysql_fetch_array($result2);

bigtitle();

if($playerinfo[turns] < 1)
{
  echo "You need at least one turn to perform a planetary transfer.<BR><BR>";
}
else
{
  $free_holds = NUM_HOLDS($playerinfo[hull]) - $playerinfo[ship_ore] - $playerinfo[ship_organics] - $playerinfo[ship_goods] - $playerinfo[ship_colonists];
  $free_power = NUM_ENERGY($playerinfo[power]) - $playerinfo[ship_energy];
  $fighter_max = NUM_FIGHTERS($playerinfo[computer]) - $playerinfo[ship_fighters];
  $torpedo_max = NUM_TORPEDOES($playerinfo[torp_launchers]) - $playerinfo[torps];  

  // first setup the tp flags
  if($tpore != -1)
  {
    $tpore = 1;
  }
  if($tporganics != -1)
  {
    $tporganics  = 1;
  }
  if($tpgoods != -1)
  {
    $tpgoods = 1;
  }
  if($tpenergy != -1)
  {
    $tpenergy = 1;
  }
  if($tpcolonists != -1)
  {
    $tpcolonists = 1;
  }
  if($tpcredits != -1)
  {
    $tpcredits = 1;
  }
  if($tptorps != -1)
  {
    $tptorps = 1;
  }
  if($tpfighters != -1)
  {
    $tpfighters = 1;
  }

  // now multiply all the transfer amounts by 1 to eliminate any trailing spaces
  $transfer_ore = $transfer_ore * 1;
  $transfer_organics = $transfer_organics * 1;
  $transfer_goods = $transfer_goods * 1;
  $transfer_energy = $transfer_energy * 1;
  $transfer_colonists = $transfer_colonists * 1;
  $transfer_credits = $transfer_credits * 1;
  $transfer_torps = $transfer_torps * 1;
  $transfer_fighters = $transfer_fighters * 1;

  // ok now get rid of all negative amounts so that all operations are expressed in terms of positive units
  if($transfer_ore < 0)
  {
    $transfer_ore = -1 * $transfer_ore;
    $tpore = -1 * $tpore;
  }

  if($transfer_organics < 0)
  {
    $transfer_organics = -1 * $transfer_organics;
    $tporganics = -1 * $tporganics;
  }

  if($transfer_goods < 0)
  {
    $transfer_goods = -1 * $transfer_goods;
    $tpgoods = -1 * $tpgoods;
  }

  if($transfer_energy < 0)
  {
    $transfer_energy = -1 * $transfer_energy;
    $tpenergy = -1 * $tpenergy;
  }

  if($transfer_colonists < 0)
  {
    $transfer_colonists = -1 * $transfer_colonistst;
    $tpcolonists = -1 * $tpcolonists;
  }

  if($transfer_credits < 0)
  {
    $transfer_credits = -1 * $transfer_credits;
    $tpcredits = -1 * $tpcredits;
  }

  if($transfer_torps < 0)
  {
    $transfer_torps = -1 * $transfer_torps;
    $tptorps = -1 * $tptorps;
  }

  if($transfer_fighters < 0)
  {
    $transfer_fighters = -1 * $transfer_fighters;
    $tpfighters = -1 * $tpfighters;
  }

  // now make sure that the source for each commodity transfer has sufficient numbers to fill the transfer
  if(($tpore == -1) && ($transfer_ore > $playerinfo['ship_ore']))
  {
    $transfer_ore = $playerinfo['ship_ore'];
    echo "You don't have enough ore. Setting ore transfer amount to $transfer_ore.<BR>\n";
  }
  elseif(($tpore == 1) && ($transfer_ore > $planetinfo['ore']))
  {
    $transfer_ore = $planetinfo['ore'];
    echo "The planet was only able to supply $transfer_ore units of ore.<BR>\n";
  }

  if(($tporganics == -1) && ($transfer_organics > $playerinfo['ship_organics']))
  {
    $transfer_organics = $playerinfo['ship_organics'];
    echo "You don't have enough organics. Setting organics transfer amount to $transfer_organics.<BR>\n";
  }
  elseif(($tporganics == 1) && ($transfer_organics > $planetinfo['organics']))
  {
    $transfer_organics = $planetinfo['organics'];
    echo "The planet was only able to supply $transfer_organics units of organics.<BR>\n";
  }

  if(($tpgoods == -1) && ($transfer_goods > $playerinfo['ship_goods']))
  {
    $transfer_goods = $playerinfo['ship_goods'];
    echo "You don't have enough goods. Setting goods transfer amount to $transfer_goods.<BR>\n";
  }
  elseif(($tpgoods == 1) && ($transfer_goods > $planetinfo['goods']))
  {
    $transfer_goods = $planetinfo['goods'];
    echo "The planet was only able to supply $transfer_goods units of goods.<BR>\n";
  }

  if(($tpenergy == -1) && ($transfer_energy > $playerinfo['ship_energy']))
  {
    $transfer_energy = $playerinfo['ship_energy'];
    echo "You don't have enough energy. Setting energy transfer amount to $transfer_energy.<BR>\n";
  }
  elseif(($tpenergy == 1) && ($transfer_energy > $planetinfo['energy']))
  {
    $transfer_energy = $planetinfo['energy'];
    echo "The planet was only able to supply $transfer_energy units of energy.<BR>\n";
  }

  if(($tpcolonists == -1) && ($transfer_colonists > $playerinfo['ship_colonists']))
  {
    $transfer_colonists = $playerinfo['ship_colonists'];
    echo "You don't have enough colonists. Setting colonists transfer amount to $transfer_colonists.<BR>\n";
  }
  elseif(($tpcolonists == 1) && ($transfer_colonists > $planetinfo['colonists']))
  {
    $transfer_colonists = $planetinfo['colonists'];
    echo "The planet was only able to supply $transfer_colonists units of colonists.<BR>\n";
  }

  if(($tpcredits == -1) && ($transfer_credits > $playerinfo['credits']))
  {
    $transfer_credits = $playerinfo['credits'];
    echo "You don't have enough credits. Setting credits transfer amount to $transfer_credits.<BR>\n";
  }
  elseif(($tpcredits == 1) && ($transfer_credits > $planetinfo['credits']))
  {
    $transfer_credits = $planetinfo['credits'];
    echo "The planet was only able to supply $transfer_credits units of credits.<BR>\n";
  }

  if(($tptorps == -1) && ($transfer_torps > $playerinfo['torps']))
  {
    $transfer_torps = $playerinfo['torps'];
    echo "You don't have enough torps. Setting torps transfer amount to $transfer_torps.<BR>\n";
  }
  elseif(($tptorps == 1) && ($transfer_torps > $planetinfo['torps']))
  {
    $transfer_torps = $planetinfo['torps'];
    echo "The planet was only able to supply $transfer_torps units of torps.<BR>\n";
  }

  if(($tpfighters == -1) && ($transfer_fighters > $playerinfo['ship_fighters']))
  {
    $transfer_fighters = $playerinfo['ship_fighters'];
    echo "You don't have enough fighters. Setting fighters transfer amount to $transfer_fighters.<BR>\n";
  }
  elseif(($tpfighters == 1) && ($transfer_fighters > $planetinfo['fighters']))
  {
    $transfer_fighters = $planetinfo['fighters'];
    echo "The planet was only able to supply $transfer_fighters units of fighters.<BR>\n";
  }

  // Now that we have the amounts adjusted to suit available resources, go ahead and multiply them by their tpflag.
  $transfer_ore = $transfer_ore * $tpore;
  $transfer_organics = $transfer_organics * $tporganics;
  $transfer_goods = $transfer_goods * $tpgoods;
  $transfer_energy = $transfer_energy * $tpenergy;
  $transfer_colonists = $transfer_colonists * $tpcolonists;
  $transfer_credits = $transfer_credits * $tpcredits;
  $transfer_torps = $transfer_torps * $tptorps;
  $transfer_fighters = $transfer_fighters * $tpfighters;

  $total_holds_needed = $transfer_ore + $transfer_organics + $transfer_goods + $transfer_colonists;

  if($total_holds_needed > $free_holds)
  {
    echo "Not enough holds for requested transfer.<BR><BR>";
    echo "Click <A HREF=planet.php3?planet_id=$planet_id>here</A> to return to planet menu.<BR><BR>";
  }
  else
  {
    if(!empty($planetinfo) == "Y")
    {
      if($planetinfo[owner] == $playerinfo[ship_id] || $planetinfo[corp] == $playerinfo[team])
      {
        if($transfer_ore < 0 && $playerinfo[ship_ore] < abs($transfer_ore))
        {
          echo "Not enough ore for requested transfer.<BR>";
          $transfer_ore = 0;
        }
        elseif($transfer_ore > 0 && $planetinfo[ore] < abs($transfer_ore))
        {
          echo "Not enough ore for requested transfer.<BR>";
          $transfer_ore = 0;
        }
        if($transfer_organics < 0 && $playerinfo[ship_organics] < abs($transfer_organics))
        {
          echo "Not enough organics for requested transfer.<BR>";
          $transfer_organics = 0;
        }
        elseif($transfer_organics > 0 && $planetinfo[organics] < abs($transfer_organics))
        {
          echo "Not enough organics for requested transfer.<BR>";
          $transfer_organics = 0;
        }
        if($transfer_goods < 0 && $playerinfo[ship_goods] < abs($transfer_goods))
        {
          echo "Not enough goods for requested transfer.<BR>";
          $transfer_goods = 0;
        }
        elseif($transfer_goods > 0 && $planetinfo[goods] < abs($transfer_goods))
        {
          echo "Not enough goods for requested transfer.<BR>";
          $transfer_goods = 0;
        }
        if($transfer_energy < 0 && $playerinfo[ship_energy] < abs($transfer_energy))
        {
          echo "Not enough energy for requested transfer.<BR>";
          $transfer_energy = 0;
        }
        elseif($transfer_energy > 0 && $planetinfo[energy] < abs($transfer_energy))
        {
          echo "Not enough energy for requested transfer.<BR>";
          $transfer_energy = 0;
        }
        elseif($transfer_energy > 0 && abs($transfer_energy) > $free_power)
        {
          echo "Not enough power capacity for requested energy transfer.<BR>";
          $transfer_energy = 0;
        }       
        if($transfer_colonists < 0 && $playerinfo[ship_colonists] < abs($transfer_colonists))
        {
          echo "Not enough colonists for requested transfer.<BR>";
          $transfer_colonists = 0;
        }
        elseif($transfer_colonists > 0 && $planetinfo[colonists] < abs($transfer_colonists))
        {
          echo "Not enough colonists for requested transfer.<BR>";
          $transfer_colonists = 0;
        }
        if($transfer_fighters < 0 && $playerinfo[ship_fighters] < abs($transfer_fighters))
        {
          echo "Not enough fighters for requested transfer.<BR>";
          $transfer_fighters = 0;
        }
        elseif($transfer_fighters > 0 && $planetinfo[fighters] < abs($transfer_fighters))
        {
          echo "Not enough fighters for requested transfer.<BR>";
          $transfer_fighters = 0;
        }
        elseif($transfer_fighters > 0 && abs($transfer_fighters) > $fighter_max)
        {
          echo "Not enough computer capacity for requested fighters transfer.<BR>";
          $transfer_fighters = 0;
        }
        if($transfer_torps < 0 && $playerinfo[torps] < abs($transfer_torps))
        {
          echo "Not enough torpedoes for requested transfer.<BR>";
          $transfer_torps = 0;
        }
        elseif($transfer_torps > 0 && $planetinfo[torps] < abs($transfer_torps))
        {
          echo "Not enough torpedoes for requested transfer.<BR>";
          $transfer_torps = 0;
        }
        elseif($transfer_torps > 0 && abs($transfer_torps) > $torpedo_max)
        {
          echo "Not enough launcher capacity for requested torpedo transfer.<BR>";
          $transfer_torps = 0;
        }
        if($transfer_credits < 0 && $playerinfo[credits] < abs($transfer_credits))
        {
          echo "Not enough credits for requested transfer.<BR>";
          $transfer_credits = 0;
        }
        elseif($transfer_credits > 0 && $planetinfo[credits] < abs($transfer_credits))
        {
          echo "Not enough credits for requested transfer.<BR>";
          $transfer_credits = 0;
        }
        $update1 = mysql_query("UPDATE ships SET ship_ore=ship_ore+$transfer_ore, ship_organics=ship_organics+$transfer_organics, ship_goods=ship_goods+$transfer_goods, ship_energy=ship_energy+$transfer_energy, ship_colonists=ship_colonists+$transfer_colonists, torps=torps+$transfer_torps, ship_fighters=ship_fighters+$transfer_fighters, credits=credits+$transfer_credits, turns=turns-1, turns_used=turns_used+1 WHERE ship_id=$playerinfo[ship_id]");
        $update2 = mysql_query("UPDATE planets SET ore=ore-$transfer_ore, organics=organics-$transfer_organics, goods=goods-$transfer_goods, energy=energy-$transfer_energy, colonists=colonists-$transfer_colonists, torps=torps-$transfer_torps, fighters=fighters-$transfer_fighters, credits=credits-$transfer_credits WHERE planet_id=$planet_id");
        echo "Transfer complete.<BR>Click <a href=planet.php3?planet_id=$planet_id>here</a> to return to planet menu.<BR><BR>";
      }
      else
      {
        echo "You do not own this planet.<BR><BR>";
      }
    }
    else
    {
      echo "There is no planet here.<BR><BR>";
    }
  }
}

mysql_query("UNLOCK TABLES");
//-------------------------------------------------------------------------------------------------

TEXT_GOTOMAIN();

include("footer.php3");

?> 
