<?

function furangeetoship($ship_id)
{
  // *********************************
  // *** SETUP GENERAL VARIABLES  ****
  // *********************************
  global $attackerbeams;
  global $attackerfighters;
  global $attackershields;
  global $attackertorps;
  global $attackerarmor;
  global $attackertorpdamage;
  global $start_energy;
  global $playerinfo;
  global $rating_combat_factor;
  global $upgrade_cost;
  global $upgrade_factor;
  global $sector_max;

  // *********************************
  // ** VERIFY SECTOR ALLOWS ATTACK **
  // *********************************
  $sectres = mysql_query ("SELECT sector_id,zone_id FROM universe WHERE sector_id='$playerinfo[sector]'");
  $sectrow = mysql_fetch_array($sectres);
  $zoneres = mysql_query ("SELECT zone_id,allow_attack FROM zones WHERE zone_id=$sectrow[1]");
  $zonerow = mysql_fetch_array($zoneres);
  if ($zonerow[1]=="N")                        //*** DEST LINK MUST ALLOW ATTACKING ***
  {
    playerlog($playerinfo[ship_id],"Attack failed, you are in a sector that prohibits attacks."); 
    return;
  }

  // *********************************
  // *** LOOKUP TARGET DETAILS    ****
  // *********************************
  mysql_query("LOCK TABLES ships WRITE, universe WRITE, zones READ, planets READ, bn_news WRITE");
  $resultt = mysql_query ("SELECT * FROM ships WHERE ship_id='$ship_id'");
  $targetinfo=mysql_fetch_array($resultt);

  // *********************************
  // *** USE EMERGENCY WARP DEVICE ***
  // *********************************
  if ($targetinfo[dev_emerwarp]>0)
  {
    playerlog($targetinfo[ship_id], "A Furangee named $playerinfo[character_name] attacked you.  Your emergency warp device engaged.<BR>");
    $dest_sector=rand(0,$sector_max);
    $result_warp = mysql_query ("UPDATE ships SET sector=$dest_sector, dev_emerwarp=dev_emerwarp-1 WHERE ship_id=$targetinfo[ship_id]");
    return;
  }

  // *********************************
  // *** SETUP ATTACKER VARIABLES ****
  // *********************************
  $attackerbeams = NUM_BEAMS($playerinfo[beams]);
  if ($attackerbeams > $playerinfo[ship_energy]) $attackerbeams = $playerinfo[ship_energy];
  $playerinfo[ship_energy] = $playerinfo[ship_energy] - $attackerbeams;
  $attackershields = NUM_SHIELDS($playerinfo[shields]);
  if ($attackershields > $playerinfo[ship_energy]) $attackershields = $playerinfo[ship_energy];
  $playerinfo[ship_energy] = $playerinfo[ship_energy] - $attackershields;
  $attackertorps = round(pow($level_factor, $playerinfo[torp_launchers])) * 2;
  if ($attackertorps > $playerinfo[torps]) $attackertorps = $playerinfo[torps];
  $playerinfo[torps] = $playerinfo[torps] - $attackertorps;
  $attackertorpdamage = $torp_dmg_rate * $attackertorps;
  $attackerarmor = $playerinfo[armour_pts];
  $attackerfighters = $playerinfo[ship_fighters];
  $playerdestroyed = 0;

  // *********************************
  // **** SETUP TARGET VARIABLES *****
  // *********************************
  $targetbeams = NUM_BEAMS($targetinfo[beams]);
  if ($targetbeams>$targetinfo[ship_energy]) $targetbeams=$targetinfo[ship_energy];
  $targetinfo[ship_energy]=$targetinfo[ship_energy]-$targetbeams;
  $targetshields = NUM_SHIELDS($targetinfo[shields]);
  if ($targetshields>$targetinfo[ship_energy]) $targetshields=$targetinfo[ship_energy];
  $targetinfo[ship_energy]=$targetinfo[ship_energy]-$targetshields;
  $targettorpnum = round(pow($level_factor,$targetinfo[torp_launchers]))*2;
  if ($targettorpnum > $targetinfo[torps]) $targettorpnum = $targetinfo[torps];
  $targetinfo[torps] = $targetinfo[torps] - $targettorpnum;
  $targettorpdmg = $torp_dmg_rate*$targettorpnum;
  $targetarmor = $targetinfo[armour_pts];
  $targetfighters = $targetinfo[ship_fighters];
  $targetdestroyed = 0;

  // *********************************
  // **** BEGIN COMBAT PROCEDURES ****
  // *********************************
  if($attackerbeams > 0 && $targetfighters > 0)
  {                                                  //******** ATTACKER HAS BEAMS - TARGET HAS FIGHTERS - BEAMS VS FIGHTERS ********
    if($attackerbeams > round($targetfighters / 2))
    {                                                           //****** ATTACKER BEAMS GT HALF TARGET FIGHTERS ******
      $lost = $targetfighters-(round($targetfighters/2));
      $targetfighters = $targetfighters-$lost;                           //**** TARGET LOOSES HALF ALL FIGHTERS ****
      $attackerbeams = $attackerbeams-$lost;                             //**** ATTACKER LOOSES BEAMS EQ TO HALF TARGET FIGHTERS ****
    } else
    {                                                           //****** ATTACKER BEAMS LE HALF TARGET FIGHTERS ******
      $targetfighters = $targetfighters-$attackerbeams;                  //**** TARGET LOOSES FIGHTERS EQ TO ATTACKER BEAMS ****
      $attackerbeams = 0;                                                //**** ATTACKER LOOSES ALL BEAMS ****
    }   
  }
  if($attackerfighters > 0 && $targetbeams > 0)
  {                                                  //******** TARGET HAS BEAMS - ATTACKER HAS FIGHTERS - BEAMS VS FIGHTERS ********
    if($targetbeams > round($attackerfighters / 2))
    {                                                           //****** TARGET BEAMS GT HALF ATTACKER FIGHTERS ******
      $lost=$attackerfighters-(round($attackerfighters/2));
      $attackerfighters=$attackerfighters-$lost;                         //**** ATTACKER LOOSES HALF ALL FIGHTERS ****
      $targetbeams=$targetbeams-$lost;                                   //**** TARGET LOOSES BEAMS EQ TO HALF ATTACKER FIGHTERS ****
    } else
    {                                                           //****** TARGET BEAMS LE HALF ATTACKER FIGHTERS ******
      $attackerfighters=$attackerfighters-$targetbeams;                  //**** ATTACKER LOOSES FIGHTERS EQ TO TARGET BEAMS **** 
      $targetbeams=0;                                                    //**** TARGET LOOSES ALL BEAMS ****
    }
  }
  if($attackerbeams > 0)
  {                                                  //******** ATTACKER HAS BEAMS LEFT - CONTINUE COMBAT - BEAMS VS SHIELDS ********
    if($attackerbeams > $targetshields)
    {                                                           //****** ATTACKER BEAMS GT TARGET SHIELDS ******
      $attackerbeams=$attackerbeams-$targetshields;                      //**** ATTACKER LOOSES BEAMS EQ TO TARGET SHIELDS ****
      $targetshields=0;                                                  //**** TARGET LOOSES ALL SHIELDS ****
    } else
    {                                                           //****** ATTACKER BEAMS LE TARGET SHIELDS ******
      $targetshields=$targetshields-$attackerbeams;                      //**** TARGET LOOSES SHIELDS EQ TO ATTACKER BEAMS ****
      $attackerbeams=0;                                                  //**** ATTACKER LOOSES ALL BEAMS ****
    }
  }
  if($targetbeams > 0)
  {                                                  //******** TARGET HAS BEAMS LEFT - CONTINUE COMBAT - BEAMS VS SHIELDS ********
    if($targetbeams > $attackershields)
    {                                                           //****** TARGET BEAMS GT ATTACKER SHIELDS ******
      $targetbeams=$targetbeams-$attackershields;                        //**** TARGET LOOSES BEAMS EQ TO ATTACKER SHIELDS ****
      $attackershields=0;                                                //**** ATTACKER LOOSES ALL SHIELDS ****
    } else
    {                                                           //****** TARGET BEAMS LE ATTACKER SHIELDS ****** 
      $attackershields=$attackershields-$targetbeams;                    //**** ATTACKER LOOSES SHIELDS EQ TO TARGET BEAMS ****
      $targetbeams=0;                                                    //**** TARGET LOOSES ALL BEAMS ****
    }
  }
  if($attackerbeams > 0)
  {                                                  //******** ATTACKER HAS BEAMS LEFT - CONTINUE COMBAT - BEAMS VS ARMOR ********
    if($attackerbeams > $targetarmor)
    {                                                           //****** ATTACKER BEAMS GT TARGET ARMOR ******
      $attackerbeams=$attackerbeams-$targetarmor;                        //**** ATTACKER LOOSES BEAMS EQ TO TARGET ARMOR ****
      $targetarmor=0;                                                    //**** TARGET LOOSES ALL ARMOR (TARGET SHIP DESTROYED) ****
    } else
    {                                                           //****** ATTACKER BEAMS LE TARGET ARMOR ******
      $targetarmor=$targetarmor-$attackerbeams;                          //**** TARGET LOOSES ARMORS EQ TO ATTACKER BEAMS ****
      $attackerbeams=0;                                                  //**** ATTACKER LOOSES ALL BEAMS ****
    } 
  }
  if($targetbeams > 0)
  {                                                 //******** TARGET HAS BEAMS LEFT - CONTINUE COMBAT - BEAMS VS ARMOR ******** 
    if($targetbeams > $attackerarmor)
    {                                                          //****** TARGET BEAMS GT ATTACKER ARMOR ******
      $targetbeams=$targetbeams-$attackerarmor;                         //**** TARGET LOOSES BEAMS EQ TO ATTACKER ARMOR ****
      $attackerarmor=0;                                                 //**** ATTACKER LOOSES ALL ARMOR (ATTACKER SHIP DESTROYED) ****
    } else
    {                                                          //****** TARGET BEAMS LE ATTACKER ARMOR ******
      $attackerarmor=$attackerarmor-$targetbeams;                       //**** ATTACKER LOOSES ARMOR EQ TO TARGET BEAMS ****
      $targetbeams=0;                                                   //**** TARGET LOOSES ALL BEAMS ****
    } 
  }
  if($targetfighters > 0 && $attackertorpdamage > 0)
  {                                                 //******** ATTACKER FIRES TORPS - TARGET HAS FIGHTERS - TORPS VS FIGHTERS ********
    if($attackertorpdamage > round($targetfighters / 2))
    {                                                          //****** ATTACKER FIRED TORPS GT HALF TARGET FIGHTERS ******
      $lost=$targetfighters-(round($targetfighters/2));
      $targetfighters=$targetfighters-$lost;                            //**** TARGET LOOSES HALF ALL FIGHTERS ****
      $attackertorpdamage=$attackertorpdamage-$lost;                    //**** ATTACKER LOOSES FIRED TORPS EQ TO HALF TARGET FIGHTERS ****
    } else
    {                                                          //****** ATTACKER FIRED TORPS LE HALF TARGET FIGHTERS ******
      $targetfighters=$targetfighters-$attackertorpdamage;              //**** TARGET LOOSES FIGHTERS EQ TO ATTACKER TORPS FIRED ****
      $attackertorpdamage=0;                                            //**** ATTACKER LOOSES ALL TORPS FIRED ****
    }
  }
  if($attackerfighters > 0 && $targettorpdmg > 0)
  {                                                 //******** TARGET FIRES TORPS - ATTACKER HAS FIGHTERS - TORPS VS FIGHTERS ********
    if($targettorpdmg > round($attackerfighters / 2))
    {                                                          //****** TARGET FIRED TORPS GT HALF ATTACKER FIGHTERS ******
      $lost=$attackerfighters-(round($attackerfighters/2));
      $attackerfighters=$attackerfighters-$lost;                        //**** ATTACKER LOOSES HALF ALL FIGHTERS ****
      $targettorpdmg=$targettorpdmg-$lost;                              //**** TARGET LOOSES FIRED TORPS EQ TO HALF ATTACKER FIGHTERS ****
    } else
    {                                                          //****** TARGET FIRED TORPS LE HALF ATTACKER FIGHTERS ******
      $attackerfighters=$attackerfighters-$targettorpdmg;               //**** ATTACKER LOOSES FIGHTERS EQ TO TARGET TORPS FIRED ****
      $targettorpdmg=0;                                                 //**** TARGET LOOSES ALL TORPS FIRED ****
    }
  }
  if($attackertorpdamage > 0)
  {                                                 //******** ATTACKER FIRES TORPS - CONTINUE COMBAT - TORPS VS ARMOR ********
    if($attackertorpdamage > $targetarmor)
    {                                                          //****** ATTACKER FIRED TORPS GT HALF TARGET ARMOR ******
      $attackertorpdamage=$attackertorpdamage-$targetarmor;             //**** ATTACKER LOOSES FIRED TORPS EQ TO TARGET ARMOR ****
      $targetarmor=0;                                                   //**** TARGET LOOSES ALL ARMOR (TARGET SHIP DESTROYED) ****
    } else
    {                                                          //****** ATTACKER FIRED TORPS LE HALF TARGET ARMOR ******
      $targetarmor=$targetarmor-$attackertorpdamage;                    //**** TARGET LOOSES ARMOR EQ TO ATTACKER TORPS FIRED ****
      $attackertorpdamage=0;                                            //**** ATTACKER LOOSES ALL TORPS FIRED ****
    } 
  }
  if($targettorpdmg > 0)
  {                                                 //******** TARGET FIRES TORPS - CONTINUE COMBAT - TORPS VS ARMOR ********
    if($targettorpdmg > $attackerarmor)
    {                                                          //****** TARGET FIRED TORPS GT HALF ATTACKER ARMOR ******
      $targettorpdmg=$targettorpdmg-$attackerarmor;                     //**** TARGET LOOSES FIRED TORPS EQ TO ATTACKER ARMOR ****
      $attackerarmor=0;                                                 //**** ATTACKER LOOSES ALL ARMOR (ATTACKER SHIP DESTROYED) ****
    } else
    {                                                          //****** TARGET FIRED TORPS LE HALF ATTACKER ARMOR ******
      $attackerarmor=$attackerarmor-$targettorpdmg;                     //**** ATTACKER LOOSES ARMOR EQ TO TARGET TORPS FIRED ****
      $targettorpdmg=0;                                                 //**** TARGET LOOSES ALL TORPS FIRED ****
    } 
  }
  if($attackerfighters > 0 && $targetfighters > 0)
  {                                                 //******** ATTACKER HAS FIGHTERS - TARGET HAS FIGHTERS - FIGHTERS VS FIGHTERS ********
    if($attackerfighters > $targetfighters)
    {                                                          //****** ATTACKER FIGHTERS GT TARGET FIGHTERS ******
      $temptargfighters=0;                                              //**** TARGET WILL LOOSE ALL FIGHTERS ****
    } else
    {                                                          //****** ATTACKER FIGHTERS LE TARGET FIGHTERS ******
      $temptargfighters=$targetfighters-$attackerfighters;              //**** TARGET WILL LOOSE FIGHTERS EQ TO ATTACKER FIGHTERS ****
    }
    if($targetfighters > $attackerfighters)
    {                                                          //****** TARGET FIGHTERS GT ATTACKER FIGHTERS ******
      $tempplayfighters=0;                                              //**** ATTACKER WILL LOOSE ALL FIGHTERS ****
    } else
    {                                                          //****** TARGET FIGHTERS LE ATTACKER FIGHTERS ******
      $tempplayfighters=$attackerfighters-$targetfighters;              //**** ATTACKER WILL LOOSE FIGHTERS EQ TO TARGET FIGHTERS ****
    }     
    $attackerfighters=$tempplayfighters;
    $targetfighters=$temptargfighters;
  }
  if($attackerfighters > 0)
  {                                                 //******** ATTACKER HAS FIGHTERS - CONTINUE COMBAT - FIGHTERS VS ARMOR ********
    if($attackerfighters > $targetarmor)
    {                                                          //****** ATTACKER FIGHTERS GT TARGET ARMOR ******
      $targetarmor=0;                                                   //**** TARGET LOOSES ALL ARMOR (TARGET SHIP DESTROYED) ****
    } else
    {                                                          //****** ATTACKER FIGHTERS LE TARGET ARMOR ******
      $targetarmor=$targetarmor-$attackerfighters;                      //**** TARGET LOOSES ARMOR EQ TO ATTACKER FIGHTERS **** 
    }
  }
  if($targetfighters > 0)
  {                                                 //******** TARGET HAS FIGHTERS - CONTINUE COMBAT - FIGHTERS VS ARMOR ********
    if($targetfighters > $attackerarmor)
    {                                                          //****** TARGET FIGHTERS GT ATTACKER ARMOR ******
      $attackerarmor=0;                                                 //**** ATTACKER LOOSES ALL ARMOR (ATTACKER SHIP DESTROYED) ****
    } else
    {                                                          //****** TARGET FIGHTERS LE ATTACKER ARMOR ******
      $attackerarmor=$attackerarmor-$targetfighters;                    //**** ATTACKER LOOSES ARMOR EQ TO TARGET FIGHTERS ****
    }
  }

  // *********************************
  // **** FIX NEGATIVE VALUE VARS ****
  // *********************************
  if ($attackerfighters < 0) $attackerfighters = 0;
  if ($attackertorps    < 0) $attackertorps = 0;
  if ($attackershields  < 0) $attackershields = 0;
  if ($attackerbeams    < 0) $attackerbeams = 0;
  if ($attackerarmor    < 0) $attackerarmor = 0;
  if ($targetfighters   < 0) $targetfighters = 0;
  if ($targettorpnum    < 0) $targettorpnum = 0;
  if ($targetshields    < 0) $targetshields = 0;
  if ($targetbeams      < 0) $targetbeams = 0;
  if ($targetarmor      < 0) $targetarmor = 0;

  // *********************************
  // *** DEAL WITH DESTROYED SHIPS ***
  // *********************************

  // *********************************
  // *** TARGET SHIP WAS DESTROYED ***
  // *********************************
  if(!$targetarmor>0)
  {
    if($targetinfo[dev_escapepod] == "Y")
    // ****** TARGET HAD ESCAPE POD ******
    {
      $rating=round($targetinfo[rating]/2);
      mysql_query("UPDATE ships SET hull=0, engines=0, power=0, computer=0,sensors=0, beams=0, torp_launchers=0, torps=0, armour=0, armour_pts=100, cloak=0, shields=0, sector=0, ship_ore=0, ship_organics=0, ship_energy=1000, ship_colonists=0, ship_goods=0, ship_fighters=100, ship_damage='', on_planet='N', dev_warpedit=0, dev_genesis=0, dev_beacon=0, dev_emerwarp=0, dev_escapepod='N', dev_fuelscoop='N', dev_minedeflector=0, ship_destroyed='N', rating='$rating' where ship_id=$targetinfo[ship_id]");
      playerlog($targetinfo[ship_id],"A Furangee named $playerinfo[character_name] attacked you, and destroyed your ship!  Luckily you had an escape pod!<BR>"); 
    } else
    // ****** TARGET HAD NO POD ******
    {
      playerlog($targetinfo[ship_id],"A Furangee named $playerinfo[character_name] attacked you, and destroyed your ship!<BR>"); 
      db_kill_player($targetinfo['ship_id']);
    }   
    if($attackerarmor>0)
    {
      // ****** ATTACKER STILL ALIVE TO SALVAGE TRAGET ******
      $rating_change=round($targetinfo[rating]*$rating_combat_factor);
      $free_ore = round($targetinfo[ship_ore]/2);
      $free_organics = round($targetinfo[ship_organics]/2);
      $free_goods = round($targetinfo[ship_goods]/2);
      $free_holds = NUM_HOLDS($playerinfo[hull]) - $playerinfo[ship_ore] - $playerinfo[ship_organics] - $playerinfo[ship_goods] - $playerinfo[ship_colonists];
      if($free_holds > $free_goods) 
      {                                                        //****** FIGURE OUT WHAT WE CAN CARRY ******
        $salv_goods=$free_goods;
        $free_holds=$free_holds-$free_goods;
      } elseif($free_holds > 0)
      {
        $salv_goods=$free_holds;
        $free_holds=0;
      } else
      {
        $salv_goods=0;
      }
      if($free_holds > $free_ore)
      {
        $salv_ore=$free_ore;
        $free_holds=$free_holds-$free_ore;
      } elseif($free_holds > 0)
      {
        $salv_ore=$free_holds;
        $free_holds=0;
      } else
      {
        $salv_ore=0;
      }
      if($free_holds > $free_organics)
      {
        $salv_organics=$free_organics;
        $free_holds=$free_holds-$free_organics;
      } elseif($free_holds > 0)
      {
        $salv_organics=$free_holds;
        $free_holds=0;
      } else
      {
        $salv_organics=0;
      }
      $ship_value=$upgrade_cost*(round(pow($upgrade_factor, $targetinfo[hull]))+round(pow($upgrade_factor, $targetinfo[engines]))+round(pow($upgrade_factor, $targetinfo[power]))+round(pow($upgrade_factor, $targetinfo[computer]))+round(pow($upgrade_factor, $targetinfo[sensors]))+round(pow($upgrade_factor, $targetinfo[beams]))+round(pow($upgrade_factor, $targetinfo[torp_launchers]))+round(pow($upgrade_factor, $targetinfo[shields]))+round(pow($upgrade_factor, $targetinfo[armor]))+round(pow($upgrade_factor, $targetinfo[cloak])));
      $ship_salvage_rate=rand(10,20);
      $ship_salvage=$ship_value*$ship_salvage_rate/100;
      playerlog($playerinfo[ship_id],"Attack successful, $targetinfo[character_name] was defeated and salvaged for $ship_salvage credits."); 
      mysql_query ("UPDATE ships SET ship_ore=ship_ore+$salv_ore, ship_organics=ship_organics+$salv_organics, ship_goods=ship_goods+$salv_goods, credits=credits+$ship_salvage WHERE ship_id=$playerinfo[ship_id]");
      $armor_lost = $playerinfo[armour_pts] - $attackerarmor;
      $fighters_lost = $playerinfo[ship_fighters] - $attackerfighters;
      $energy=$playerinfo[ship_energy];
      mysql_query ("UPDATE ships SET ship_energy=$energy,ship_fighters=ship_fighters-$fighters_lost, torps=torps-$attackertorps,armour_pts=armour_pts-$armor_lost, rating=rating-$rating_change WHERE ship_id=$playerinfo[ship_id]");
    }
  }

  // *********************************
  // *** TARGET AND ATTACKER LIVE  ***
  // *********************************
  if($targetarmor>0 && $attackerarmor>0)
  {
    $rating_change=round($targetinfo[rating]*.1);
    $armor_lost = $playerinfo[armour_pts] - $attackerarmor;
    $fighters_lost = $playerinfo[ship_fighters] - $attackerfighters;
    $energy=$playerinfo[ship_energy];
    $target_rating_change=round($targetinfo[rating]/2);
    $target_armor_lost = $targetinfo[armour_pts] - $targetarmor;
    $target_fighters_lost = $targetinfo[ship_fighters] - $targetfighters;
    $target_energy=$targetinfo[ship_energy];
    playerlog($playerinfo[ship_id],"Attack failed, $targetinfo[character_name] survived."); 
    playerlog($targetinfo[ship_id],"The Furangee $playerinfo[character_name] attacked you.  You lost $target_armor_lost points of armor and $target_fighters_lost fighters.<BR><BR>");
    mysql_query ("UPDATE ships SET ship_energy=$energy,ship_fighters=ship_fighters-$fighters_lost, torps=torps-$attackertorps,armour_pts=armour_pts-$armor_lost, rating=rating-$rating_change WHERE ship_id=$playerinfo[ship_id]");
    mysql_query ("UPDATE ships SET ship_energy=$target_energy,ship_fighters=ship_fighters-$target_fighters_lost, armour_pts=armour_pts-$target_armor_lost, torps=torps-$targettorpnum, rating=$target_rating_change WHERE ship_id=$targetinfo[ship_id]");
  }

  // *********************************
  // *** ATTACKER SHIP DESTROYED   ***
  // *********************************
  if(!$attackerarmor>0)
  {
    playerlog($playerinfo[ship_id],"$targetinfo[character_name] destroyed your ship!"); 
    db_kill_player($playerinfo['ship_id']);
    if($targetarmor>0)
    {
      // ****** TARGET STILL ALIVE TO SALVAGE ATTACKER ******
      $rating_change=round($playerinfo[rating]*$rating_combat_factor);
      $free_ore = round($playerinfo[ship_ore]/2);
      $free_organics = round($playerinfo[ship_organics]/2);
      $free_goods = round($playerinfo[ship_goods]/2);
      $free_holds = NUM_HOLDS($targetinfo[hull]) - $targetinfo[ship_ore] - $targetinfo[ship_organics] - $targetinfo[ship_goods] - $targetinfo[ship_colonists];
      if($free_holds > $free_goods) 
      {                                                        //****** FIGURE OUT WHAT TARGET CAN CARRY ******
        $salv_goods=$free_goods;
        $free_holds=$free_holds-$free_goods;
      } elseif($free_holds > 0)
      {
        $salv_goods=$free_holds;
        $free_holds=0;
      } else
      {
        $salv_goods=0;
      }
      if($free_holds > $free_ore)
      {
        $salv_ore=$free_ore;
        $free_holds=$free_holds-$free_ore;
      } elseif($free_holds > 0)
      {
        $salv_ore=$free_holds;
        $free_holds=0;
      } else
      {
        $salv_ore=0;
      }
      if($free_holds > $free_organics)
      {
        $salv_organics=$free_organics;
        $free_holds=$free_holds-$free_organics;
      } elseif($free_holds > 0)
      {
        $salv_organics=$free_holds;
        $free_holds=0;
      } else
      {
        $salv_organics=0;
      }
      $ship_value=$upgrade_cost*(round(pow($upgrade_factor, $playerinfo[hull]))+round(pow($upgrade_factor, $playerinfo[engines]))+round(pow($upgrade_factor, $playerinfo[power]))+round(pow($upgrade_factor, $playerinfo[computer]))+round(pow($upgrade_factor, $playerinfo[sensors]))+round(pow($upgrade_factor, $playerinfo[beams]))+round(pow($upgrade_factor, $playerinfo[torp_launchers]))+round(pow($upgrade_factor, $playerinfo[shields]))+round(pow($upgrade_factor, $playerinfo[armor]))+round(pow($upgrade_factor, $playerinfo[cloak])));
      $ship_salvage_rate=rand(10,20);
      $ship_salvage=$ship_value*$ship_salvage_rate/100;
      playerlog($targetinfo[ship_id],"You were attacked by Furangee $playerinfo[character_name] and you destroyed thier ship.<BR>");
      playerlog($targetinfo[ship_id],"You salvaged $salv_ore units of ore, $salv_organics units of organics, $salv_goods units of goods, and salvaged $ship_salvage_rate% of the ship for $ship_salvage credits.<BR>");
      mysql_query ("UPDATE ships SET ship_ore=ship_ore+$salv_ore, ship_organics=ship_organics+$salv_organics, ship_goods=ship_goods+$salv_goods, credits=credits+$ship_salvage WHERE ship_id=$playerinfo[ship_id]");
      $armor_lost = $targetinfo[armour_pts] - $targetarmor;
      $fighters_lost = $targetinfo[ship_fighters] - $targetfighters;
      $energy=$targetinfo[ship_energy];
      mysql_query ("UPDATE ships SET ship_energy=$energy,ship_fighters=ship_fighters-$fighters_lost, torps=torps-$targettorpnum,armour_pts=armour_pts-$armor_lost, rating=rating-$rating_change WHERE ship_id=$targetinfo[ship_id]");
    }
  }

  // *********************************
  // *** END OF FURANGEETOSHIP SUB ***
  // *********************************
  mysql_query("UNLOCK TABLES");
}

