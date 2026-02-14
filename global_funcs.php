<?php

use BNT\ADODB\ADOPDO;
use BNT\Log\LogTypeConstants;
use BNT\Ship\Servant\ShipEscapepodServant;

preg_match("/global_funcs.php/i", $_SERVER['PHP_SELF']) ? die("You can not access this file directly!") : null;

function db(): ADOPDO
{
    global $db;

    return $db;
}

function uuidv7()
{
    $timestamp = intval(microtime(true) * 1000);

    return sprintf('%02x%02x%02x%02x-%02x%02x-%04x-%04x-%012x', ($timestamp >> 40) & 0xFF, ($timestamp >> 32) & 0xFF, ($timestamp >> 24) & 0xFF, ($timestamp >> 16) & 0xFF, ($timestamp >> 8) & 0xFF, $timestamp & 0xFF, mt_rand(0, 0x0FFF) | 0x7000, mt_rand(0, 0x3FFF) | 0x8000, mt_rand(0, 0xFFFFFFFFFFFF));
}

function redirectTo($location)
{
    header('Location: ' . $location, true, 302);
}

function responseJsonByMessages(array $messages)
{
    return json_encode([
        'success' => true,
        'type' => 'messages',
        'messages' => array_values($messages),
    ], JSON_UNESCAPED_UNICODE);
}

function responseJsonByException(\Exception $ex)
{
    return json_encode([
        'success' => false,
        'type' => 'exception',
        'error' => $ex->getMessage(),
        'code' => $ex->getCode(),
    ], JSON_UNESCAPED_UNICODE);
}

function requestMethod()
{
    return $_SERVER['REQUEST_METHOD'];
}

function languages()
{
    global $avail_lang;

    $languages = [];

    foreach ($avail_lang as $curlang) {
        $languages[$curlang['file']] = $curlang['name'];
    }

    return $languages;
}

function fromRequest($name, $default = null)
{
    $fromGet = fromGET($name);

    if ($fromGet) {
        return $fromGet;
    }

    return fromPOST($name, $default);
}

function fromPOST($name, $default = null)
{
    if (!isset($_POST[$name]) && $default instanceof \Exception) {
        throw $default;
    }

    if (!isset($_POST[$name])) {
        return $default;
    }

    return $_POST[$name];
}

function fromGET($name, $default = null)
{
    if (!isset($_GET[$name]) && $default instanceof \Exception) {
        throw $default;
    }

    if (!isset($_GET[$name])) {
        return $default;
    }

    return $_GET[$name];
}

function mypw($one, $two)
{
    return pow($one * 1, $two * 1);
}

function bigtitle()
{
    global $title;
    echo "<h1>$title</h1>";
}

function checklogin($return = true)
{
    global $username, $playerinfo, $l_global_needlogin, $l_global_died;
    global $l_login_died, $l_die_please;
    global $server_closed, $l_login_closed_message;

    if ($server_closed) {
        echo $return ? $l_login_closed_message : '';
        return true;
    }

    if (empty($playerinfo)) {
        echo $return ? $l_global_needlogin : '';
        return true;
    }

    if ($playerinfo['ship_destroyed'] == "N") {
        $username = $playerinfo['email'];
        return false;
    }
    if ($playerinfo['dev_escapepod'] == "Y") {
        $escapepod = ShipEscapepodServant::new($this->container);
        $escapepod->ship = $playerinfo;
        $escapepod->serve();
        echo $return ? $l_login_died : '';
        return true;
    }

    echo $return ? $l_global_died . $l_die_please : '';

    return true;
}

function playerlog($sid, $log_type, $data = "")
{
    if (is_array($data)) {
        $data = implode('|', $data);
    }
    if (empty($sid) || empty($log_type)) {
        return;
    }

    db()->q("INSERT INTO logs VALUES(NULL, :sid, :log_type, NOW(), :data)", [
        'sid' => $sid,
        'log_type' => $log_type,
        'data' => $data,
    ]);
}

