<?php
//$Id$
// This is required by Setup Info, So DO NOT REMOVE
// create_universe_port_fix,0.2.0,25-02-2004,TheMightyDude

include("config.php");
loadlanguage($lang);


/*
##############################################################################
# Create Universe Script                                                     #
#                                                                            #
# ChangeLog                                                                  #
#  Sep 2, 04 - TheMightyDude - Completely Rewritten from scratch             #                                                                #
#              It should now be more Load balanced for PHP and MySQL         #
#  Nov 2, 01 - Wandrer - Rewritten mostly from scratch                       #
##############################################################################
*/

/*
##############################################################################
# Define Functions for this script                                           #
##############################################################################
*/

## HTML Table Functions ##

if (!function_exists('PrintFlush'))
{
    function PrintFlush($Text="")
    {
        print "$Text";
        flush();
    }
}


if (!function_exists('TRUEFALSE'))
{
    function TRUEFALSE($truefalse,$Stat,$True,$False)
    {
        return(($truefalse == $Stat) ? $True : $False);
    }
}

if (!function_exists('Table_Header'))
{
    function Table_Header($title="")
    {
        PrintFlush( "<div align=\"center\">\n");
        PrintFlush( "  <center>\n");
        PrintFlush( "  <table border=\"0\" cellpadding=\"1\" width=\"700\" cellspacing=\"1\" bgcolor=\"#000000\">\n");
        PrintFlush( "    <tr>\n");
        PrintFlush( "      <th width=\"700\" colspan=\"2\" height=\"20\" bgcolor=\"#9999CC\" align=\"left\"><font face=\"Verdana\" color=\"#000000\" size=\"2\">$title</font></th>\n");
        PrintFlush( "    </tr>\n");
    }
}

if (!function_exists('Table_Row'))
{
    function Table_Row($data,$failed="Failed",$passed="Passed")
    {
        global $db;
        $err = TRUEFALSE(0,$db->ErrorNo(),"No errors found",$db->ErrorNo() . ": " . $db->ErrorMsg());;
        PrintFlush( "    <tr title=\"$err\">\n");
        PrintFlush( "      <td width=\"600\" bgcolor=\"#CCCCFF\"><font face=\"Verdana\" size=\"1\" color=\"#000000\">$data</font></td>\n");
        if($db->ErrorNo()!=0)
            {PrintFlush( "      <td width=\"100\" align=\"center\" bgcolor=\"#C0C0C0\"><font face=\"Verdana\" size=\"1\" color=\"red\">$failed ({$db->ErrorMsg()})</font></td>\n");}
        else
            {PrintFlush( "      <td width=\"100\" align=\"center\" bgcolor=\"#C0C0C0\"><font face=\"Verdana\" size=\"1\" color=\"Blue\">$passed</font></td>\n");}
        echo "    </tr>\n";
    }
}


if (!function_exists('Table_2Col'))
{
    function Table_2Col($name,$value)
    {
        PrintFlush("    <tr>\n");
        PrintFlush( "      <td width=\"600\" bgcolor=\"#CCCCFF\"><font face=\"Verdana\" size=\"1\" color=\"#000000\">$name</font></td>\n");
        PrintFlush( "      <td width=\"100\" bgcolor=\"#C0C0C0\"><font face=\"Verdana\" size=\"1\" color=\"#000000\">$value</font></td>\n");
        PrintFlush( "    </tr>\n");
    }
}

if (!function_exists('Table_1Col'))
{
    function Table_1Col($data)
    {
        PrintFlush( "    <tr>\n");
        PrintFlush( "      <td width=\"700\" colspan=\"2\" bgcolor=\"#C0C0C0\" align=\"left\"><font face=\"Verdana\" color=\"#000000\" size=\"1\">$data</font></td>\n");
        PrintFlush( "    </tr>\n");
    }
}

if (!function_exists('Table_Spacer'))
{
    function Table_Spacer()
    {
        PrintFlush( "    <tr>\n");
        PrintFlush( "      <td width=\"100%\" colspan=\"2\" bgcolor=\"#9999CC\" height=\"1\"></td>\n");
        PrintFlush( "    </tr>\n");
    }
}

if (!function_exists('Table_Footer'))
{
    function Table_Footer($footer='')
    {
        if(!empty($footer))
        {
            PrintFlush( "    <tr>\n");
            PrintFlush( "      <td width=\"100%\" colspan=\"2\" bgcolor=\"#9999CC\" align=\"left\"><font face=\"Verdana\" color=\"#000000\" size=\"1\">$footer</font></td>\n");
            PrintFlush( "    </tr>\n");
        }
        PrintFlush( "  </table>\n");
        PrintFlush( "  </center>\n");
        PrintFlush( "</div><p>\n");
    }
}

## ---- ##

### Description: Create Benchmark Class

class c_Timer
{
    var $t_start = 0;
    var $t_stop = 0;
    var $t_elapsed = 0;

    function start()
    {
        $this->t_start = microtime();
    }

    function stop()
    {
        $this->t_stop = microtime();
    }