function furangeemove()
{
  // *********************************
  // *** SETUP GENERAL VARIABLES  ****
  // *********************************
  global $playerinfo;
  global $sector_max;
  global $targetlink;

  // *********************************
  // ***** OBTAIN A TARGET LINK ******
  // *********************************
  if ($targetlink==$playerinfo[sector]) $targetlink=0;
  $linkres = mysql_query ("SELECT * FROM links WHERE link_start='$playerinfo[sector]'");
  if ($linkres>0)
  {
    while ($row = mysql_fetch_array($linkres))
    {
      // *** OBTAIN SECTOR INFORMATION ***
      $sectres = mysql_query ("SELECT sector_id,zone_id FROM universe WHERE sector_id='$row[link_dest]'");
      $sectrow = mysql_fetch_array($sectres);
      $zoneres = mysql_query ("SELECT zone_id,allow_attack FROM zones WHERE zone_id=$sectrow[1]");
      $zonerow = mysql_fetch_array($zoneres);
      if ($zonerow[1]=="Y")                        //*** DEST LINK MUST ALLOW ATTACKING ***
      {
        $setlink=rand(0,2);                        //*** 33% CHANCE OF REPLACING DEST LINK WITH THIS ONE ***
        if ($setlink==0 || !$targetlink>0)          //*** UNLESS THERE IS NO DEST LINK, CHHOSE THIS ONE ***
        {
          $targetlink=$row[link_dest];
        }
      }
    }
  }

  // *********************************
  // ***** IF NO ACCEPTABLE LINK *****
  // *********************************
  // **** TIME TO USE A WORM HOLE ****
  // *********************************
  if (!$targetlink>0)
  {
    // *** GENERATE A RANDOM SECTOR NUMBER ***
    $wormto=rand(1,($sector_max-15));
    $limitloop=1;                        // *** LIMIT THE NUMBER OF LOOPS ***
    while (!$targetlink>0 && $limitloop<15)
    {
      // *** OBTAIN SECTOR INFORMATION ***
      $sectres = mysql_query ("SELECT sector_id,zone_id FROM universe WHERE sector_id='$wormto'");
      $sectrow = mysql_fetch_array($sectres);
      $zoneres = mysql_query ("SELECT zone_id,allow_attack FROM zones WHERE zone_id=$sectrow[1]");
      $zonerow = mysql_fetch_array($zoneres);
      if ($zonerow[1]=="Y")
      {
        $targetlink=$wormto;
        playerlog($playerinfo[ship_id],"Furangee used a wormhole to warp to a zone where attacks are allowed."); 
      }
      $wormto++;
      $wormto++;
      $limitloop++;
    }
  } 

  // *********************************
  // *** CHECK FOR SECTOR DEFENCE ****
  // *********************************
  if ($targetlink>0)
  {
    $resultf = mysql_query ("SELECT * FROM sector_defence WHERE sector_id='$targetlink' and defence_type ='F' ORDER BY quantity DESC");
    $i = 0;
    $total_sector_fighters = 0;
    if($resultf > 0)
    {
      while($row = mysql_fetch_array($resultf))
      {
        $defences[$i] = $row;
        $total_sector_fighters += $defences[$i]['quantity'];
        $i++;
      }
    }
    $resultm = mysql_query ("SELECT * FROM sector_defence WHERE sector_id='$targetlink' and defence_type ='M'");
    $i = 0;
    $total_sector_mines = 0;
    if($resultm > 0)
    {
      while($row = mysql_fetch_array($resultm))
      {
        $defences[$i] = $row;
        $total_sector_mines += $defences[$i]['quantity'];
        $i++;
      }
    }
    if ($total_sector_fighters>0 || $total_sector_mines>0 || ($total_sector_fighters>0 && $total_sector_mines>0))
    //*** DEST LINK HAS DEFENCES ***
    {
      playerlog($playerinfo[ship_id],"Move failed, the sector is defended by $total_sector_fighters fighters and $total_sector_mines mines."); 
      return;
    }
  }


  // *********************************
  // **** DO MOVE TO TARGET LINK *****
  // *********************************
  if ($targetlink>0)
  {
    $stamp = date("Y-m-d H-i-s");
    $query="UPDATE ships SET last_login='$stamp', turns_used=turns_used+1, sector=$targetlink where ship_id=$playerinfo[ship_id]";
    $move_result = mysql_query ("$query");
    if (!$move_result)
    {
      $error = mysql_error($move_result);
      playerlog($playerinfo[ship_id],"Move failed with error: $error "); 
    } else
    {
      // playerlog($playerinfo[ship_id],"Furangee moved to $targetlink without incident."); 
    }
  } else
  {                                            //*** WE HAVE NO TARGET LINK FOR SOME REASON ***
    playerlog($playerinfo[ship_id],"Move failed due to lack of target link.");
  }
}