function adminlog($log_type, $data = "")
{

    /* write log_entry to the admin log  */
    if (!empty($log_type)) {
        db()->q("INSERT INTO logs VALUES(NULL, 0, :log_type, NOW(), :data)", [
            'log_type' => $log_type,
            'data' => $data,
        ]);
    }
}

function gen_score($sid)
{
    global $ore_price;
    global $organics_price;
    global $goods_price;
    global $energy_price;
    global $upgrade_cost;
    global $upgrade_factor;
    global $dev_genesis_price;
    global $dev_beacon_price;
    global $dev_emerwarp_price;
    global $dev_warpedit_price;
    global $dev_minedeflector_price;
    global $dev_escapepod_price;
    global $dev_fuelscoop_price;
    global $dev_lssd_price;
    global $fighter_price;
    global $torpedo_price;
    global $armor_price;
    global $colonist_price;
    global $base_ore;
    global $base_goods;
    global $base_organics;
    global $base_credits;

    $calc_hull = "ROUND(pow($upgrade_factor,hull))";
    $calc_engines = "ROUND(pow($upgrade_factor,engines))";
    $calc_power = "ROUND(pow($upgrade_factor,power))";
    $calc_computer = "ROUND(pow($upgrade_factor,computer))";
    $calc_sensors = "ROUND(pow($upgrade_factor,sensors))";
    $calc_beams = "ROUND(pow($upgrade_factor,beams))";
    $calc_torp_launchers = "ROUND(pow($upgrade_factor,torp_launchers))";
    $calc_shields = "ROUND(pow($upgrade_factor,shields))";
    $calc_armor = "ROUND(pow($upgrade_factor,armor))";
    $calc_cloak = "ROUND(pow($upgrade_factor,cloak))";
    $calc_levels = "($calc_hull+$calc_engines+$calc_power+$calc_computer+$calc_sensors+$calc_beams+$calc_torp_launchers+$calc_shields+$calc_armor+$calc_cloak)*$upgrade_cost";

    $calc_torps = "ships.torps*$torpedo_price";
    $calc_armor_pts = "armor_pts*$armor_price";
    $calc_ship_ore = "ship_ore*$ore_price";
    $calc_ship_organics = "ship_organics*$organics_price";
    $calc_ship_goods = "ship_goods*$goods_price";
    $calc_ship_energy = "ship_energy*$energy_price";
    $calc_ship_colonists = "ship_colonists*$colonist_price";
    $calc_ship_fighters = "ship_fighters*$fighter_price";
    $calc_equip = "$calc_torps+$calc_armor_pts+$calc_ship_ore+$calc_ship_organics+$calc_ship_goods+$calc_ship_energy+$calc_ship_colonists+$calc_ship_fighters";

    $calc_dev_warpedit = "dev_warpedit*$dev_warpedit_price";
    $calc_dev_genesis = "dev_genesis*$dev_genesis_price";
    $calc_dev_beacon = "dev_beacon*$dev_beacon_price";
    $calc_dev_emerwarp = "dev_emerwarp*$dev_emerwarp_price";
    $calc_dev_escapepod = "IF(dev_escapepod='Y', $dev_escapepod_price, 0)";
    $calc_dev_fuelscoop = "IF(dev_fuelscoop='Y', $dev_fuelscoop_price, 0)";
    $calc_dev_lssd = "IF(dev_lssd='Y', $dev_lssd_price, 0)";
    $calc_dev_minedeflector = "dev_minedeflector*$dev_minedeflector_price";
    $calc_dev = "$calc_dev_warpedit+$calc_dev_genesis+$calc_dev_beacon+$calc_dev_emerwarp+$calc_dev_escapepod+$calc_dev_fuelscoop+$calc_dev_minedeflector+$calc_dev_lssd";

    $calc_planet_goods = "if(planets.planet_id,SUM(planets.organics)*$organics_price+SUM(planets.ore)*$ore_price+SUM(planets.goods)*$goods_price+SUM(planets.energy)*$energy_price";
    $calc_planet_colonists = "SUM(planets.colonists)*$colonist_price";
    $calc_planet_defence = "SUM(planets.fighters)*$fighter_price+IF(planets.base='Y', $base_credits+SUM(planets.torps)*$torpedo_price, 0)";
    $calc_planet_credits = "SUM(planets.credits),0)";

    $row = db()->fetch("SELECT $calc_levels+$calc_equip+$calc_dev+ships.credits+$calc_planet_goods+$calc_planet_colonists+$calc_planet_defence+$calc_planet_credits AS score FROM ships LEFT JOIN planets ON planets.owner=ship_id WHERE ship_id=:sid AND ship_destroyed='N'", [
        'sid' => $sid,
    ]);
    $score = $row['score'];

    $row = db()->fetch("SELECT balance, loan FROM ibank_accounts where ship_id = :sid", [
        'sid' => $sid,
    ]);
    if ($row) {
        $score += ($row['balance'] - $row['loan']);
    }
    $score = ROUND(SQRT($score));

    db()->q("UPDATE ships SET score=:score WHERE ship_id=:sid", [
        'score' => $score,
        'sid' => $sid,
    ]);

    return $score;
}

