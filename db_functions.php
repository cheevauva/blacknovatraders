<?php

/**
 * @return ADODB_pdo_mysql
 */
function db()
{
    global $db;

    return $db;
}

function sqlGetPlayerByEmail($email)
{
    global $dbtables;

    $stmt = db()->PrepareStmt("SELECT * FROM ships WHERE email= :email");
    $stmt->InParameter($email, ':email');

    return $stmt->Execute()->fields;
}

function sqlCheckIpBan($ip)
{
    global $dbtables;

    $stmt = db()->PrepareStmt("SELECT * FROM {$dbtables['ip_bans']} WHERE :ip LIKE ban_mask");
    $stmt->InParameter($ip, ':ip');

    return $stmt->Execute()->RecordCount();
}

function sqlUpdateLogin($ship_id, $token)
{
    global $dbtables;

    $stamp = date("Y-m-d H-i-s");
    $stmt = db()->PrepareStmt("UPDATE ships SET last_login = :last_login, token = :token WHERE ship_id = :ship_id");
    $stmt->InParameter($stamp, ':last_login');
    $stmt->InParameter($token, ':token');
    $stmt->InParameter($ship_id, ':ship_id');

    return $stmt->Execute();
}

function sqlRestoreShipEscapepod($ship_id)
{
    global $dbtables;

    $sql = "
    UPDATE 
        ships 
    SET 
        hull = 0,
        engines = 0,
        power = 0,
        computer = 0,
        sensors = 0,
        beams = 0,
        torp_launchers = 0,
        torps = 0,
        armor = 0,
        armor_pts = 100,
        cloak = 0,
        shields = 0,
        sector = 0,
        ship_ore = 0,
        ship_organics = 0,
        ship_energy = 1000,
        ship_colonists = 0,
        ship_goods = 0,
        ship_fighters = 100,
        ship_damage = 0,
        on_planet = 'N',
        dev_warpedit = 0,
        dev_genesis = 0,
        dev_beacon = 0,
        dev_emerwarp = 0,
        dev_escapepod = 'N',
        dev_fuelscoop = 'N',
        dev_minedeflector = 0,
        ship_destroyed = 'N',
        dev_lssd = 'N' 
        WHERE ship_id = :ship_id
    ";
    $stmt = db()->PrepareStmt($sql);

    $stmt->InParameter($ship_id, ':ship_id');

    return $stmt->Execute();
}

function sqlCheckNewbieShip($ship_id, $newbie_hull, $newbie_engines, $newbie_power, $newbie_computer, $newbie_sensors, $newbie_armor, $newbie_shields, $newbie_beams, $newbie_torp_launchers, $newbie_cloak)
{
    global $dbtables;

    $sql = "
    SELECT 
        COUNT(id)
    FROM 
        ships
    WHERE 
        ship_id = :ship_id
        AND hull <= :newbie_hull
        AND engines <= :newbie_engines
        AND power <= :newbie_power
        AND computer <= :newbie_computer
        AND sensors <= :newbie_sensors
        AND armor <= :newbie_armor
        AND shields <= :newbie_shields
        AND beams <= :newbie_beams
        AND torp_launchers <= :newbie_torp_launchers
        AND cloak <= :newbie_cloak
    ";

    $stmt = db()->PrepareStmt($sql);
    $stmt->InParameter($ship_id, ':ship_id');
    $stmt->InParameter($newbie_hull, ':newbie_hull');
    $stmt->InParameter($newbie_engines, ':newbie_engines');
    $stmt->InParameter($newbie_power, ':newbie_power');
    $stmt->InParameter($newbie_computer, ':newbie_computer');
    $stmt->InParameter($newbie_sensors, ':newbie_sensors');
    $stmt->InParameter($newbie_armor, ':newbie_armor');
    $stmt->InParameter($newbie_shields, ':newbie_shields');
    $stmt->InParameter($newbie_beams, ':newbie_beams');
    $stmt->InParameter($newbie_torp_launchers, ':newbie_torp_launchers');
    $stmt->InParameter($newbie_cloak, ':newbie_cloak');

    return $stmt->Execute()->RecordCount();
}