    function elapsed()
    {
        $start_u = substr($this->t_start,0,10);
        $start_s = substr($this->t_start,11,10);
        $stop_u  = substr($this->t_stop,0,10);
        $stop_s  = substr($this->t_stop,11,10);
        $start_total = doubleval($start_u) + $start_s;
        $stop_total  = doubleval($stop_u) + $stop_s;
        $this->t_elapsed = $stop_total - $start_total;
        return $this->t_elapsed;
    }
}
/*
//function PrintFlush($Text="")
//{
//    print "$Text";
//    flush();
//}
 */
### End defining functions.

### Start Timer
$BenchmarkTimer = new c_Timer;
$BenchmarkTimer->start();

### Set timelimit and randomize timer.

set_time_limit(0);
srand(intval(microtime(true) * 1000000));

### Include config files and db scheme.

include("includes/schema.php");

### Update cookie.
updatecookie();

$title="Create Universe";
include("header.php");

### Connect to the database.

connectDB();

### Print Title on Page.

bigtitle();

### Manually set step var if info isn't correct.

if (!isset($_POST['swordfish'])) {
    $_POST['swordfish'] = null;
}

if (!isset($_POST['engage'])) {
    $engage = null;
} else {
    $engage = $_POST['engage'];
}

if($adminpass!= $_POST['swordfish'])
{
    $step="0";
}

if($engage == "" && $adminpass == $_POST['swordfish'] )
{
    $step="1";
}

if($engage == "1" && $adminpass == $_POST['swordfish'] )
{
    $step="2";
}

extract($_POST);

### Main switch statement.