function db_kill_player($ship_id)
{
    global $default_prod_ore;
    global $default_prod_organics;
    global $default_prod_goods;
    global $default_prod_energy;
    global $default_prod_fighters;
    global $default_prod_torp;
    global $gameroot;

    include("languages/english.inc");

    db()->q("UPDATE ships SET ship_destroyed='Y',on_planet='N',sector=0,cleared_defences=' ' WHERE ship_id=:ship_id", [
        'ship_id' => $ship_id,
    ]);
    db()->q("DELETE from bounty WHERE placed_by = :ship_id", [
        'ship_id' => $ship_id,
    ]);

    $res = db()->fetchAll("SELECT DISTINCT sector_id FROM planets WHERE owner=:ship_id AND base='Y'", [
        'ship_id' => $ship_id,
    ]);
    $i = 0;
    $sectors = [];

    foreach ($res as $row) {
        $sectors[$i] = $row['sector_id'];
        $i++;
    }

    db()->q("UPDATE planets SET owner=0,fighters=0, base='N' WHERE owner=:ship_id", [
        'ship_id' => $ship_id,
    ]);

    if (!empty($sectors)) {
        foreach ($sectors as $sector) {
            calc_ownership($sector);
        }
    }
    db()->q("DELETE FROM sector_defence where ship_id=:ship_id", [
        'ship_id' => $ship_id,
    ]);

    $row = db()->fetch("SELECT zone_id FROM zones WHERE corp_zone='N' AND owner=:ship_id", [
        'ship_id' => $ship_id,
    ]);
    $zone = $row;

    db()->q("UPDATE universe SET zone_id=:zone_id WHERE zone_id=:old_zone_id", [
        'zone_id' => 1,
        'old_zone_id' => $zone['zone_id'],
    ]);

    $row = db()->fetch("select character_name from ships where ship_id=:ship_id", [
        'ship_id' => $ship_id,
    ]);
    $name = $row;

    $headline = $name['character_name'] . $l_killheadline;

    $newstext = str_replace("[name]", $name['character_name'], $l_news_killed);

    db()->q("INSERT INTO news (headline, newstext, user_id, date, news_type) VALUES (:headline, :newstext, :ship_id, NOW(), 'killed')", [
        'headline' => $headline,
        'newstext' => $newstext,
        'ship_id' => $ship_id,
    ]);
}

function NUMBER($number, $decimals = 0)
{
    global $local_number_dec_point;
    global $local_number_thousands_sep;
    return number_format($number, $decimals, $local_number_dec_point, $local_number_thousands_sep);
}

function NUM_HOLDS($level_hull)
{
    global $level_factor;
    return round(mypw($level_factor, $level_hull) * 100);
}

function NUM_ENERGY($level_power)
{
    global $level_factor;
    return round(mypw($level_factor, $level_power) * 500);
}

function NUM_FIGHTERS($level_computer)
{
    global $level_factor;
    return round(mypw($level_factor, $level_computer) * 100);
}