function furangeeregen()
{
  // *******************************
  // *** SETUP GENERAL VARIABLES ***
  // *******************************
  global $playerinfo;

  // *******************************
  // *** LETS REGENERATE ENERGY ****
  // *******************************
  $maxenergy = NUM_ENERGY($playerinfo[power]);
  if ($playerinfo[ship_energy] <= ($maxenergy - 50))  // *** STOP REGEN WHEN WITHIN 50 OF MAX ***
  {                                                   // *** REGEN HALF OF REMAINING ENERGY ***
    $playerinfo[ship_energy] = $playerinfo[ship_energy] + round(($maxenergy - $playerinfo[ship_energy])/2);
    $gene = "regenerated Energy to $playerinfo[ship_energy] units,";
  }

  // *******************************
  // *** LETS REGENERATE ARMOUR ****
  // *******************************
  $maxarmour = NUM_ARMOUR($playerinfo[armour]);
  if ($playerinfo[armour_pts] <= ($maxarmour - 50))  // *** STOP REGEN WHEN WITHIN 50 OF MAX ***
  {                                                  // *** REGEN HALF OF REMAINING ARMOUR ***
    $playerinfo[armour_pts] = $playerinfo[armour_pts] + round(($maxarmour - $playerinfo[armour_pts])/2);
    $gena = "regenerated Armour to $playerinfo[armour_pts] points,";
  }

  // *******************************
  // *** LETS BUY FIGHTERS/TORPS ***
  // *******************************

  // *******************************
  // *** FURANGEE PAY 6/FIGHTER ****
  // *******************************
  $available_fighters = NUM_FIGHTERS($playerinfo[computer]) - $playerinfo[ship_fighters];
  if (($playerinfo[credits]>5) && ($available_fighters>0))
  {
    if (round($playerinfo[credits]/6)>$available_fighters)
    {
      $purchase = ($available_fighters*6);
      $playerinfo[credits] = $playerinfo[credits] - $purchase;
      $playerinfo[ship_fighters] = $playerinfo[ship_fighters] + $available_fighters;
      $genf = "purchased $available_fighters fighters for $purchase credits,";
    }
    if (round($playerinfo[credits]/6)<=$available_fighters)
    {
      $purchase = (round($playerinfo[credits]/6));
      $playerinfo[ship_fighters] = $playerinfo[ship_fighters] + $purchase;
      $genf = "purchased $purchase fighters for $playerinfo[credits] credits,";
      $playerinfo[credits] = 0;
    }
  } 

  // *******************************
  // *** FURANGEE PAY 3/TORPEDO ****
  // *******************************
  $available_torpedoes = NUM_TORPEDOES($playerinfo[torp_launchers]) - $playerinfo[torps];
  if (($playerinfo[credits]>2) && ($available_torpedoes>0))
  {
    if (round($playerinfo[credits]/3)>$available_torpedoes)
    {
      $purchase = ($available_torpedoes*3);
      $playerinfo[credits] = $playerinfo[credits] - $purchase;
      $playerinfo[torps] = $playerinfo[torps] + $available_torpedoes;
      $gent = "purchased $available_torpedoes torpedoes for $purchase credits,";
    }
    if (round($playerinfo[credits]/3)<=$available_torpedoes)
    {
      $purchase = (round($playerinfo[credits]/3));
      $playerinfo[torps] = $playerinfo[torps] + $purchase;
      $gent = "purchased $purchase torpedoes for $playerinfo[credits] credits,";
      $playerinfo[credits] = 0;
    }
  } 

  // *********************************
  // *** UPDATE FURANGEE RECORD ******
  // *********************************
  mysql_query ("UPDATE ships SET ship_energy=$playerinfo[ship_energy], armour_pts=$playerinfo[armour_pts], ship_fighters=$playerinfo[ship_fighters], torps=$playerinfo[torps], credits=$playerinfo[credits] WHERE ship_id=$playerinfo[ship_id]");
  if (!$gene=='' || !$gena=='' || !$genf=='' || !$gent=='')
  {
    playerlog($playerinfo[ship_id],"Furangee $gene $gena $genf $gent and has been updated."); 
  }

}

