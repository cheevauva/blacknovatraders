<?php

$disableAutoLogin = true;

include 'config.php';

function PrintFlush($Text = "")
{
    print "$Text";
    flush();
}

function TRUEFALSE($truefalse, $Stat, $True, $False)
{
    return(($truefalse == $Stat) ? $True : $False);
}

function Table_Header($title = "")
{
    echo '<h3>' . $title . '</h3>';
}

function Table_Row($data, $failed = "Failed", $passed = "Passed")
{
    global $db;
    $err = TRUEFALSE(0, $db->ErrorNo(), "No errors found", $db->ErrorNo() . ": " . $db->ErrorMsg());
    echo '<pre>', $data, '<pre>';
    if ($db->ErrorNo() != 0) {
        echo $failed;
    } else {
        echo $passed;
    }
    echo '<br/>';
}

function Table_2Col($name, $value)
{
    echo '<pre>' . print_r([$name, $value]) . '</pre>';
}

function Table_1Col($data)
{
    echo '<pre>' . print_r($data) . '</pre>';
}

function Table_Spacer()
{
    echo '<br/>';
}

function Table_Footer($footer = '')
{
    
}

srand((double) microtime() * 1000000);

$title = "Create Universe";

if ($adminpass != $_POST['swordfish']) {
    $step = "0";
}

if ($engage == "" && $adminpass == $_POST['swordfish']) {
    $step = "1";
}

if ($engage == "1" && $adminpass == $_POST['swordfish']) {
    $step = "2";
}

### Main switch statement.