function NUM_TORPEDOES($level_torp_launchers)
{
    global $level_factor;
    return round(mypw($level_factor, $level_torp_launchers) * 100);
}

function NUM_ARMOUR($level_armor)
{
    global $level_factor;
    return round(mypw($level_factor, $level_armor) * 100);
}

function NUM_BEAMS($level_beams)
{
    global $level_factor;
    return round(mypw($level_factor, $level_beams) * 100);
}

function NUM_SHIELDS($level_shields)
{
    global $level_factor;
    return round(mypw($level_factor, $level_shields) * 100);
}

function SCAN_SUCCESS($level_scan, $level_cloak)
{
    return (5 + $level_scan - $level_cloak) * 5;
}

function SCAN_ERROR($level_scan, $level_cloak)
{
    global $scan_error_factor;

    $sc_error = (4 + $level_scan / 2 - $level_cloak / 2) * $scan_error_factor;

    if ($sc_error < 1) {
        $sc_error = 1;
    }
    if ($sc_error > 99) {
        $sc_error = 99;
    }

    return $sc_error;
}

function explode_mines($sector, $num_mines)
{


    $result3 = db()->fetchAll("SELECT * FROM sector_defence WHERE sector_id=:sector and defence_type ='M' order by quantity ASC", [
        'sector' => $sector,
    ]);

    //Put the defence information into the array "defenceinfo"
    if (!empty($result3)) {
        foreach ($result3 as $row) {
            if ($num_mines <= 0)
                break;

            if ($row['quantity'] > $num_mines) {
                db()->q("UPDATE sector_defence set quantity=quantity - :num_mines where defence_id = :defence_id", [
                    'num_mines' => $num_mines,
                    'defence_id' => $row['defence_id'],
                ]);
                $num_mines = 0;
            } else {
                db()->q("DELETE FROM sector_defence WHERE defence_id = :defence_id", [
                    'defence_id' => $row['defence_id'],
                ]);
                $num_mines -= $row['quantity'];
            }
        }
    }
}

function destroy_fighters($sector, $num_fighters)
{


    $result3 = db()->fetchAll("SELECT * FROM sector_defence WHERE sector_id=:sector and defence_type ='F' order by quantity ASC", [
        'sector' => $sector,
    ]);

    //Put the defence information into the array "defenceinfo"
    if (!empty($result3)) {
        foreach ($result3 as $row) {
            if ($num_fighters <= 0)
                break;

            if ($row['quantity'] > $num_fighters) {
                db()->q("UPDATE sector_defence set quantity=quantity - :num_fighters where defence_id = :defence_id", [
                    'num_fighters' => $num_fighters,
                    'defence_id' => $row['defence_id'],
                ]);
                $num_fighters = 0;
            } else {
                db()->q("DELETE FROM sector_defence WHERE defence_id = :defence_id", [
                    'defence_id' => $row['defence_id'],
                ]);
                $num_fighters -= $row['quantity'];
            }
        }
    }
}

function message_defence_owner($sector, $message)
{

    $result3 = db()->fetchAll("SELECT * FROM sector_defence WHERE sector_id=:sector", [
        'sector' => $sector,
    ]);

    //Put the defence information into the array "defenceinfo"
    if (!empty($result3)) {
        foreach ($result3 as $row) {
            playerlog($row['ship_id'], LogTypeConstants::LOG_RAW, $message);
        }
    }
}

function distribute_toll($sector, $toll, $total_fighters)
{
    //Put the defence information into the array "defenceinfo"
    foreach (defencesBySectorAndFighters($sector) as $defence) {
        $toll_amount = ROUND(($defence['quantity'] / $total_fighters) * $toll);
        shipCreditsAdd($defence['ship_id'], $toll_amount);
        playerlog($defence['ship_id'], LogTypeConstants::LOG_TOLL_RECV, "$toll_amount|$sector");
    }
}