function furangeetrade()
{
  // *********************************
  // *** SETUP GENERAL VARIABLES  ****
  // *********************************
  global $playerinfo;
  global $inventory_factor;
  global $ore_price;
  global $ore_delta;
  global $ore_limit;
  global $goods_price;
  global $goods_delta;
  global $goods_limit;
  global $organics_price;
  global $organics_delta;
  global $organics_limit;

  // *********************************
  // *** OBTAIN SECTOR INFORMATION ***
  // *********************************
  $sectres = mysql_query ("SELECT * FROM universe WHERE sector_id='$playerinfo[sector]'");
  $sectorinfo = mysql_fetch_array($sectres);

  // *********************************
  // **** OBTAIN ZONE INFORMATION ****
  // *********************************
  $zoneres = mysql_query ("SELECT zone_id,allow_attack,allow_trade FROM zones WHERE zone_id='$sectorinfo[zone_id]'");
  $zonerow = mysql_fetch_array($zoneres);

  // Debug info
  // playerlog($playerinfo[ship_id],"PORT $sectorinfo[port_type] ALLOW_TRADE $zonerow[2] ORE $playerinfo[ship_ore] ORGAN $playerinfo[ship_organics] GOODS $playerinfo[ship_goods] CREDITS $playerinfo[credits] "); 

  // *********************************
  // ** MAKE SURE WE CAN TRADE HERE **
  // *********************************
  if ($zonerow[2]=="N") return;

  // *********************************
  // ** CHECK FOR A PORT WE CAN USE **
  // *********************************
  if($sectorinfo[port_type] == "none") return;
  // *** FURANGEE DO NOT TRADE AT ENERGY PORTS SINCE THEY REGEN ENERGY ***
  if($sectorinfo[port_type] == "energy") return;

  // *********************************
  // ** CHECK FOR NEG CREDIT/CARGO ***
  // *********************************
  if($playerinfo[ship_ore]<0) $playerinfo[ship_ore]=$shipore=0;
  if($playerinfo[ship_organics]<0) $playerinfo[ship_organics]=$shiporganics=0;
  if($playerinfo[ship_goods]<0) $playerinfo[ship_goods]=$shipgoods=0;
  if($playerinfo[credits]<0) $playerinfo[credits]=$shipcredits=0;

  // *********************************
  // ** CHECK FURANGEE CREDIT/CARGO **
  // *********************************
  if($playerinfo[ship_ore]>0) $shipore=$playerinfo[ship_ore];
  if($playerinfo[ship_organics]>0) $shiporganics=$playerinfo[ship_organics];
  if($playerinfo[ship_goods]>0) $shipgoods=$playerinfo[ship_goods];
  if($playerinfo[credits]>0) $shipcredits=$playerinfo[credits];
  // *** MAKE SURE WE HAVE CARGO OR CREDITS ***
  if(!$playerinfo[credits]>0 && !$playerinfo[ship_ore]>0 && !$playerinfo[ship_goods]>0 && !$playerinfo[ship_organics]>0) return;

  // *********************************
  // ** MAKE SURE CARGOS COMPATABLE **
  // *********************************
  if($sectorinfo[port_type]=="ore" && $shipore>0) return;
  if($sectorinfo[port_type]=="organics" && $shiporganics>0) return;
  if($sectorinfo[port_type]=="goods" && $shipgoods>0) return;

  // *********************************
  // ***** LETS TRADE SOME CARGO *****
  // *********************************
  if($sectorinfo[port_type]=="ore")
  // *********************
  // ***** PORT ORE ******
  // *********************
  {
    // ************************
    // **** SET THE PRICES ****
    // ************************
    $ore_price = $ore_price - $ore_delta * $sectorinfo[port_ore] / $ore_limit * $inventory_factor;
    $organics_price = $organics_price + $organics_delta * $sectorinfo[port_organics] / $organics_limit * $inventory_factor;
    $goods_price = $goods_price + $goods_delta * $sectorinfo[port_goods] / $goods_limit * $inventory_factor;
    // ************************
    // ** SET CARGO BUY/SELL **
    // ************************
    $amount_organics = $playerinfo[ship_organics];
    $amount_goods = $playerinfo[ship_goods];
    // *** SINCE WE SELL ALL OTHER HOLDS WE SET AMOUNT TO BE OUR TOTAL HOLD LIMIT *** 
    $amount_ore = NUM_HOLDS($playerinfo[hull]);
    // *** WE ADJUST THIS TO MAKE SURE IT DOES NOT EXCEED WHAT THE PORT HAS TO SELL ***
    $amount_ore = min($amount_ore, $sectorinfo[port_ore]);
    // *** WE ADJUST THIS TO MAKE SURE IT DOES NOT EXCEES WHAT WE CAN AFFORD TO BUY ***
    $amount_ore = min($amount_ore, floor(($playerinfo[credits] + $amount_organics * $organics_price + $amount_goods * $goods_price) / $ore_price));
    // ************************
    // **** BUY/SELL CARGO ****
    // ************************
    $total_cost = round(($amount_ore * $ore_price) - ($amount_organics * $organics_price + $amount_goods * $goods_price));
    $newcredits = max(0,$playerinfo[credits]-$total_cost);
    $newore = $playerinfo[ship_ore]+$amount_ore;
    $neworganics = max(0,$playerinfo[ship_organics]-$amount_organics);
    $newgoods = max(0,$playerinfo[ship_goods]-$amount_goods);
    $trade_result = mysql_query("UPDATE ships SET rating=rating+1, credits=$newcredits, ship_ore=$newore, ship_organics=$neworganics, ship_goods=$newgoods where ship_id=$playerinfo[ship_id]");
    $trade_result2 = mysql_query("UPDATE universe SET port_ore=port_ore-$amount_ore, port_organics=port_organics+$amount_organics, port_goods=port_goods+$amount_goods where sector_id=$sectorinfo[sector_id]");
    playerlog($playerinfo[ship_id],"Furangee Trade Results: Sold $amount_organics Organics Sold $amount_goods Goods Bought $amount_ore Ore Cost $total_cost"); 
  }
  if($sectorinfo[port_type]=="organics")
  // *********************
  // *** PORT ORGANICS ***
  // *********************
  {
    // ************************
    // **** SET THE PRICES ****
    // ************************
    $organics_price = $organics_price - $organics_delta * $sectorinfo[port_organics] / $organics_limit * $inventory_factor;
    $ore_price = $ore_price + $ore_delta * $sectorinfo[port_ore] / $ore_limit * $inventory_factor;
    $goods_price = $goods_price + $goods_delta * $sectorinfo[port_goods] / $goods_limit * $inventory_factor;
    // ************************
    // ** SET CARGO BUY/SELL **
    // ************************
    $amount_ore = $playerinfo[ship_ore];
    $amount_goods = $playerinfo[ship_goods];
    // *** SINCE WE SELL ALL OTHER HOLDS WE SET AMOUNT TO BE OUR TOTAL HOLD LIMIT *** 
    $amount_organics = NUM_HOLDS($playerinfo[hull]);
    // *** WE ADJUST THIS TO MAKE SURE IT DOES NOT EXCEED WHAT THE PORT HAS TO SELL ***
    $amount_organics = min($amount_organics, $sectorinfo[port_organics]);
    // *** WE ADJUST THIS TO MAKE SURE IT DOES NOT EXCEES WHAT WE CAN AFFORD TO BUY ***
    $amount_organics = min($amount_organics, floor(($playerinfo[credits] + $amount_ore * $ore_price + $amount_goods * $goods_price) / $organics_price));
    // ************************
    // **** BUY/SELL CARGO ****
    // ************************
    $total_cost = round(($amount_organics * $organics_price) - ($amount_ore * $ore_price + $amount_goods * $goods_price));
    $newcredits = max(0,$playerinfo[credits]-$total_cost);
    $newore = max(0,$playerinfo[ship_ore]-$amount_ore);
    $neworganics = $playerinfo[ship_organics]+$amount_organics;
    $newgoods = max(0,$playerinfo[ship_goods]-$amount_goods);
    $trade_result = mysql_query("UPDATE ships SET rating=rating+1, credits=$newcredits, ship_ore=$newore, ship_organics=$neworganics, ship_goods=$newgoods where ship_id=$playerinfo[ship_id]");
    $trade_result2 = mysql_query("UPDATE universe SET port_ore=port_ore+$amount_ore, port_organics=port_organics-$amount_organics, port_goods=port_goods+$amount_goods where sector_id=$sectorinfo[sector_id]");
    playerlog($playerinfo[ship_id],"Furangee Trade Results: Sold $amount_goods Goods Sold $amount_ore Ore Bought $amount_organics Organics Cost $total_cost"); 
  }
  if($sectorinfo[port_type]=="goods")
  // *********************
  // **** PORT GOODS *****
  // *********************
  {
    // ************************
    // **** SET THE PRICES ****
    // ************************
    $goods_price = $goods_price - $goods_delta * $sectorinfo[port_goods] / $goods_limit * $inventory_factor;
    $ore_price = $ore_price + $ore_delta * $sectorinfo[port_ore] / $ore_limit * $inventory_factor;
    $organics_price = $organics_price + $organics_delta * $sectorinfo[port_organics] / $organics_limit * $inventory_factor;
    // ************************
    // ** SET CARGO BUY/SELL **
    // ************************
    $amount_ore = $playerinfo[ship_ore];
    $amount_organics = $playerinfo[ship_organics];
    // *** SINCE WE SELL ALL OTHER HOLDS WE SET AMOUNT TO BE OUR TOTAL HOLD LIMIT *** 
    $amount_goods = NUM_HOLDS($playerinfo[hull]);
    // *** WE ADJUST THIS TO MAKE SURE IT DOES NOT EXCEED WHAT THE PORT HAS TO SELL ***
    $amount_goods = min($amount_goods, $sectorinfo[port_goods]);
    // *** WE ADJUST THIS TO MAKE SURE IT DOES NOT EXCEES WHAT WE CAN AFFORD TO BUY ***
    $amount_goods = min($amount_goods, floor(($playerinfo[credits] + $amount_ore * $ore_price + $amount_organics * $organics_price) / $goods_price));
    // ************************
    // **** BUY/SELL CARGO ****
    // ************************
    $total_cost = round(($amount_goods * $goods_price) - ($amount_organics * $organics_price + $amount_ore * $ore_price));
    $newcredits = max(0,$playerinfo[credits]-$total_cost);
    $newore = max(0,$playerinfo[ship_ore]-$amount_ore);
    $neworganics = max(0,$playerinfo[ship_organics]-$amount_organics);
    $newgoods = $playerinfo[ship_goods]+$amount_goods;
    $trade_result = mysql_query("UPDATE ships SET rating=rating+1, credits=$newcredits, ship_ore=$newore, ship_organics=$neworganics, ship_goods=$newgoods where ship_id=$playerinfo[ship_id]");
    $trade_result2 = mysql_query("UPDATE universe SET port_ore=port_ore+$amount_ore, port_organics=port_organics+$amount_organics, port_goods=port_goods-$amount_goods where sector_id=$sectorinfo[sector_id]");
    playerlog($playerinfo[ship_id],"Furangee Trade Results: Sold $amount_ore Ore Sold $amount_organics Organics Bought $amount_goods Goods Cost $total_cost"); 
  }

}