switch ($step) {
    case "1":
        $fedsecs = intval($sector_max / 200);
        $loops = intval($sector_max / 500);

        include 'tpls/create_universe/create_universe_step1.tpl.php';
        return;
    case "2":
        $spp = round($sector_max * $special / 100);
        $oep = round($sector_max * $ore / 100);
        $ogp = round($sector_max * $organics / 100);
        $gop = round($sector_max * $goods / 100);
        $enp = round($sector_max * $energy / 100);
        $empty = $sector_max - $spp - $oep - $ogp - $gop - $enp;
        $nump = round($sector_max * $planets / 100);
        $sector_max = round($sektors);

        include 'tpls/create_universe/create_universe_step2.tpl.php';
        return;
    case "4":
        $initsore = $ore_limit * $initscommod / 100.0;
        $initsorganics = $organics_limit * $initscommod / 100.0;
        $initsgoods = $goods_limit * $initscommod / 100.0;
        $initsenergy = $energy_limit * $initscommod / 100.0;
        $initbore = $ore_limit * $initbcommod / 100.0;
        $initborganics = $organics_limit * $initbcommod / 100.0;
        $initbgoods = $goods_limit * $initbcommod / 100.0;
        $initbenergy = $energy_limit * $initbcommod / 100.0;
        $remaining = $sector_max - 2;
        
        $sql = "
        INSERT INTO universe (sector_id, zone_id, angle1, angle2, distance) 
        SELECT 
            null,
            1,
            FLOOR(RAND() * 180) AS angle1,
            FLOOR(RAND() * 90) AS angle2,
            ROUND(RAND() * :universe_size) AS distance
        FROM 
            information_schema.tables t1
        CROSS JOIN 
            information_schema.tables t2
        CROSS JOIN 
            information_schema.tables t3
        LIMIT :sector_max
        ";

        db()->q($sql, [
            'sector_max' => (int) $sector_max,
            'universe_size'=> $universe_size,
        ], [
            'sector_max' => \PDO::PARAM_INT,
        ]);
        
        db()->q("UPDATE zones SET max_hull = :fed_max_hull WHERE zone_id = 2", [
            'fed_max_hull'=> $fed_max_hull,
        ]);
        db()->q("UPDATE universe SET zone_id= 2 WHERE sector_id < :fedsecs", [
            'fedsecs' => $fedsecs,
        ]);

        $specialPortsSql = "
        UPDATE 
            universe 
        SET 
            zone_id = 3,
            port_type = 'special' 
        WHERE 
            port_type = 'none' AND 
            sector_id IN (
                SELECT 
                    s.sector_id 
                FROM 
                    (
                        SELECT 
                            sector_id 
                        FROM 
                            universe 
                        WHERE 
                            port_type = 'none' 
                        ORDER BY 
                            rand() DESC 
                        LIMIT :spp
                    ) AS s
            )
        ";

        db()->q($specialPortsSql, [
            'spp' => (int) $spp,
        ], [
            'spp' => \PDO::PARAM_INT,
        ]);

        function updateNotSpecialPort($portType, $limit)
        {
            global $initsore, $initborganics, $initbgoods, $initbenergy;
            $sql = "
            UPDATE 
                universe 
            SET 
                port_ore = :initsore,
                port_organics = :initborganics,
                port_goods = :initbgoods,
                port_energy = :initbenergy,
                port_type = :portType
            WHERE 
                port_type = 'none' AND 
                sector_id IN (
                    SELECT 
                        s.sector_id 
                    FROM 
                        (
                            SELECT 
                                sector_id 
                            FROM 
                                universe 
                            WHERE 
                                port_type = 'none' 
                            ORDER BY 
                                rand() DESC 
                            LIMIT :limit
                        ) AS s
                )
            ";
            db()->q($sql, [
                'limit' => (int) $limit,
                'portType' => $portType,
                'initsore' => $initsore,
                'initborganics' => $initborganics,
                'initbgoods' => $initbgoods,
                'initbenergy' => $initbenergy,
            ], [
                'limit' => \PDO::PARAM_INT,
            ]);
        }

        updateNotSpecialPort('ore', $oep);
        updateNotSpecialPort('organics', $ogp);
        updateNotSpecialPort('goods', $gop);
        updateNotSpecialPort('energy', $enp);

        // @todo show info
        Table_Header("Setting up Sectors --- STAGE 4");
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

        $p_add = 0;
        $p_skip = 0;
        $i = 0;

        Table_Header("Setting up Universe Sectors --- Stage 5");

        do {
            $num = rand(2, ($sector_max - 1));
            $select = $db->adoExecute("SELECT universe.sector_id FROM universe, zones WHERE universe.sector_id=$num AND zones.zone_id=universe.zone_id AND zones.allow_planet='N'") or die("DB error");
            if ($select->RecordCount() == 0) {
                $insert = $db->adoExecute("INSERT INTO planets (colonists, owner, corp, prod_ore, prod_organics, prod_goods, prod_energy, prod_fighters, prod_torp, sector_id) VALUES (2,0,0,$default_prod_ore,$default_prod_organics,$default_prod_goods,$default_prod_energy, $default_prod_fighters, $default_prod_torp,$num)");
                $p_add++;
            }
        } while ($p_add < $nump);
        
        
        Table_Row("Selecting $nump sectors to place unowned planets in.", "Failed", "Selected");

        Table_Spacer();

## Adds Sector Size *2 amount of links to the links table ##
        # !!!!! DO NOT ALTER LOOPSIZE !!!!!
        # This should be balanced 50%/50% PHP/MySQL load :)

        $loopsize = 500;
        $loops = round($sector_max / $loopsize) + 1;
        if ($loops <= 0)
            $loops = 1;
        $finish = $loopsize;
        if ($finish > $sector_max)
            $finish = ($sector_max);
        $start = 0;

        for ($i = 1; $i <= $loops; $i++) {
            $update = "INSERT INTO links (link_start,link_dest) VALUES ";
            for ($j = $start; $j < $finish; $j++) {
                $k = $j + 1;
                $update .= "($j,$k), ($k,$j)";
                if ($j < ($finish - 1))
                    $update .= ", ";
                else
                    $update .= ";";
            }
            if ($start < $sector_max && $finish <= $sector_max)
                $db->adoExecute($update);

            Table_Row("Creating loop $i of $loops sectors (from sector " . ($start) . " to " . ($finish - 1) . ") - loop $i", "Failed", "Created");

            $start = $finish;
            $finish += $loopsize;
            if ($finish > $sector_max)
                $finish = $sector_max;
        }

//      PrintFlush("<BR>Sector Links created successfully.<BR>");
####################

        Table_Spacer();

//      PrintFlush("<BR>Randomly One-way Linking $i Sectors (out of $sector_max sectors)<br>\n");
## Adds Sector Size amount of links to the links table ##
        # !!!!! DO NOT ALTER LOOPSIZE !!!!!
        # This should be balanced 50%/50% PHP/MySQL load :)

        $loopsize = 500;
        $loops = round($sector_max / $loopsize) + 1;
        if ($loops <= 0)
            $loops = 1;
        $finish = $loopsize;
        if ($finish > $sector_max)
            $finish = ($sector_max);
        $start = 0;

        for ($i = 1; $i <= $loops; $i++) {
            $insert = "INSERT INTO links (link_start,link_dest) VALUES ";
            for ($j = $start; $j < $finish; $j++) {
                $link1 = intval(rand(1, $sector_max - 1));
                $link2 = intval(rand(1, $sector_max - 1));
                $insert .= "($link1,$link2)";
                if ($j < ($finish - 1))
                    $insert .= ", ";
                else
                    $insert .= ";";
            }
#           PrintFlush("<font color='#FFFF00'>Creating loop $i of $loopsize Random One-way Links (from sector ".($start)." to ".($finish-1).") - loop $i</font><br>\n");

            if ($start < $sector_max && $finish <= $sector_max)
                $db->adoExecute($insert);

//          $db->Execute($insert);

            Table_Row("Creating loop $i of $loops Random One-way Links (from sector " . ($start) . " to " . ($finish - 1) . ") - loop $i", "Failed", "Created");

            $start = $finish;
            $finish += $loopsize;
            if ($finish > $sector_max)
                $finish = ($sector_max);
        }

//      PrintFlush("Completed successfully.<BR>\n");
######################

        Table_Spacer();

//      PrintFlush("<BR>Randomly Two-way Linking Sectors<br>\n");
## Adds Sector Size*2 amount of links to the links table ##
        # !!!!! DO NOT ALTER LOOPSIZE !!!!!
        # This should be balanced 50%/50% PHP/MySQL load :)

        $loopsize = 500;
        $loops = round($sector_max / $loopsize) + 1;
        if ($loops <= 0)
            $loops = 1;
        $finish = $loopsize;
        if ($finish > $sector_max)
            $finish = ($sector_max);
        $start = 0;

        for ($i = 1; $i <= $loops; $i++) {
            $insert = "INSERT INTO links (link_start,link_dest) VALUES ";
            for ($j = $start; $j < $finish; $j++) {
                $link1 = intval(rand(1, $sector_max - 1));
                $link2 = intval(rand(1, $sector_max - 1));
                $insert .= "($link1,$link2), ($link2,$link1)";
                if ($j < ($finish - 1))
                    $insert .= ", ";
                else
                    $insert .= ";";
            }
//          PrintFlush("<font color='#FFFF00'>Creating loop $i of $loopsize Random Two-way Links (from sector ".($start)." to ".($finish-1).") - loop $i</font><br>\n");
//          $db->Execute($insert);
            if ($start < $sector_max && $finish <= $sector_max)
                $db->adoExecute($insert);

            Table_Row("Creating loop $i of $loops Random Two-way Links (from sector " . ($start) . " to " . ($finish - 1) . ") - loop $i", "Failed", "Created");

            $start = $finish;
            $finish += $loopsize;
            if ($finish > $sector_max)
                $finish = ($sector_max);
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
        $db->adoExecute("INSERT INTO scheduler VALUES(NULL, 'Y', 0, $sched_turns, 0, 'sched_turns.php', NULL,unix_timestamp(now()))");
        $db->adoExecute("INSERT INTO scheduler VALUES(NULL, 'Y', 0, $sched_turns, 0, 'sched_defenses.php', NULL,unix_timestamp(now()))");
        $db->adoExecute("INSERT INTO scheduler VALUES(NULL, 'Y', 0, $sched_turns, 0, 'sched_xenobe.php', NULL,unix_timestamp(now()))");
        $db->adoExecute("INSERT INTO scheduler VALUES(NULL, 'Y', 0, $sched_igb, 0, 'sched_igb.php', NULL,unix_timestamp(now()))");
        $db->adoExecute("INSERT INTO scheduler VALUES(NULL, 'Y', 0, $sched_news, 0, 'sched_news.php', NULL,unix_timestamp(now()))");
        $db->adoExecute("INSERT INTO scheduler VALUES(NULL, 'Y', 0, $sched_planets, 0, 'sched_planets.php', NULL,unix_timestamp(now()))");
        $db->adoExecute("INSERT INTO scheduler VALUES(NULL, 'Y', 0, $sched_ports, 0, 'sched_ports.php', NULL,unix_timestamp(now()))");
        $db->adoExecute("INSERT INTO scheduler VALUES(NULL, 'Y', 0, $sched_turns, 0, 'sched_tow.php', NULL,unix_timestamp(now()))");
        $db->adoExecute("INSERT INTO scheduler VALUES(NULL, 'Y', 0, $sched_ranking, 0, 'sched_ranking.php', NULL,unix_timestamp(now()))");
        $db->adoExecute("INSERT INTO scheduler VALUES(NULL, 'Y', 0, $sched_degrade, 0, 'sched_degrade.php', NULL,unix_timestamp(now()))");
        $db->adoExecute("INSERT INTO scheduler VALUES(NULL, 'Y', 0, $sched_apocalypse, 0, 'sched_apocalypse.php', NULL,unix_timestamp(now()))");
        $db->adoExecute("INSERT INTO ibank_accounts (ship_id,balance,loan) VALUES (1,0,0)");

        shipCreate([
            'ship_name' => 'WebMaster',
            'character_name' => 'WebMaster',
            'password' => md5($admin_pass),
            'email' => $admin_mail,
            'armor_pts' => $start_armor,
            'credits' => $start_credits,
            'ship_energy' => $start_energy,
            'ship_fighters' => $start_fighters,
            'turns' => $start_turns,
            'last_login' => date('Y-m-d H:i:s'),
            'lang' => $language,
            'role' => 'admin'
        ]);

        $db->adoExecute("INSERT INTO zones VALUES(NULL,'WebMaster\'s Territory', 1, 'N', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 0)");

        include 'tpls/create_universe/create_universe_step7.tpl.php';
        break;
    default:
        include 'tpls/create_universe/create_universe_login.tpl.php';
        break;
}