function defence_vs_defence($ship_id)
{


    $result1 = db()->fetchAll("SELECT * from sector_defence where ship_id = :ship_id", [
        'ship_id' => $ship_id,
    ]);

    if (!empty($result1)) {
        foreach ($result1 as $row) {
            $deftype = $row['defence_type'] == 'F' ? 'Fighters' : 'Mines';
            $qty = $row['quantity'];

            $result2 = db()->fetchAll("SELECT * from sector_defence where sector_id = :sector_id and ship_id <> :ship_id ORDER BY quantity DESC", [
                'sector_id' => $row['sector_id'],
                'ship_id' => $ship_id,
            ]);

            if (!empty($result2)) {
                foreach ($result2 as $cur) {
                    if ($qty <= 0)
                        break;

                    $targetdeftype = $cur['defence_type'] == 'F' ? $l_fighters : $l_mines;

                    if ($qty > $cur['quantity']) {
                        db()->q("DELETE FROM sector_defence WHERE defence_id = :defence_id", [
                            'defence_id' => $cur['defence_id'],
                        ]);
                        $qty -= $cur['quantity'];

                        db()->q("UPDATE sector_defence SET quantity = :qty where defence_id = :defence_id", [
                            'qty' => $qty,
                            'defence_id' => $row['defence_id'],
                        ]);

                        playerlog($cur['ship_id'], LogTypeConstants::LOG_DEFS_DESTROYED, "$cur[quantity]|$targetdeftype|$row[sector_id]");
                        playerlog($row['ship_id'], LogTypeConstants::LOG_DEFS_DESTROYED, "$cur[quantity]|$deftype|$row[sector_id]");
                    } else {
                        db()->q("DELETE FROM sector_defence WHERE defence_id = :defence_id", [
                            'defence_id' => $row['defence_id'],
                        ]);

                        db()->q("UPDATE sector_defence SET quantity=quantity - :qty WHERE defence_id = :defence_id", [
                            'qty' => $qty,
                            'defence_id' => $cur['defence_id'],
                        ]);

                        playerlog($cur['ship_id'], LogTypeConstants::LOG_DEFS_DESTROYED, "$qty|$targetdeftype|$row[sector_id]");
                        playerlog($row['ship_id'], LogTypeConstants::LOG_DEFS_DESTROYED, "$qty|$deftype|$row[sector_id]");
                        $qty = 0;
                    }
                }
            }
        }
        db()->q("DELETE FROM sector_defence WHERE quantity <= 0");
    }
}

function kick_off_planet($ship_id, $whichteam)
{


    $result1 = db()->fetchAll("SELECT * from planets where owner = :ship_id", [
        'ship_id' => $ship_id,
    ]);

    if (!empty($result1)) {
        foreach ($result1 as $row) {
            $result2 = db()->fetchAll("SELECT * from ships where on_planet = 'Y' and planet_id = :planet_id and ship_id <> :ship_id", [
                'planet_id' => $row['planet_id'],
                'ship_id' => $ship_id,
            ]);

            if (!empty($result2)) {
                foreach ($result2 as $cur) {
                    db()->q("UPDATE ships SET on_planet = 'N',planet_id = '0' WHERE ship_id=:ship_id", [
                        'ship_id' => $cur['ship_id'],
                    ]);
                    playerlog($cur['ship_id'], LogTypeConstants::LOG_PLANET_EJECT, "$cur[sector]|$row[character_name]");
                }
            }
        }
    }
}