function furangeehunter()
{
  // *********************************
  // *** SETUP GENERAL VARIABLES  ****
  // *********************************
  global $playerinfo;

  $rescount = mysql_query("SELECT COUNT(*) AS num_players FROM ships WHERE ship_destroyed='N' and planet_id=0 and email NOT LIKE '%@furangee'");
  $rowcount = mysql_fetch_array($rescount);
  $topnum = min(10,$rowcount[num_players]);

  // *** IF WE HAVE KILLED ALL THE PLAYERS IN THE GAME THEN THERE IS LITTLE POINT IN PROCEEDING ***
  if ($topnum<1) return;

  $res = mysql_query("SELECT * FROM ships WHERE ship_destroyed='N' and planet_id=0 and email NOT LIKE '%@furangee' ORDER BY score DESC LIMIT $topnum");

  // *** LETS CHOOSE A TARGET FROM THE TOP PLAYER LIST ***
  $i=1;
  $targetnum=rand(1,$topnum);
  while ($row = mysql_fetch_array($res))
  {
    if ($i==$targetnum)
    { 
    $targetinfo=$row;
    }
    $i++;
  }
  // Debug
  // echo "Target is $targetinfo[character_name] <BR>";  

  // *********************************
  // *** WORM HOLE TO TARGET SECTOR **
  // *********************************
  $sectres = mysql_query ("SELECT sector_id,zone_id FROM universe WHERE sector_id='$targetinfo[sector]'");
  $sectrow = mysql_fetch_array($sectres);
  $zoneres = mysql_query ("SELECT zone_id,allow_attack FROM zones WHERE zone_id=$sectrow[1]");
  $zonerow = mysql_fetch_array($zoneres);
  // *** ONLY WORM HOLM TO TARGET IF WE CAN ATTACK IN TARGET SECTOR ***
  if ($zonerow[1]=="Y")
  {
    $stamp = date("Y-m-d H-i-s");
    $query="UPDATE ships SET last_login='$stamp', turns_used=turns_used+1, sector=$targetinfo[sector] where ship_id=$playerinfo[ship_id]";
    $move_result = mysql_query ("$query");
    playerlog($playerinfo[ship_id],"Furangee used a wormhole to warp to sector $targetinfo[sector] where he is hunting player $targetinfo[character_name]."); 
    if (!$move_result)
    {
      $error = mysql_error($move_result);
      playerlog($playerinfo[ship_id],"Move failed with error: $error "); 
      return;
    }
    // *** TIME TO ATTACK THE TARGET ***
    playerlog($playerinfo[ship_id],"Furangee launching an attack on $targetinfo[character_name]."); 
    furangeetoship($targetinfo[ship_id]);
  } else
  {
    playerlog($playerinfo[ship_id],"Furangee hunt failed, target $targetinfo[character_name] was in a no attack zone.");
  }
}

?>