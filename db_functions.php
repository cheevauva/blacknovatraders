<?php

/**
 * @return ADODB_pdo_mysql
 */
function db()
{
    global $db;

    return $db;
}

function ipBansCheck($ip)
{
    return db()->column("SELECT * FROM ip_bans WHERE :ip LIKE ban_mask", [
        'ip' => $ip,
    ]);
}

function shipSetToken($shipId, $token)
{
    db()->exec("UPDATE ships SET last_login = NOW(), token = :token WHERE ship_id = :ship_id", [
        'token' => $token,
        'ship_id' => $shipId,
    ]);
}

function planetById($id)
{
    return db()->fetch('SELECT * FROM planets WHERE planet_id = :id', [
        'id' => $id,
    ]);
}

function shipRestoreEscapepod($ship_id)
{
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

    db()->exec($sql, [
        'ship_id' => $ship_id,
    ]);
}

function shipCheckNewbie($ship_id)
{
    global $newbie_hull, $newbie_engines, $newbie_power, $newbie_computer, $newbie_sensors, $newbie_armor, $newbie_shields, $newbie_beams, $newbie_torp_launchers, $newbie_cloak;
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

    return db()->column($sql, [
        ':ship_id' => $ship_id,
        ':newbie_hull' => $newbie_hull,
        ':newbie_engines' => $newbie_engines,
        ':newbie_power' => $newbie_power,
        ':newbie_computer' => $newbie_computer,
        ':newbie_sensors' => $newbie_sensors,
        ':newbie_armor' => $newbie_armor,
        ':newbie_shields' => $newbie_shields,
        ':newbie_beams' => $newbie_beams,
        ':newbie_torp_launchers' => $newbie_torp_launchers,
        ':newbie_cloak' => $newbie_cloak,
    ]);
}

function shipRestoreNewbie($ship_id)
{
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
        WHERE ship_id = :ship_id
    ";

    db()->exec($sql, [
        'ship_id' => $ship_id,
    ]);
}

function mturnsMax()
{
    global $max_turns;

    $mturns = db()->column("SELECT MAX(turns_used + turns) AS mturns FROM ships");

    if ($mturns > $max_turns) {
        $mturns = $max_turns;
    }

    return $mturns;
}

function shipCreate($data)
{
    $columns = [];
    $parameters = [];
    $values = [];

    foreach ($data as $key => $value) {
        $columns[] =  $key;
        $values[] = sprintf(':%s', $key);
        $parameters[$key] = $value;
    }

    return db()->exec(sprintf('INSERT INTO ships (%s) VALUES (%s)', implode(', ', $columns), implode(', ', $values)), $parameters);
}