function calc_ownership($sector)
{
    global $min_bases_to_own, $l_global_warzone, $l_global_nzone, $l_global_team, $l_global_player;

    $res = db()->fetchAll("SELECT owner, corp FROM planets WHERE sector_id=:sector AND base='Y'", [
        'sector' => $sector,
    ]);
    $num_bases = count($res);

    $i = 0;
    if ($num_bases > 0) {
        foreach ($res as $row) {
            $bases[$i] = $row;
            $i++;
        }
    } else {
        return "Sector ownership didn't change";
    }

    $owner_num = 0;
    $owners = [];

    foreach ($bases as $curbase) {
        $curcorp = -1;
        $curship = -1;
        $loop = 0;
        while ($loop < $owner_num) {
            if ($curbase['corp'] != 0) {
                if ($owners[$loop]['type'] == 'C') {
                    if ($owners[$loop]['id'] == $curbase['corp']) {
                        $curcorp = $loop;
                        $owners[$loop]['num']++;
                    }
                }
            }

            if ($owners[$loop]['type'] == 'S') {
                if ($owners[$loop]['id'] == $curbase['owner']) {
                    $curship = $loop;
                    $owners[$loop]['num']++;
                }
            }

            $loop++;
        }

        if ($curcorp == -1) {
            if ($curbase['corp'] != 0) {
                $curcorp = $owner_num;
                $owner_num++;
                $owners[$curcorp]['type'] = 'C';
                $owners[$curcorp]['num'] = 1;
                $owners[$curcorp]['id'] = $curbase['corp'];
            }
        }

        if ($curship == -1) {
            if ($curbase['owner'] != 0) {
                $curship = $owner_num;
                $owner_num++;
                $owners[$curship]['type'] = 'S';
                $owners[$curship]['num'] = 1;
                $owners[$curship]['id'] = $curbase['owner'];
            }
        }
    }

    // We've got all the contenders with their bases.
    // Time to test for conflict

    $loop = 0;
    $nbcorps = 0;
    $nbships = 0;
    $ships = [];
    $scorps = [];

    while ($loop < $owner_num) {
        if ($owners[$loop]['type'] == 'C') {
            $nbcorps++;
        } else {
            $row = db()->fetch("SELECT team FROM ships WHERE ship_id=:ship_id", [
                'ship_id' => $owners[$loop]['id'],
            ]);
            if ($row) {
                $curship = $row;
                $ships[$nbships] = $owners[$loop]['id'];
                $scorps[$nbships] = $curship['team'];
                $nbships++;
            }
        }

        $loop++;
    }

    //More than one corp, war
    if ($nbcorps > 1) {
        db()->q("UPDATE universe SET zone_id=4 WHERE sector_id=:sector", [
            'sector' => $sector,
        ]);
        return $l_global_warzone;
    }

    //More than one unallied ship, war
    $numunallied = 0;
    foreach ($scorps as $corp) {
        if ($corp == 0) {
            $numunallied++;
        }
    }
    if ($numunallied > 1) {
        db()->q("UPDATE universe SET zone_id=4 WHERE sector_id=:sector", [
            'sector' => $sector,
        ]);
        return $l_global_warzone;
    }

    //Unallied ship, another corp present, war
    if ($numunallied > 0 && $nbcorps > 0) {
        db()->q("UPDATE universe SET zone_id=4 WHERE sector_id=:sector", [
            'sector' => $sector,
        ]);
        return $l_global_warzone;
    }

    //Unallied ship, another ship in a corp, war
    if ($numunallied > 0) {
        $query = "SELECT team FROM ships WHERE (";
        $i = 0;
        $params = [];
        foreach ($ships as $ship) {
            $query = $query . "ship_id=:ship_id" . $i;
            $params['ship_id' . $i] = $ship;
            $i++;
            if ($i != $nbships) {
                $query = $query . " OR ";
            } else {
                $query = $query . ")";
            }
        }
        $query = $query . " AND team!=0";
        $res = db()->fetchAll($query, $params);
        if (!empty($res)) {
            db()->q("UPDATE universe SET zone_id=4 WHERE sector_id=:sector", [
                'sector' => $sector,
            ]);
            return $l_global_warzone;
        }
    }


    //Ok, all bases are allied at this point. Let's make a winner.
    $winner = 0;
    $i = 1;
    while ($i < $owner_num) {
        if ($owners[$i]['num'] > $owners[$winner]['num']) {
            $winner = $i;
        } elseif ($owners[$i]['num'] == $owners[$winner]['num']) {
            if ($owners[$i]['type'] == 'C') {
                $winner = $i;
            }
        }
        $i++;
    }

    if ($owners[$winner]['num'] < $min_bases_to_own) {
        db()->q("UPDATE universe SET zone_id=1 WHERE sector_id=:sector", [
            'sector' => $sector,
        ]);
        return $l_global_nzone;
    }


    if ($owners[$winner]['type'] == 'C') {
        $row = db()->fetch("SELECT zone_id FROM zones WHERE corp_zone='Y' && owner=:owner", [
            'owner' => $owners[$winner]['id'],
        ]);
        $zone = $row;

        $row = db()->fetch("SELECT team_name FROM teams WHERE id=:id", [
            'id' => $owners[$winner]['id'],
        ]);
        $corp = $row;

        db()->q("UPDATE universe SET zone_id=:zone_id WHERE sector_id=:sector", [
            'zone_id' => $zone['zone_id'],
            'sector' => $sector,
        ]);
        return "$l_global_team $corp[team_name]!";
    } else {
        $onpar = 0;
        foreach ($owners as $curowner) {
            if ($curowner['type'] == 'S' && $curowner['id'] != $owners[$winner]['id'] && $curowner['num'] == $owners[$winner]['num']) {
                $onpar = 1;
            }
            break;
        }

        //Two allies have the same number of bases
        if ($onpar == 1) {
            db()->q("UPDATE universe SET zone_id=1 WHERE sector_id=:sector", [
                'sector' => $sector,
            ]);
            return $l_global_nzone;
        } else {
            $row = db()->fetch("SELECT zone_id FROM zones WHERE corp_zone='N' && owner=:owner", [
                'owner' => $owners[$winner]['id'],
            ]);
            $zone = $row;

            $row = db()->fetch("SELECT character_name FROM ships WHERE ship_id=:ship_id", [
                'ship_id' => $owners[$winner]['id'],
            ]);
            $ship = $row;

            db()->q("UPDATE universe SET zone_id=:zone_id WHERE sector_id=:sector", [
                'zone_id' => $zone['zone_id'],
                'sector' => $sector,
            ]);
            return "$l_global_player $ship[character_name]!";
        }
    }
}