switch ($step) {
   case "1":
      echo "<form action=create_universe.php method=post>";

    Table_Header("Create Universe [Base/Planet Setup]");
    Table_2Col("Percent Special","<input type=text name=special size=10 maxlength=10 value=1>");
    Table_2Col("Percent Ore","<input type=text name=ore size=10 maxlength=10 value=15>");
    Table_2Col("Percent Organics","<input type=text name=organics size=10 maxlength=10 value=10>");
    Table_2Col("Percent Goods","<input type=text name=goods size=10 maxlength=10 value=15>");
    Table_2Col("Percent Energy","<input type=text name=energy size=10 maxlength=10 value=10>");

    Table_1Col("Percent Empty: Equal to 100 - total of above.");

    Table_2Col("Initial Commodities to Sell [% of max]","<input type=text name=initscommod size=10 maxlength=10 value=100.00>");
    Table_2Col("Initial Commodities to Buy [% of max]","<input type=text name=initbcommod size=10 maxlength=10 value=100.00>");
    Table_Footer(" ");

    Table_Header("Create Universe [Sector/Link Setup] --- Stage 1");

    $fedsecs = intval($sector_max / 200);
    $loops = intval($sector_max / 500);

    Table_2Col("Number of sectors total (<b>overrides config.php</b>)","<input type=text name=sektors size=10 maxlength=10 value=$sector_max>");
    Table_2Col("Number of Federation sectors","<input type=text name=fedsecs size=10 maxlength=10 value=$fedsecs>");
    Table_2Col("Number of loops","<input type=text name=loops size=10 maxlength=10 value=$loops>");
    Table_2Col("Percent of sectors with unowned planets","<input type=text name=planets size=10 maxlength=10 value=10>");
    Table_Footer(" ");

    echo "<input type=hidden name=engage value=1>\n";
    echo "<input type=hidden name=step value=2>\n";
    echo "<input type=hidden name=swordfish value=$swordfish>\n";

    Table_Header("Submit Settings");
    Table_1Col("<p align='center'><input type=submit value=Submit><input type=reset value=Reset></p>");
    Table_Footer(" ");

    echo "</form>";
      break;
   case "2":

    Table_Header("Create Universe Confirmation [So you would like your $sector_max sector universe to have:] --- Stage2");

      $sector_max = round($sektors);
      if($fedsecs > $sector_max)
      {
    Table_1Col("<FONT COLOR=RED>The number of Federation sectors must be smaller than the size of the universe!</FONT>");
    Table_Footer(" ");
         break;
      }
      $spp = round($sector_max*$special/100);
      $oep = round($sector_max*$ore/100);
      $ogp = round($sector_max*$organics/100);
      $gop = round($sector_max*$goods/100);
      $enp = round($sector_max*$energy/100);
      $empty = $sector_max-$spp-$oep-$ogp-$gop-$enp;
      $nump = round ($sector_max*$planets/100);

      echo "<form action=create_universe.php method=post>\n";
      echo "<input type=hidden name=step value=3>\n";
      echo "<input type=hidden name=spp value=$spp>\n";
      echo "<input type=hidden name=oep value=$oep>\n";
      echo "<input type=hidden name=ogp value=$ogp>\n";
      echo "<input type=hidden name=gop value=$gop>\n";
      echo "<input type=hidden name=enp value=$enp>\n";
      echo "<input type=hidden name=initscommod value=$initscommod>\n";
      echo "<input type=hidden name=initbcommod value=$initbcommod>\n";
      echo "<input type=hidden name=nump value=$nump>\n";
      echo "<input type=hidden name=fedsecs value=$fedsecs>\n";
      echo "<input type=hidden name=loops value=$loops>\n";
      echo "<input type=hidden name=engage value=2>\n";
      echo "<input type=hidden name=swordfish value=$swordfish>\n";

    Table_2Col("Special ports",$spp);
    Table_2Col("Ore ports",$oep);
    Table_2Col("Organics ports",$ogp);
    Table_2Col("Goods ports",$gop);
    Table_2Col("Energy ports",$enp);
    Table_Spacer();
    Table_2Col("Initial commodities to sell",$initscommod."%");
    Table_2Col("Initial commodities to buy",$initbcommod."%");
    Table_Spacer();
    Table_2Col("Empty sectors",$empty);
    Table_2Col("Federation sectors",$fedsecs);
    Table_2Col("Loops",$loops);
    Table_2Col("Unowned planets",$nump);
    Table_Spacer();

    Table_1Col("<p align='center'><input type=submit value=Confirm></p>");
    Table_Spacer();

    Table_1Col("<FONT COLOR=RED>WARNING: ALL TABLES WILL BE DROPPED AND THE GAME WILL BE RESET WHEN YOU CLICK 'CONFIRM'!</FONT>");
    Table_Footer(" ");

      echo "</form>";

      break;
   case "3":
      create_schema();
      echo "<form action=create_universe.php method=post>";
      echo "<input type=hidden name=step value=4>";
      echo "<input type=hidden name=spp value=$spp>";
      echo "<input type=hidden name=oep value=$oep>";
      echo "<input type=hidden name=ogp value=$ogp>";
      echo "<input type=hidden name=gop value=$gop>";
      echo "<input type=hidden name=enp value=$enp>";
      echo "<input type=hidden name=initscommod value=$initscommod>";
      echo "<input type=hidden name=initbcommod value=$initbcommod>";
      echo "<input type=hidden name=nump value=$nump>";
      echo "<input type=hidden name=fedsecs value=$fedsecs>";
      echo "<input type=hidden name=loops value=$loops>";
      echo "<input type=hidden name=engage value=2>";
      echo "<input type=hidden name=swordfish value=$swordfish>";
      echo "<p align='center'><input type=submit value=Confirm></p>";
      echo "</form>";
      break;
   case "4":
       
    Table_Header("Setting up Sectors --- STAGE 4");
       $db->Execute("DELETE FROM $dbtables[universe]");
    Table_Row("Delete all sector","Failed","Deleted");
      $initsore = $ore_limit * $initscommod / 100.0;
      $initsorganics = $organics_limit * $initscommod / 100.0;
      $initsgoods = $goods_limit * $initscommod / 100.0;
      $initsenergy = $energy_limit * $initscommod / 100.0;
      $initbore = $ore_limit * $initbcommod / 100.0;
      $initborganics = $organics_limit * $initbcommod / 100.0;
      $initbgoods = $goods_limit * $initbcommod / 100.0;
      $initbenergy = $energy_limit * $initbcommod / 100.0;

      $insert = $db->Execute("INSERT INTO $dbtables[universe] (sector_id, sector_name, zone_id, port_type, port_organics, port_ore, port_goods, port_energy, beacon, angle1, angle2, distance) VALUES (null, 'Sol', '1', 'special', '0', '0', '0', '0', 'Sol: Hub of the Universe', '0', '0', '0')");
    Table_Row("Creating Sol sector","Failed","Created");
      $insert = $db->Execute("INSERT INTO $dbtables[universe] (sector_id, sector_name, zone_id, port_type, port_organics, port_ore, port_goods, port_energy, beacon, angle1, angle2, distance) VALUES (null, 'Alpha Centauri', '1', 'energy',  '0', '0', '0', '0', 'Alpha Centauri: Gateway to the Galaxy', '0', '0', '1')");
    Table_Row("Creating Alpha Centauri in sector 1","Failed","Created");

    Table_Spacer();

      $remaining = $sector_max-2;
      ### Cycle through remaining sectors

    # !!!!! DO NOT ALTER LOOPSIZE !!!!!
    # This should be balanced 50%/50% PHP/MySQL load :)

        $loopsize = 500;
        $loops = round($sector_max / $loopsize)+1;
        if($loops <= 0) $loops = 1;
        $finish = $loopsize;
        if ($finish>($sector_max)) $finish=($sector_max);
        $start=2;

        for($i=1; $i<=$loops; $i++)
        {
            $insert="INSERT INTO $dbtables[universe] (sector_id,zone_id,angle1,angle2,distance) VALUES ";
            for($j=$start; $j<$finish; $j++)
            {
                $distance=intval(rand(1,$universe_size));
                $angle1=rand(0,180);
                $angle2=rand(0,90);
                $insert.="(NULL,'1',$angle1,$angle2,$distance)";
                if($j<($finish-1)) $insert .= ", "; else $insert .= ";";
            }
            ### Now lets post the information to the mysql database.
//          $db->Execute("$insert");
            if ($start<$sector_max && $finish<=$sector_max) $db->Execute($insert);

        Table_Row("Inserting loop $i of $loops Sector Block [".($start)." - ".($finish-1)."] into the Universe.","Failed","Inserted");

            $start = $finish;
            $finish += $loopsize;
            if ($finish>($sector_max)) $finish=($sector_max);
        };

    Table_Spacer();

      $replace = $db->Execute("REPLACE INTO $dbtables[zones] (zone_id, zone_name, owner, corp_zone, allow_beacon, allow_attack, allow_planetattack, allow_warpedit, allow_planet, allow_trade, allow_defenses, max_hull) VALUES ('1', 'Unchartered space', 0, 'N', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', '0' )");
    Table_Row("Setting up Zone (Unchartered space)","Failed","Set");

      $replace = $db->Execute("REPLACE INTO $dbtables[zones](zone_id, zone_name, owner, corp_zone, allow_beacon, allow_attack, allow_planetattack, allow_warpedit, allow_planet, allow_trade, allow_defenses, max_hull) VALUES ('2', 'Federation space', 0, 'N', 'N', 'N', 'N', 'N', 'N',  'Y', 'N', '$fed_max_hull')");
    Table_Row("Setting up Zone (Federation space)","Failed","Set");

      $replace = $db->Execute("REPLACE INTO $dbtables[zones](zone_id, zone_name, owner, corp_zone, allow_beacon, allow_attack, allow_planetattack, allow_warpedit, allow_planet, allow_trade, allow_defenses, max_hull) VALUES ('3', 'Free-Trade space', 0, 'N', 'N', 'Y', 'N', 'N', 'N','Y', 'N', '0')");
    Table_Row("Setting up Zone (Free-Trade space)","Failed","Set");

      $replace = $db->Execute("REPLACE INTO $dbtables[zones](zone_id, zone_name, owner, corp_zone, allow_beacon, allow_attack, allow_planetattack, allow_warpedit, allow_planet, allow_trade, allow_defenses, max_hull) VALUES ('4', 'War Zone', 0, 'N', 'Y', 'Y', 'Y', 'Y', 'Y','N', 'Y', '0')");
    Table_Row("Setting up Zone (War Zone)","Failed","Set");

      $update = $db->Execute("UPDATE $dbtables[universe] SET zone_id='2' WHERE sector_id<$fedsecs");
    Table_Row("Setting up the $fedsecs Federation Sectors","Failed","Set");

      ### Finding random sectors where port=none and getting their sector ids in one sql query
      ### For Special Ports

# !!!!! DO NOT ALTER LOOPSIZE !!!!!
# This should be balanced 50%/50% PHP/MySQL load :)

        $loopsize = 500;
        $loops = round($spp / $loopsize);
        if($loops <= 0) $loops = 1;
        $finish = $loopsize;
        if ($finish>$spp) $finish=($spp);

    # Well since we hard coded a special port already, we start from 1.
        $start=1;

    Table_Spacer();

        $sql_query=$db->Execute("select sector_id from $dbtables[universe] WHERE port_type='none' order by rand() desc limit $spp");
        $update="UPDATE $dbtables[universe] SET zone_id='3',port_type='special' WHERE ";

        for($i=1; $i<=$loops; $i++)
        {
            $update="UPDATE $dbtables[universe] SET zone_id='3',port_type='special' WHERE ";
            for($j=$start; $j<$finish; $j++)
            {
                $result = $sql_query->fields;
                $update .= "(port_type='none' and sector_id=$result[sector_id])";
                if($j<($finish-1)) $update .= " or "; else $update .= ";";
                $sql_query->Movenext();
            }
            $db->Execute($update);

    Table_Row("Loop $i of $loops (Setting up Special Ports) Port [".($start+1)." - $finish]","Failed","Selected");

            $start=$finish;
            $finish += $loopsize;
            if ($finish>$spp) $finish=($spp);
        }

      ### Finding random sectors where port=none and getting their sector ids in one sql query
      ### For Ore Ports
      $initsore = $ore_limit * $initscommod / 100.0;
      $initsorganics = $organics_limit * $initscommod / 100.0;
      $initsgoods = $goods_limit * $initscommod / 100.0;
      $initsenergy = $energy_limit * $initscommod / 100.0;
      $initbore = $ore_limit * $initbcommod / 100.0;
      $initborganics = $organics_limit * $initbcommod / 100.0;
      $initbgoods = $goods_limit * $initbcommod / 100.0;
      $initbenergy = $energy_limit * $initbcommod / 100.0;

    # !!!!! DO NOT ALTER LOOPSIZE !!!!!
    # This should be balanced 50%/50% PHP/MySQL load :)

        $loopsize = 500;
        $loops = round($oep / $loopsize);
        if($loops <= 0) $loops = 1;
        $finish = $loopsize;
        if ($finish>$oep) $finish=($oep);
        $start=0;

    Table_Spacer();

        $sql_query=$db->Execute("select sector_id from $dbtables[universe] WHERE port_type='none' order by rand() desc limit $oep");
        $update="UPDATE $dbtables[universe] SET port_type='ore',port_ore=$initsore,port_organics=$initborganics,port_goods=$initbgoods,port_energy=$initbenergy WHERE ";

        for($i=1; $i<=$loops; $i++)
        {
            $update="UPDATE $dbtables[universe] SET port_type='ore',port_ore=$initsore,port_organics=$initborganics,port_goods=$initbgoods,port_energy=$initbenergy WHERE ";
            for($j=$start; $j<$finish; $j++)
            {
                $result = $sql_query->fields;
                $update .= "(port_type='none' and sector_id=$result[sector_id])";
                if($j<($finish-1)) $update .= " or "; else $update .= ";";
                $sql_query->Movenext();
            }
            $db->Execute($update);

    Table_Row("Loop $i of $loops (Setting up Ore Ports) Block [".($start+1)." - $finish]","Failed","Selected");

            $start=$finish;
            $finish += $loopsize;
            if ($finish>$oep) $finish=($oep);
        }

      ### Finding random sectors where port=none and getting their sector ids in one sql query
      ### For Organic Ports
      $initsore = $ore_limit * $initscommod / 100.0;
      $initsorganics = $organics_limit * $initscommod / 100.0;
      $initsgoods = $goods_limit * $initscommod / 100.0;
      $initsenergy = $energy_limit * $initscommod / 100.0;
      $initbore = $ore_limit * $initbcommod / 100.0;
      $initborganics = $organics_limit * $initbcommod / 100.0;
      $initbgoods = $goods_limit * $initbcommod / 100.0;
      $initbenergy = $energy_limit * $initbcommod / 100.0;

    # !!!!! DO NOT ALTER LOOPSIZE !!!!!
    # This should be balanced 50%/50% PHP/MySQL load :)

        $loopsize = 500;
        $loops = round($ogp / $loopsize);
        if($loops <= 0) $loops = 1;
        $finish = $loopsize;
        if ($finish>$ogp) $finish=($ogp);
        $start=0;

    Table_Spacer();

        $sql_query=$db->Execute("select sector_id from $dbtables[universe] WHERE port_type='none' order by rand() desc limit $ogp");
        $update="UPDATE $dbtables[universe] SET port_type='organics',port_ore=$initsore,port_organics=$initborganics,port_goods=$initbgoods,port_energy=$initbenergy WHERE ";

        for($i=1; $i<=$loops; $i++)
        {
            $update="UPDATE $dbtables[universe] SET port_type='organics',port_ore=$initbore,port_organics=$initsorganics,port_goods=$initbgoods,port_energy=$initbenergy WHERE ";
            for($j=$start; $j<$finish; $j++)
            {
                $result = $sql_query->fields;
                $update .= "(port_type='none' and sector_id=$result[sector_id])";
                if($j<($finish-1)) $update .= " or "; else $update .= ";";
                $sql_query->Movenext();
            }
            $db->Execute($update);

    Table_Row("Loop $i of $loops (Setting up Organics Ports) Block [".($start+1)." - $finish]","Failed","Selected");

            $start=$finish;
            $finish += $loopsize;
            if ($finish>$ogp) $finish=($ogp);
        }

      ### Finding random sectors where port=none and getting their sector ids in one sql query
      ### For Goods Ports
      $initsore = $ore_limit * $initscommod / 100.0;
      $initsorganics = $organics_limit * $initscommod / 100.0;
      $initsgoods = $goods_limit * $initscommod / 100.0;
      $initsenergy = $energy_limit * $initscommod / 100.0;
      $initbore = $ore_limit * $initbcommod / 100.0;
      $initborganics = $organics_limit * $initbcommod / 100.0;
      $initbgoods = $goods_limit * $initbcommod / 100.0;
      $initbenergy = $energy_limit * $initbcommod / 100.0;

    # !!!!! DO NOT ALTER LOOPSIZE !!!!!
    # This should be balanced 50%/50% PHP/MySQL load :)

        $loopsize = 500;
        $loops = round($gop / $loopsize);
        if($loops <= 0) $loops = 1;
        $finish = $loopsize;
        if ($finish>$gop) $finish=($gop);
        $start=0;

    Table_Spacer();

        $sql_query=$db->Execute("select sector_id from $dbtables[universe] WHERE port_type='none' order by rand() desc limit $gop");
        $update="UPDATE $dbtables[universe] SET port_type='goods',port_ore=$initbore,port_organics=$initborganics,port_goods=$initsgoods,port_energy=$initbenergy WHERE ";

        for($i=1; $i<=$loops; $i++)
        {
            $update="UPDATE $dbtables[universe] SET port_type='goods',port_ore=$initbore,port_organics=$initborganics,port_goods=$initsgoods,port_energy=$initbenergy WHERE ";
            for($j=$start; $j<$finish; $j++)
            {
                $result = $sql_query->fields;
                $update .= "(port_type='none' and sector_id=$result[sector_id])";
                if($j<($finish-1)) $update .= " or "; else $update .= ";";
                $sql_query->Movenext();
            }
            $db->Execute($update);

    Table_Row("Loop $i of $loops (Setting up Goods Ports) Block [".($start+1)." - $finish]","Failed","Selected");

            $start=$finish;
            $finish += $loopsize;
            if ($finish>$gop) $finish=($gop);
        }

      ### Finding random sectors where port=none and getting their sector ids in one sql query
      ### For Energy Ports
      $initsore = $ore_limit * $initscommod / 100.0;
      $initsorganics = $organics_limit * $initscommod / 100.0;
      $initsgoods = $goods_limit * $initscommod / 100.0;
      $initsenergy = $energy_limit * $initscommod / 100.0;
      $initbore = $ore_limit * $initbcommod / 100.0;
      $initborganics = $organics_limit * $initbcommod / 100.0;
      $initbgoods = $goods_limit * $initbcommod / 100.0;
      $initbenergy = $energy_limit * $initbcommod / 100.0;

    # !!!!! DO NOT ALTER LOOPSIZE !!!!!
    # This should be balanced 50%/50% PHP/MySQL load :)

        $loopsize = 500;
        $loops = round($enp / $loopsize);
        if($loops <= 0) $loops = 1;
        $finish = $loopsize;
        if ($finish>$enp) $finish=($enp);

    # Well since we hard coded an energy port already, we start from 1.
        $start=1;

    Table_Spacer();

        $sql_query=$db->Execute("select sector_id from $dbtables[universe] WHERE port_type='none' order by rand() desc limit $enp");
        $update="UPDATE $dbtables[universe] SET port_type='energy',port_ore=$initbore,port_organics=$initborganics,port_goods=$initsgoods,port_energy=$initbenergy WHERE ";

        for($i=1; $i<=$loops; $i++)
        {
            $update="UPDATE $dbtables[universe] SET port_type='energy',port_ore=$initbore,port_organics=$initborganics,port_goods=$initsgoods,port_energy=$initbenergy WHERE ";
            for($j=$start; $j<$finish; $j++)
            {
                $result = $sql_query->fields;
                $update .= "(port_type='none' and sector_id=$result[sector_id])";
                if($j<($finish-1)) $update .= " or "; else $update .= ";";
                $sql_query->Movenext();
            }
            $db->Execute($update);

    Table_Row("Loop $i of $loops (Setting up Energy Ports) Block [".($start+1)." - $finish]","Failed","Selected");

            $start=$finish;
            $finish += $loopsize;
            if ($finish>$enp) $finish=($enp);
        }

    Table_Spacer();
    Table_Footer("Completed successfully");

      echo "<form action=create_universe.php method=post>";
      echo "<input type=hidden name=step value=5>";
      echo "<input type=hidden name=spp value=$spp>";
      echo "<input type=hidden name=oep value=$oep>";
      echo "<input type=hidden name=ogp value=$ogp>";
      echo "<input type=hidden name=gop value=$gop>";
      echo "<input type=hidden name=enp value=$enp>";
      echo "<input type=hidden name=initscommod value=$initscommod>";
      echo "<input type=hidden name=initbcommod value=$initbcommod>";
      echo "<input type=hidden name=nump value=$nump>";
      echo "<input type=hidden name=fedsecs value=$fedsecs>";
      echo "<input type=hidden name=loops value=$loops>";
      echo "<input type=hidden name=engage value=2>";
      echo "<input type=hidden name=swordfish value=$swordfish>";
      echo "<p align='center'><input type=submit value=Confirm></p>";
      echo "</form>";
      break;
   case "5":

        $p_add=0;$p_skip=0;$i=0;

Table_Header("Setting up Universe Sectors --- Stage 5");

        do
        {
            $num = rand(2, ($sector_max-1));
            $select = $db->Execute("SELECT $dbtables[universe].sector_id FROM $dbtables[universe], $dbtables[zones] WHERE $dbtables[universe].sector_id=$num AND $dbtables[zones].zone_id=$dbtables[universe].zone_id AND $dbtables[zones].allow_planet='N'") or die("DB error");
            if($select->RecordCount() == 0)
            {
                $insert = $db->Execute("INSERT INTO $dbtables[planets] (colonists, owner, corp, prod_ore, prod_organics, prod_goods, prod_energy, prod_fighters, prod_torp, sector_id) VALUES (2,0,0,$default_prod_ore,$default_prod_organics,$default_prod_goods,$default_prod_energy, $default_prod_fighters, $default_prod_torp,$num)");
                $p_add++;
            }
        }
        while ($p_add < $nump);

Table_Row("Selecting $nump sectors to place unowned planets in.","Failed","Selected");

Table_Spacer();

## Adds Sector Size *2 amount of links to the links table ##

    # !!!!! DO NOT ALTER LOOPSIZE !!!!!
    # This should be balanced 50%/50% PHP/MySQL load :)

        $loopsize = 500;
        $loops = round($sector_max / $loopsize)+1;
        if($loops <= 0) $loops = 1;
        $finish = $loopsize;
        if ($finish>$sector_max) $finish=($sector_max);
        $start=0;

        for($i=1; $i<=$loops; $i++)
        {
            $update = "INSERT INTO $dbtables[links] (link_start,link_dest) VALUES ";
            for($j=$start; $j<$finish; $j++)
            {
                $k = $j + 1;
                $update .= "($j,$k), ($k,$j)";
                if($j<($finish-1)) $update .= ", "; else $update .= ";";
            }
            if ($start<$sector_max && $finish<=$sector_max) $db->Execute($update);

            Table_Row("Creating loop $i of $loops sectors (from sector ".($start)." to ".($finish-1).") - loop $i","Failed","Created");

            $start=$finish;
            $finish += $loopsize;
            if ($finish>$sector_max) $finish=$sector_max;
        }

//      PrintFlush("<BR>Sector Links created successfully.<BR>");

####################

Table_Spacer();

//      PrintFlush("<BR>Randomly One-way Linking $i Sectors (out of $sector_max sectors)<br>\n");

## Adds Sector Size amount of links to the links table ##

    # !!!!! DO NOT ALTER LOOPSIZE !!!!!
    # This should be balanced 50%/50% PHP/MySQL load :)

        $loopsize = 500;
        $loops = round($sector_max / $loopsize)+1;
        if($loops <= 0) $loops = 1;
        $finish = $loopsize;
        if ($finish>$sector_max) $finish=($sector_max);
        $start=0;

        for($i=1; $i<=$loops; $i++)
        {
            $insert="INSERT INTO $dbtables[links] (link_start,link_dest) VALUES ";
            for($j=$start; $j<$finish; $j++)
            {
                $link1=intval(rand(1,$sector_max-1));
                $link2=intval(rand(1,$sector_max-1));
                $insert.="($link1,$link2)";
                if($j<($finish-1)) $insert .= ", "; else $insert .= ";";
            }
#           PrintFlush("<font color='#FFFF00'>Creating loop $i of $loopsize Random One-way Links (from sector ".($start)." to ".($finish-1).") - loop $i</font><br>\n");

            if ($start<$sector_max && $finish<=$sector_max) $db->Execute($insert);

//          $db->Execute($insert);

Table_Row("Creating loop $i of $loops Random One-way Links (from sector ".($start)." to ".($finish-1).") - loop $i","Failed","Created");

            $start=$finish;
            $finish += $loopsize;
            if ($finish>$sector_max) $finish=($sector_max);
        }

//      PrintFlush("Completed successfully.<BR>\n");

######################

Table_Spacer();

//      PrintFlush("<BR>Randomly Two-way Linking Sectors<br>\n");

## Adds Sector Size*2 amount of links to the links table ##

    # !!!!! DO NOT ALTER LOOPSIZE !!!!!
    # This should be balanced 50%/50% PHP/MySQL load :)

        $loopsize = 500;
        $loops = round($sector_max / $loopsize)+1;
        if($loops <= 0) $loops = 1;
        $finish = $loopsize;
        if ($finish>$sector_max) $finish=($sector_max);
        $start=0;

        for($i=1; $i<=$loops; $i++)
        {
            $insert="INSERT INTO $dbtables[links] (link_start,link_dest) VALUES ";
            for($j=$start; $j<$finish; $j++)
            {
                $link1=intval(rand(1,$sector_max-1));
                $link2=intval(rand(1,$sector_max-1));
                $insert.="($link1,$link2), ($link2,$link1)";
                if($j<($finish-1)) $insert .= ", "; else $insert .= ";";
            }
//          PrintFlush("<font color='#FFFF00'>Creating loop $i of $loopsize Random Two-way Links (from sector ".($start)." to ".($finish-1).") - loop $i</font><br>\n");
//          $db->Execute($insert);
            if ($start<$sector_max && $finish<=$sector_max) $db->Execute($insert);

Table_Row("Creating loop $i of $loops Random Two-way Links (from sector ".($start)." to ".($finish-1).") - loop $i","Failed","Created");

            $start=$finish;
            $finish += $loopsize;
            if ($finish>$sector_max) $finish=($sector_max);
        }

Table_Footer("Completed successfully.");

      echo "<form action=create_universe.php method=post>";
      echo "<input type=hidden name=step value=7>";
      echo "<input type=hidden name=spp value=$spp>";
      echo "<input type=hidden name=oep value=$oep>";
      echo "<input type=hidden name=ogp value=$ogp>";
      echo "<input type=hidden name=gop value=$gop>";
      echo "<input type=hidden name=enp value=$enp>";
      echo "<input type=hidden name=initscommod value=$initscommod>";
      echo "<input type=hidden name=initbcommod value=$initbcommod>";
      echo "<input type=hidden name=nump value=$nump>";
      echo "<INPUT TYPE=HIDDEN NAME=fedsecs VALUE=$fedsecs>";
      echo "<input type=hidden name=loops value=$loops>";
      echo "<input type=hidden name=engage value=2>";
      echo "<input type=hidden name=swordfish value=$swordfish>";
      echo "<p align='center'><input type=submit value=Confirm></p>";
      echo "</form>";
      break;
   case "7":

    Table_Header("Configuring game scheduler --- Stage 7");

    Table_2Col("Update ticks will occur every $sched_ticks minutes.","<p align='center'><font face=\"Verdana\" size=\"1\" color=\"Blue\">Already Set</font></p>");

      $db->Execute("INSERT INTO $dbtables[scheduler] VALUES(NULL, 'Y', 0, $sched_turns, 0, 'sched_turns.php', NULL,unix_timestamp(now()))");
    Table_Row("Turns will occur every $sched_turns minutes","Failed","Inserted");

      $db->Execute("INSERT INTO $dbtables[scheduler] VALUES(NULL, 'Y', 0, $sched_turns, 0, 'sched_defenses.php', NULL,unix_timestamp(now()))");
    Table_Row("Defenses will be checked every $sched_turns minutes","Failed","Inserted");

      $db->Execute("INSERT INTO $dbtables[scheduler] VALUES(NULL, 'Y', 0, $sched_turns, 0, 'sched_xenobe.php', NULL,unix_timestamp(now()))");
    Table_Row("Xenobes will play every $sched_turns minutes.","Failed","Inserted");

      $db->Execute("INSERT INTO $dbtables[scheduler] VALUES(NULL, 'Y', 0, $sched_igb, 0, 'sched_igb.php', NULL,unix_timestamp(now()))");
    Table_Row("Interests on IGB accounts will be accumulated every $sched_igb minutes.","Failed","Inserted");

      $db->Execute("INSERT INTO $dbtables[scheduler] VALUES(NULL, 'Y', 0, $sched_news, 0, 'sched_news.php', NULL,unix_timestamp(now()))");
    Table_Row("News will be generated every $sched_news minutes.","Failed","Inserted");

      $db->Execute("INSERT INTO $dbtables[scheduler] VALUES(NULL, 'Y', 0, $sched_planets, 0, 'sched_planets.php', NULL,unix_timestamp(now()))");
    Table_Row("Planets will generate production every $sched_planets minutes.","Failed","Inserted");

      $db->Execute("INSERT INTO $dbtables[scheduler] VALUES(NULL, 'Y', 0, $sched_ports, 0, 'sched_ports.php', NULL,unix_timestamp(now()))");
    Table_Row("Ports will regenerate every $sched_ports minutes.","Failed","Inserted");

      $db->Execute("INSERT INTO $dbtables[scheduler] VALUES(NULL, 'Y', 0, $sched_turns, 0, 'sched_tow.php', NULL,unix_timestamp(now()))");
    Table_Row("Ships will be towed from fed sectors every $sched_turns minutes.","Failed","Inserted");

      $db->Execute("INSERT INTO $dbtables[scheduler] VALUES(NULL, 'Y', 0, $sched_ranking, 0, 'sched_ranking.php', NULL,unix_timestamp(now()))");
    Table_Row("Rankings will be generated every $sched_ranking minutes.","Failed","Inserted");

      $db->Execute("INSERT INTO $dbtables[scheduler] VALUES(NULL, 'Y', 0, $sched_degrade, 0, 'sched_degrade.php', NULL,unix_timestamp(now()))");
    Table_Row("Sector Defences will degrade every $sched_degrade minutes.","Failed","Inserted");

      $db->Execute("INSERT INTO $dbtables[scheduler] VALUES(NULL, 'Y', 0, $sched_apocalypse, 0, 'sched_apocalypse.php', NULL,unix_timestamp(now()))");
    Table_Row("The planetary apocalypse will occur every $sched_apocalypse minutes.","Failed","Inserted");

      $db->Execute("INSERT INTO $dbtables[scheduler] VALUES(NULL, 'Y', '60', '60', '0', 'bnt_ls_client.php', NULL, unix_timestamp(now()))");
        Table_Row("The Master server list update will occur every 60 minutes.","Failed","Inserted");


    Table_Footer("Completed successfully");

    Table_Header("Inserting Admins Acount Information");

      $update = $db->Execute("INSERT INTO $dbtables[ibank_accounts] (ship_id,balance,loan) VALUES (1,0,0)");
    Table_Row("Inserting Admins ibank Information","Failed","Inserted");
$password = $adminpass;
    $ship = new \BNT\Ship\Entity\Ship;
    $ship->ship_name = 'WebMaster';
    $ship->character_name = 'WebMaster';
    $ship->email = $admin_mail;
    
    $ship->password($password);

    $createShip = \BNT\Ship\Servant\ShipCreateServant::new($container);
    $createShip->ship = $ship;
    $createShip->serve();
    
    Table_1Col("Admins login Information:<br>Username: '$admin_mail'<br>Password: '$password'");
    Table_Row("Inserting Admins Ship Information","Failed","Inserted");

      $db->Execute("INSERT INTO $dbtables[zones] VALUES(NULL,'WebMaster\'s Territory', 1, 'N', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 0)");
    Table_Row("Inserting Admins Zone Information","Failed","Inserted");
    Table_Footer("Completed successfully.");

      PrintFlush("<BR><BR><center><BR><B>Congratulations! Universe created successfully.</B><BR>");
      PrintFlush("<B>Click <A HREF=login.php>here</A> to return to the login screen.</B></center>");

      break;
   default:
      echo "<form action=create_universe.php method=post>";
      echo "Password: <input type=password name=swordfish size=20 maxlength=20>&nbsp;&nbsp;";
      echo "<input type=submit value=Submit><input type=hidden name=step value=1>";
      echo "<input type=reset value=Reset>";
      echo "</form>";
      break;
}

$StopTime=$BenchmarkTimer->stop();
$Elapsed=$BenchmarkTimer->elapsed();
PrintFlush("<br>Elapsed Time - $Elapsed");
include("footer.php");



?>