function zoneCreate($shipId, $zoneName)
{
    global $db;

    $sql = "
    INSERT INTO
        zones 
    VALUES(
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

    $db->exec($sql, [
        ':zone_name' => $zoneName,
        ':ship_id' => (int) $shipId,
        ':allow_attack' => 'N',
        ':allow_planetattack' => 'Y',
        ':allow_trade' => 'Y',
        ':allow_defenses' => 'Y',
        ':allow_shipyard' => 'Y',
        ':allow_build' => 'Y',
        ':allow_energy' => 'Y',
        ':allow_warpedit' => 'Y',
    ]);
}

function bankAccountCreate($shipId)
{
    db()->exec("INSERT INTO ibank_accounts (ship_id, balance, loan) VALUES(:ship_id, :balance, :loan)", [
        ':ship_id' => (int) $shipId,
        ':balance' => 0,
        ':loan' => 0,
    ]);
}

function shipsGetOnlinePlayersCount()
{
    return (int) db()->column("SELECT COUNT(*) as loggedin FROM ships WHERE (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(ships.last_login)) / 60 <= 5 AND email NOT LIKE '%@xenobe'");
}

function schedulerGetLastRun()
{
    return db()->column("SELECT last_run FROM scheduler LIMIT 1");
}

function newsByDate($date)
{
    return db()->fetchAll("SELECT * FROM news WHERE date = :date ORDER BY news_id DESC", [
        'date' => $date,
    ]);
}

function shipsGetNotDestroyedExcludeXenobeCount()
{
    return db()->column("SELECT COUNT(*) AS num_players FROM ships WHERE ship_destroyed='N' and email NOT LIKE '%@xenobe'");
}

function shipsGetRankingData($sort, $max_rank)
{
    if ($sort == "turns") {
        $by = "turns_used DESC,character_name ASC";
    } elseif ($sort == "login") {
        $by = "last_login DESC,character_name ASC";
    } elseif ($sort == "good") {
        $by = "rating DESC,character_name ASC";
    } elseif ($sort == "bad") {
        $by = "rating ASC,character_name ASC";
    } elseif ($sort == "alliance") {
        $by = "teams.team_name DESC, character_name ASC";
    } elseif ($sort == "efficiency") {
        $by = "efficiency DESC";
    } elseif ($sort == "online") {
        $by = "online DESC";
    } else {
        $by = "score DESC,character_name ASC";
    }

    $query = "
    SELECT 
        ships.email,
        ships.score,
        ships.character_name,
        ships.turns_used,
        ships.last_login,
        UNIX_TIMESTAMP(ships.last_login) as online,
        ships.rating, 
        teams.team_name, 
        IF(ships.turns_used<150,0,ROUND(ships.score/ships.turns_used)) AS efficiency 
    FROM 
        ships 
    LEFT JOIN 
        teams 
    ON 
        ships.team = teams.id  
    WHERE 
        ship_destroyed='N' AND 
        email NOT LIKE '%@xenobe' 
    ORDER BY $by 
    LIMIT :limit
    ";

    $stmt = db()->PrepareStmt($query);
    $stmt->bindParam('limit', $max_rank, PDO::PARAM_INT);

    return $stmt->fetchAll();
}

function messagesCountByShip($shipId)
{

    return db()->column("SELECT COUNT(*) FROM messages WHERE recp_id = :shipId AND notified = 'N'", [
        'shipId' => $shipId,
    ]);
}

function messagesNotifiedByShip($shipId)
{
    db()->exec("UPDATE messages SET notified = 'Y' WHERE recp_id = :shipId AND notified = 'N'", [
        'shipId' => $shipId,
    ]);
}

function shipByEmail($email)
{
    return db()->fetch("SELECT * FROM ships WHERE email = :username LIMIT 1", [
        'username' => $email,
    ]);
}

function shipById($id)
{
    return db()->fetch("SELECT * FROM ships WHERE ship_id = :id LIMIT 1", [
        'id' => $id,
    ]);
}

function shipByToken($token)
{
    return db()->fetch("SELECT * FROM ships WHERE token = :token LIMIT 1", [
        'token' => $token,
    ]);
}

function sectoryById($sectorId)
{
    return db()->fetch('SELECT * FROM universe WHERE sector_id = :sectorId LIMIT 1', [
        'sectorId' => $sectorId,
    ]);
}

function zoneById($zoneId)
{
    return db()->fetch('SELECT * FROM zones WHERE zone_id = :zoneId', [
        'zoneId' => $zoneId,
    ]);
}

function linksByStartAndDest($link_start, $link_dest)
{
    return db()->fetchAll("SELECT * FROM links WHERE link_start = :link_start AND link_dest = :link_dest", [
        'link_start' => $link_start,
        'link_dest' => $link_dest,
    ]);
}

function linksByStart($sectorId)
{
    return db()->fetchAll("SELECT * FROM links WHERE link_start= :sectorId ORDER BY link_dest ASC", [
        'sectorId' => $sectorId,
    ]);
}

function linksDeleteByStartAndDest($link_start, $link_dest)
{
    return db()->exec('DELETE FROM links WHERE link_start= :link_start AND link_dest= :link_dest', [
        'link_start' => $link_start,
        'link_dest' => $link_dest,
    ]);
}

function linkCreate($link_start, $link_dest)
{
    return db()->exec("INSERT INTO links SET link_start= :link_start, link_dest= :link_dest", [
        'link_start' => $link_start,
        'link_dest' => $link_dest,
    ]);
}

function planetsBySector($sectorId)
{
    $sql = "
    SELECT 
        p.*,
        (owner.hull + owner.engines + owner.computer + owner.beams + owner.torp_launchers + owner.shields + owner.armor) / 7 AS owner_score,
        owner.character_name AS owner_character_name
    FROM 
        planets AS p
    LEFT JOIN
        ships AS owner
    ON
        p.owner = owner.ship_id
    WHERE 
        p.sector_id = :sectorId
    ";

    return db()->fetchAll($sql, [
        'sectorId' => $sectorId,
    ]);
}

function defencesBySector($sectorId)
{
    $sql = "
    SELECT 
        sector_defence.*,
        ships.character_name
    FROM
        sector_defence,
        ships
    WHERE 
        sector_defence.sector_id = :sectorId AND 
        ships.ship_id = sector_defence.ship_id 
    ";

    return db()->fetchAll($sql, [
        'sectorId' => $sectorId,
    ]);
}

function getShipsInSector($sectorId, $playerShipId)
{
    if (empty($sectorId)) {
        return [];
    }

    $sql = " 
    SELECT
        ships.*,
        (ships.hull + ships.engines + ships.power + ships.computer + ships.sensors + ships.armor + ships.shields + ships.beams + ships.torp_launchers + ships.cloak) / 10 AS score,
        teams.team_name,
        teams.id
    FROM 
        ships
    LEFT JOIN
        teams
    ON 
        ships.team = teams.id
    WHERE 
        ships.ship_id != :playerShipId AND
        ships.sector = :sector AND
        ships.on_planet = 'N'
    ";

    return db()->fetchAll($sql, [
        'sector' => $sectorId,
        'playerShipId' => $playerShipId,
    ]);
}

function traderoutesBySectorAndShip($sector, $shipId)
{
    $sql = "
    SELECT
        traderoutes.*,
        planet_src.name AS planet_source,
        planet_dst.name AS planet_dest
    FROM
        (
            SELECT traderoutes.* FROM traderoutes WHERE source_type = 'P' AND source_id = :sector AND owner = :shipId 
            UNION
            SELECT traderoutes.* FROM traderoutes WHERE source_type = 'D' AND source_id = :sector AND owner = :shipId 
            UNION
            SELECT traderoutes.* FROM planets, traderoutes WHERE traderoutes.source_type = 'L' AND traderoutes.source_id = planets.planet_id AND planets.sector_id = :sector AND traderoutes.owner = :shipId
            UNION
            SELECT traderoutes.* FROM planets, traderoutes WHERE traderoutes.source_type = 'C' AND traderoutes.source_id = planets.planet_id AND planets.sector_id = :sector AND traderoutes.owner = :shipId
        ) AS traderoutes
    LEFT JOIN
        planets AS planet_src
    ON
        planet_src.planet_id = traderoutes.source_id
    LEFT JOIN
        planets AS planet_dst
    ON
        planet_dst.planet_id = traderoutes.dest_id
    ";

    return db()->fetchAll($sql, [
        'sector' => $sector,
        'shipId' => $shipId,
    ]);
}

function shipResetClearedDefences($shipId)
{
    db()->exec("UPDATE ships SET last_login = NOW(), cleared_defences = '' WHERE ship_id= :shipId", [
        'shipId' => $shipId,
    ]);
}

function shipSetClearedDefences($shipId, $cleared_defences)
{
    db()->exec("UPDATE ships SET last_login = NOW(), cleared_defences = :cleared_defences WHERE ship_id= :shipId", [
        'shipId' => $shipId,
        'cleared_defences' => $cleared_defences,
    ]);
}

function shipMoveToSector($shipId, $sector)
{
    db()->exec("UPDATE ships SET last_login = NOW(), turns=turns - 1, turns_used = turns_used + 1, sector=:sector WHERE ship_id = :shipId", [
        'sector' => $sector,
        'shipId' => $shipId,
    ]);
}

function shipCreditsAdd($shipId, $credits)
{
    db()->exec("UPDATE ships SET last_login = NOW(), credits = credits + :credits WHERE ship_id = :shipId", [
        'credits' => $credits,
        'shipId' => $shipId,
    ]);
}

function shipCreditsSub($shipId, $credits)
{
    db()->exec("UPDATE ships SET last_login = NOW(), credits = credits - :credits WHERE ship_id = :shipId", [
        'credits' => $credits,
        'shipId' => $shipId,
    ]);
}

function shipToSector($shipId, $sector)
{
    db()->exec("UPDATE ships SET last_login=NOW(), sector=:sector WHERE ship_id = :shipId", [
        'sector' => $sector,
        'shipId' => $shipId,
    ]);
}

function shipRetreatToSector($shipId, $sector)
{
    db()->exec("UPDATE ships SET last_login=NOW(), turns=turns - 2, turns_used = turns_used + 2, sector=:sector where ship_id = :shipId", [
        'sector' => $sector,
        'shipId' => $shipId,
    ]);
}

function defencesBySectorAndFighters($sectorId)
{
    return db()->fetchAll("SELECT * FROM sector_defence WHERE sector_id=:sectorId and defence_type ='F' ORDER BY quantity DESC", [
        'sectorId' => $sectorId,
    ]);
}

function defencesCleanUp()
{
    return db()->exec("delete from sector_defence where quantity <= 0 ");
}

function logsByShipAndDate($ship, $date)
{
    return db()->fetchAll('SELECT * FROM logs WHERE ship_id = :ship AND time LIKE :date ORDER BY time DESC, type DESC', [
        'ship' => $ship,
        'date' => $date . '%',
    ]);
}

function teamById($team)
{
    return db()->fetchAll('SELECT team_name, creator, id FROM teams WHERE id=:team LIMIT 1', [
        'team' => $team,
    ]);
}
function zoneUpdate($zone, $data)
{
    $parameters = [];
    $values = [];

    foreach ($data as $key => $value) {
        $values[] = sprintf('%s = :%s', $key, $key);
        $parameters[$key] = $value;
    }

    $parameters['zone_id'] = $zone;

    return db()->exec(sprintf('UPDATE zones SET %s WHERE zone_id = :zone_id', implode(', ', $values)), $parameters);
}

function planetUpdate($planet, $data)
{
    $parameters = [];
    $values = [];

    foreach ($data as $key => $value) {
        $values[] = sprintf('%s = :%s', $key, $key);
        $parameters[$key] = $value;
    }

    $parameters['planet_id'] = $planet;

    return db()->exec(sprintf('UPDATE planets SET %s WHERE planet_id = :planet_id', implode(', ', $values)), $parameters);
}

function shipUpdate($ship, $data)
{
    $parameters = [];
    $values = [];

    foreach ($data as $key => $value) {
        $values[] = sprintf('%s = :%s', $key, $key);
        $parameters[$key] = $value;
    }

    $parameters['ship_id'] = $ship;

    return db()->exec(sprintf('UPDATE ships SET %s WHERE ship_id = :ship_id', implode(', ', $values)), $parameters);
}

function sectorUpdate($sector, $data)
{
    $parameters = [];
    $values = [];

    foreach ($data as $key => $value) {
        $values[] = sprintf('%s = :%s', $key, $key);
        $parameters[$key] = $value;
    }

    $parameters['sector_id'] = $sector;

    return db()->exec(sprintf('UPDATE universe SET %s WHERE sector_id = :sector_id', implode(', ', $values)), $parameters);
}

function shipDevWarpeditSub($ship, $devWarpedit)
{
    return db()->exec('UPDATE ships SET dev_warpedit = dev_warpedit - :dev_warpedit WHERE ship_id= :ship', [
        'ship' => $ship,
        'dev_warpedit' => $devWarpedit,
    ]);
}

function shipDevBeaconSub($ship, $beacon)
{
    return db()->exec('UPDATE ships SET dev_beacon = dev_beacon - :beacon WHERE ship_id= :ship', [
        'ship' => $ship,
        'beacon' => $beacon,
    ]);
}

function sectorUpdateBeacon($sector, $beaconText)
{
    return db()->exec('UPDATE universe SET beacon = :beaconText WHERE sector_id= :sector', [
        'sector' => $sector,
        'beaconText' => $beaconText,
    ]);
}

function shipTurn($shipId, $turns)
{
    return db()->exec('UPDATE ships SET last_login = NOW(), turns = turns - :turns, turns_used = turns_used + :turns WHERE ship_id = :shipId', [
        'turns' => $turns,
        'shipId' => $shipId,
    ]);
}