function player_insignia_name($a_username)
{

// Somewhat inefficient, but I think this is the best way to do this.

    global $db, $username, $player_insignia;
    global $l_insignia;

    $row = db()->fetch("SELECT score FROM ships WHERE email=:email", [
        'email' => $a_username,
    ]);
    $playerinfo = $row;
    $score_array = array('1000', '3000', '6000', '9000', '12000', '15000', '20000', '40000', '60000', '80000', '100000', '120000', '160000', '200000', '250000', '300000', '350000', '400000', '450000', '500000');

    for ($i = 0; $i < count($score_array); $i++) {
        if ($playerinfo['score'] < $score_array[$i]) {
            $player_insignia = $l_insignia[$i];
            break;
        }
    }

    if (!isset($player_insignia)) {
        $player_insignia = end($l_insignia);
    }

    return $player_insignia;
}

function t_port($ptype)
{

    global $l_ore, $l_none, $l_energy, $l_organics, $l_goods, $l_special;

    switch ($ptype) {
        case "ore":
            $ret = $l_ore;
            break;
        case "none":
            $ret = $l_none;
            break;
        case "energy":
            $ret = $l_energy;
            break;
        case "organics":
            $ret = $l_organics;
            break;
        case "goods":
            $ret = $l_goods;
            break;
        case "special":
            $ret = $l_special;
            break;
    }

    return $ret;
}

function stripnum($str)
{
    $output = preg_replace('/[^0-9]/', '', $str);
    return $output;
}

