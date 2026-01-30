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

    $stmt = db()->PrepareStmt("SELECT * FROM {$dbtables['ships']} WHERE email= :email");
    $stmt->InParameter($email, ':email');

    return $stmt->Execute()->fields;
}

function sqlCheckIpBan($ip, $playerIp)
{
    global $dbtables;

    $stmt = db()->PrepareStmt("SELECT * FROM {$dbtables['ip_bans']} WHERE :ip LIKE ban_mask OR :playerIp LIKE ban_mask");
    $stmt->InParameter($ip, ':ip');
    $stmt->InParameter($playerIp, ':playerIp');

    return $stmt->Execute()->RecordCount();
}

function sqlUpdateLogin($ship_id, $ip)
{
    global $dbtables;

    $stamp = date("Y-m-d H-i-s");
    $stmt = db()->PrepareStmt("UPDATE {$dbtables['ships']} SET last_login = :last_login, ip_address = :ip_address WHERE ship_id = :ship_id");
    $stmt->InParameter($stamp, ':last_login');
    $stmt->InParameter($ip, ':ip_address');
    $stmt->InParameter($ship_id, ':ship_id');

    return $stmt->Execute();
}

function sqlRestoreShipEscapepod($ship_id)
{
    global $dbtables;
    
    $sql = "
    UPDATE 
        {$dbtables['ships']} 
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
        {$dbtables['ships']}
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

    $stmt = db()->PrepareStmt("UPDATE {$dbtables['ships']} SET 
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