function sqlRestoreNewbieShip($ship_id)
{
    global $dbtables;

    $stmt = db()->PrepareStmt("UPDATE ships SET 
        hull = 0,
        engines = 0,
        power = 0,
        computer = 0,
        sensors = 0,
        beams = 0,
        torp_launchers = 0,
        torps = 0,
        armor = 0,
        armor_pts = 100,
        cloak = 0,
        shields = 0,
        sector = 0,
        ship_ore = 0,
        ship_organics = 0,
        ship_energy = 1000,
        ship_colonists = 0,
        ship_goods = 0,
        ship_fighters = 100,
        ship_damage = 0,
        credits = 1000,
        on_planet = 'N',
        dev_warpedit = 0,
        dev_genesis = 0,
        dev_beacon = 0,
        dev_emerwarp = 0,
        dev_escapepod = 'N',
        dev_fuelscoop = 'N',
        dev_minedeflector = 0,
        ship_destroyed = 'N',
        dev_lssd = 'N' 
        WHERE ship_id = :ship_id");

    $stmt->InParameter($ship_id, ':ship_id');

    return $stmt->Execute();
}

/**
 * Получить максимальное количество ходов среди всех игроков
 * @return int Максимальное количество ходов
 */
function sqlGetMaxTurns()
{
    global $db, $max_turns;
    $query = $db->Execute("SELECT MAX(turns_used + turns) AS mturns FROM ships");
    $res = $query->fields;
    $mturns = $res['mturns'];

    if ($mturns > $max_turns) {
        $mturns = $max_turns;
    }

    return $mturns;
}

function sqlCreatePlayer($playerData)
{
    global $db, $default_lang;

    $sql = "
    INSERT INTO ships (
        ship_name, ship_destroyed, character_name, password, email, 
        armor_pts, credits, ship_energy, ship_fighters, turns, 
        on_planet, dev_warpedit, dev_genesis, dev_beacon, dev_emerwarp, 
        dev_escapepod, dev_fuelscoop, dev_minedeflector, last_login, 
        interface, token, trade_colonists, trade_fighters, trade_torps, 
        trade_energy, cleared_defences, lang, dhtml, dev_lssd
    ) VALUES (
        :ship_name, :ship_destroyed, :character_name, :password, :email,
        :armor_pts, :credits, :ship_energy, :ship_fighters, :turns,
        :on_planet, :dev_warpedit, :dev_genesis, :dev_beacon, :dev_emerwarp,
        :dev_escapepod, :dev_fuelscoop, :dev_minedeflector, :last_login,
        :interface, :token, :trade_colonists, :trade_fighters, :trade_torps,
        :trade_energy, :cleared_defences, :lang, :dhtml, :dev_lssd
    )
    ";

    $stmt = db()->PrepareStmt($sql);
    $stmt->InParameter($playerData['ship_name'], ':ship_name');
    $stmt->InParameter($playerData['ship_destroyed'], ':ship_destroyed');
    $stmt->InParameter($playerData['character_name'], ':character_name');
    $stmt->InParameter($playerData['password'], ':password');
    $stmt->InParameter($playerData['email'], ':email');
    $stmt->InParameter($playerData['on_planet'], ':on_planet');
    $stmt->InParameter($playerData['dev_escapepod'], ':dev_escapepod');
    $stmt->InParameter($playerData['dev_fuelscoop'], ':dev_fuelscoop');
    $stmt->InParameter($playerData['last_login'], ':last_login');
    $stmt->InParameter($playerData['interface'], ':interface');
    $stmt->InParameter($playerData['token'], ':token');
    $stmt->InParameter($playerData['trade_colonists'], ':trade_colonists');
    $stmt->InParameter($playerData['trade_fighters'], ':trade_fighters');
    $stmt->InParameter($playerData['trade_torps'], ':trade_torps');
    $stmt->InParameter($playerData['trade_energy'], ':trade_energy');
    $stmt->InParameter($playerData['cleared_defences'], ':cleared_defences');
    $stmt->InParameter($playerData['lang'], ':lang');
    $stmt->InParameter($playerData['dhtml'], ':dhtml');
    $stmt->InParameter($playerData['armor_pts'], ':armor_pts');
    $stmt->InParameter($playerData['credits'], ':credits');
    $stmt->InParameter($playerData['ship_energy'], ':ship_energy');
    $stmt->InParameter($playerData['ship_fighters'], ':ship_fighters');
    $stmt->InParameter($playerData['turns'], ':turns');
    $stmt->InParameter($playerData['dev_warpedit'], ':dev_warpedit');
    $stmt->InParameter($playerData['dev_genesis'], ':dev_genesis');
    $stmt->InParameter($playerData['dev_beacon'], ':dev_beacon');
    $stmt->InParameter($playerData['dev_emerwarp'], ':dev_emerwarp');
    $stmt->InParameter($playerData['dev_minedeflector'], ':dev_minedeflector');
    $stmt->InParameter($playerData['dev_lssd'], ':dev_lssd');

    return $stmt->Execute();
}

function sqlCreateZone($shipId, $zoneName)
{
    global $db;

    $sql = "
    INSERT INTO zones VALUES(
        NULL, 
        :zone_name, 
        :ship_id, 
        :allow_attack, 
        :allow_planetattack, 
        :allow_trade, 
        :allow_defenses, 
        :allow_shipyard, 
        :allow_build, 
        :allow_energy, 
        :allow_warpedit, 
        0
    )
    ";

    $stmt = $db->PrepareStmt($sql);
    $stmt->InParameter($zoneName, ':zone_name');
    $stmt->InParameter((int) $shipId, ':ship_id');
    $stmt->InParameter('N', ':allow_attack');
    $stmt->InParameter('Y', ':allow_planetattack');
    $stmt->InParameter('Y', ':allow_trade');
    $stmt->InParameter('Y', ':allow_defenses');
    $stmt->InParameter('Y', ':allow_shipyard');
    $stmt->InParameter('Y', ':allow_build');
    $stmt->InParameter('Y', ':allow_energy');
    $stmt->InParameter('Y', ':allow_warpedit');

    return $stmt->Execute();
}

function sqlCreateBankAccount($shipId)
{
    global $db;

    $stmt = $db->PrepareStmt("INSERT INTO ibank_accounts (ship_id, balance, loan) VALUES(:ship_id, :balance, :loan)");
    $stmt->InParameter((int) $shipId, ':ship_id');
    $stmt->InParameter(0, ':balance');
    $stmt->InParameter(0, ':loan');

    return $stmt->Execute();
}

function sqlGetOnlinePlayersCount()
{
    return (int) db()->Execute("SELECT COUNT(*) as loggedin FROM ships WHERE (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(ships.last_login)) / 60 <= 5 AND email NOT LIKE '%@xenobe'")->fields['loggedin'];
}

function sqlGetSchedulerLastRun()
{
    return db()->Execute("SELECT last_run FROM scheduler LIMIT 1")->fields;
}

function sqlGetNewsByDate($date)
{
    $stmt = db()->PrepareStmt("SELECT * FROM news WHERE date = ? ORDER BY news_id DESC");
    $stmt->InParameter($date, 'date');

    $res = $stmt->Execute();
    
    if (!$res) {
        return [];
    }
    
    $rows = [];

    while (!$res->EOF) {
        $rows[] = $res->fields;
        
        $res->MoveNext();
    }
    
    return $rows;
}