function collect_bounty($attacker, $bounty_on)
{
    global $db, $l_by_thefeds;
    $res = db()->fetchAll("SELECT * FROM bounty,ships WHERE bounty_on = :bounty_on AND bounty_on = ship_id and placed_by <> 0", [
        'bounty_on' => $bounty_on,
    ]);

    if (!empty($res)) {
        foreach ($res as $bountydetails) {
            if ($bountydetails['placed_by'] == 0) {
                $placed = $l_by_thefeds;
            } else {
                $row = db()->fetch("SELECT * FROM ships WHERE ship_id = :ship_id", [
                    'ship_id' => $bountydetails['placed_by'],
                ]);
                $placed = $row['character_name'];
            }

            db()->q("UPDATE ships SET credits = credits + :amount WHERE ship_id = :attacker", [
                'amount' => $bountydetails['amount'],
                'attacker' => $attacker,
            ]);

            db()->q("DELETE FROM bounty WHERE bounty_id = :bounty_id", [
                'bounty_id' => $bountydetails['bounty_id'],
            ]);

            playerlog($attacker, LogTypeConstants::LOG_BOUNTY_CLAIMED, "$bountydetails[amount]|$bountydetails[character_name]|$placed");
            playerlog($bountydetails['placed_by'], LogTypeConstants::LOG_BOUNTY_PAID, "$bountydetails[amount]|$bountydetails[character_name]");
        }
    }

    db()->q("DELETE FROM bounty WHERE bounty_on = :bounty_on", [
        'bounty_on' => $bounty_on,
    ]);
}

function cancel_bounty($bounty_on)
{

    $res = db()->fetchAll("SELECT * FROM bounty,ships WHERE bounty_on = :bounty_on AND bounty_on = ship_id", [
        'bounty_on' => $bounty_on,
    ]);

    if (!empty($res)) {
        foreach ($res as $bountydetails) {
            if ($bountydetails['placed_by'] <> 0) {
                db()->q("UPDATE ships SET credits = credits + :amount WHERE ship_id = :ship_id", [
                    'amount' => $bountydetails['amount'],
                    'ship_id' => $bountydetails['placed_by'],
                ]);

                playerlog($bountydetails['placed_by'], LogTypeConstants::LOG_BOUNTY_CANCELLED, "$bountydetails[amount]|$bountydetails[character_name]");
            }

            db()->q("DELETE FROM bounty WHERE bounty_id = :bounty_id", [
                'bounty_id' => $bountydetails['bounty_id'],
            ]);
        }
    }
}

function get_player($ship_id)
{

    $row = db()->fetch("SELECT character_name from ships where ship_id = :ship_id", [
        'ship_id' => $ship_id,
    ]);
    if ($row) {
        $character_name = $row['character_name'];
        return $character_name;
    } else {
        return "Unknown";
    }
}

function log_move($ship_id, $sector_id)
{
    db()->q('INSERT INTO movement_log (ship_id,sector_id,time) VALUES (:ship_id,:sector_id,NOW())', [
        'ship_id' => $ship_id,
        'sector_id' => $sector_id,
    ]);
}

function isLoanPending($ship_id)
{

    global $IGB_lrate;

    $row = db()->fetch("SELECT loan, UNIX_TIMESTAMP(loantime) AS time FROM ibank_accounts WHERE ship_id=:ship_id", [
        'ship_id' => $ship_id,
    ]);

    if ($row) {
        $account = $row;

        if ($account['loan'] == 0) {
            return false;
        }

        $curtime = time();
        $difftime = ($curtime - $account['time']) / 60;
        if ($difftime > $IGB_lrate) {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

function sensorsCloakSuccess($sensors, $cloak)
{
    $success = SCAN_SUCCESS($sensors, $cloak);

    if ($success < 5) {
        $success = 5;
    }
    if ($success > 95) {
        $success = 95;
    }

    return $success;
}

function shipScore($ship)
{
    return array_sum([
        $ship['hull'],
        $ship['engines'],
        $ship['power'],
        $ship['computer'],
        $ship['sensors'],
        $ship['armor'],
        $ship['shields'],
        $ship['beams'],
        $ship['torp_launchers'],
        $ship['cloak']
    ]) / 10;
}

function planetLevel($score)
{
    if ($score < 8) :
        return 0;
    elseif ($score < 12) :
        return 1;
    elseif ($score < 16) :
        return 2;
    elseif ($score < 20) :
        return 3;
    else :
        return 4;
    endif;
}

function shipLevel($score)
{
    if (is_array($score)) {
        $score = shipScore($score);
    }

    if ($score < 8) :
        return 0;
    elseif ($score < 12) :
        return 1;
    elseif ($score < 16) :
        return 2;
    elseif ($score < 20) :
        return 3;
    else :
        return 4;
    endif;
}
