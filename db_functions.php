<?php

function ipBansCheck($ip)
{
    return db()->column("SELECT * FROM ip_bans WHERE :ip LIKE ban_mask", [
        'ip' => $ip,
    ]);
}

function planetById($id)
{
    return db()->fetch('SELECT * FROM planets WHERE planet_id = :id', [
        'id' => $id,
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

function schedulerCreate($data)
{
    return rowCreate('scheduler', $data);
}

function rowCreate($table, $data)
{
    $parameters = [];
    $sets = [];

    foreach ($data as $key => $value) {
        $sets[] = sprintf('%s = :%s', $key, $key);
        $parameters[$key] = $value;
    }

    db()->q(sprintf('INSERT INTO %s SET %s', $table, implode(', ', $sets)), $parameters);

    return db()->lastInsertId();
}

function configRead()
{
    return db()->fetchAllKeyValue('SELECT name, value FROM config');
}

function configUpdate($data)
{
    foreach ($data as $name => $value) {
        db()->q('REPLACE INTO config SET value = :value , name = :name', [
            'name' => $name,
            'value' => $value,
        ]);
    }
}

function zoneCreate($data)
{
    return rowCreate('zones', $data);
}

function bankAccountCreate($data)
{
    return rowCreate('ibank_accounts', $data);
}

function sectorUpdateBeacon($sector, $beaconText)
{
    return db()->q('UPDATE universe SET beacon = :beaconText WHERE sector_id= :sector', [
        'sector' => $sector,
        'beaconText' => $beaconText,
    ]);
}


function newsByDate($date)
{
    return db()->fetchAll("SELECT * FROM news WHERE date = :date ORDER BY news_id DESC", [
        'date' => $date,
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
    return db()->q('DELETE FROM links WHERE link_start= :link_start AND link_dest= :link_dest', [
        'link_start' => $link_start,
        'link_dest' => $link_dest,
    ]);
}

function linkCreate($link_start, $link_dest)
{
    return db()->q("INSERT INTO links SET link_start= :link_start, link_dest= :link_dest", [
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

function traderoutesBySectorAndShip($sector, $shipId)
{
    $sql = "
    SELECT
        traderoutes.*,
        planet_src.name AS planet_source,
        planet_dst.name AS planet_dest
    FROM
        (
            SELECT traderoutes.* FROM traderoutes WHERE source_type = 'P' AND source_id = :sector_id AND owner = :ship_id 
            UNION
            SELECT traderoutes.* FROM traderoutes WHERE source_type = 'D' AND source_id = :sector_id AND owner = :ship_id 
            UNION
            SELECT traderoutes.* FROM planets, traderoutes WHERE traderoutes.source_type = 'L' AND traderoutes.source_id = planets.planet_id AND planets.sector_id = :sector_id AND traderoutes.owner = :ship_id
            UNION
            SELECT traderoutes.* FROM planets, traderoutes WHERE traderoutes.source_type = 'C' AND traderoutes.source_id = planets.planet_id AND planets.sector_id = :sector_id AND traderoutes.owner = :ship_id
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
        'sector_id' => $sector,
        'ship_id' => $shipId,
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
    return db()->q("delete from sector_defence where quantity <= 0 ");
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

    return db()->q(sprintf('UPDATE zones SET %s WHERE zone_id = :zone_id', implode(', ', $values)), $parameters);
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

    return db()->q(sprintf('UPDATE planets SET %s WHERE planet_id = :planet_id', implode(', ', $values)), $parameters);
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

    return db()->q(sprintf('UPDATE universe SET %s WHERE sector_id = :sector_id', implode(', ', $values)), $parameters);
}

function sectorCreate($data)
{
    $columns = [];
    $parameters = [];
    $values = [];

    foreach ($data as $key => $value) {
        $columns[] = $key;
        $values[] = sprintf(':%s', $key);
        $parameters[$key] = $value;
    }

    return db()->q(sprintf('INSERT INTO universe (%s) VALUES (%s)', implode(', ', $columns), implode(', ', $values)), $parameters);
}
