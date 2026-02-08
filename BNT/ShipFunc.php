<?php

//declare(strict_types=1);

namespace BNT;

use PDO;

class ShipFunc
{

    public static function shipDevWarpeditSub($ship, $devWarpedit)
    {
        return db()->q('UPDATE ships SET dev_warpedit = dev_warpedit - :dev_warpedit WHERE ship_id= :ship', [
            'ship' => $ship,
            'dev_warpedit' => $devWarpedit,
        ]);
    }

    public static function shipDevBeaconSub($ship, $beacon)
    {
        return db()->q('UPDATE ships SET dev_beacon = dev_beacon - :beacon WHERE ship_id= :ship', [
            'ship' => $ship,
            'beacon' => $beacon,
        ]);
    }

    public static function shipTurn($shipId, $turns)
    {
        return db()->q('UPDATE ships SET last_login = NOW(), turns = turns - :turns, turns_used = turns_used + :turns WHERE ship_id = :shipId', [
            'turns' => $turns,
            'shipId' => $shipId,
        ]);
    }

    public static function shipUpdate($ship, $data)
    {
        $parameters = [];
        $values = [];

        foreach ($data as $key => $value) {
            $values[] = sprintf('%s = :%s', $key, $key);
            $parameters[$key] = $value;
        }

        $parameters['ship_id'] = $ship;

        return db()->q(sprintf('UPDATE ships SET %s WHERE ship_id = :ship_id', implode(', ', $values)), $parameters);
    }

    public static function shipCreditsAdd($shipId, $credits)
    {
        db()->q("UPDATE ships SET last_login = NOW(), credits = credits + :credits WHERE ship_id = :shipId", [
            'credits' => $credits,
            'shipId' => $shipId,
        ]);
    }

    public static function shipCreditsSub($shipId, $credits)
    {
        db()->q("UPDATE ships SET last_login = NOW(), credits = credits - :credits WHERE ship_id = :shipId", [
            'credits' => $credits,
            'shipId' => $shipId,
        ]);
    }

    public static function shipToSector($shipId, $sector)
    {
        db()->q("UPDATE ships SET last_login=NOW(), sector=:sector WHERE ship_id = :shipId", [
            'sector' => $sector,
            'shipId' => $shipId,
        ]);
    }

    public static function shipRetreatToSector($shipId, $sector)
    {
        db()->q("UPDATE ships SET last_login=NOW(), turns=turns - 2, turns_used = turns_used + 2, sector=:sector where ship_id = :shipId", [
            'sector' => $sector,
            'shipId' => $shipId,
        ]);
    }

    public static function shipResetClearedDefences($shipId)
    {
        db()->q("UPDATE ships SET last_login = NOW(), cleared_defences = '' WHERE ship_id= :shipId", [
            'shipId' => $shipId,
        ]);
    }

    public static function shipSetClearedDefences($shipId, $cleared_defences)
    {
        db()->q("UPDATE ships SET last_login = NOW(), cleared_defences = :cleared_defences WHERE ship_id= :shipId", [
            'shipId' => $shipId,
            'cleared_defences' => $cleared_defences,
        ]);
    }

    public static function shipMoveToSector($shipId, $sector)
    {
        db()->q("UPDATE ships SET last_login = NOW(), turns=turns - 1, turns_used = turns_used + 1, sector=:sector WHERE ship_id = :shipId", [
            'sector' => $sector,
            'shipId' => $shipId,
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

    public static function shipByEmail($email)
    {
        return db()->fetch("SELECT * FROM ships WHERE email = :username LIMIT 1", [
            'username' => $email,
        ]);
    }

    public static function shipById($id)
    {
        global $container;
        
        $shipById = \BNT\Ship\DAO\ShipByIdDAO::_new($container);
        $shipById->id = $id;
        $shipById->serve();

        return $shipById->ship;
    }

    public static function shipByToken($token)
    {
        global $container;
    
        $shipByToken = \BNT\Ship\DAO\ShipByTokenDAO::_new($container);
        $shipByToken->token = $token;
        $shipByToken->serve();

        return $shipByToken->ship;
    }

    public static function shipsGetNotDestroyedExcludeXenobeCount()
    {
        return db()->column("SELECT COUNT(*) AS num_players FROM ships WHERE ship_destroyed='N' and email NOT LIKE '%@xenobe'");
    }

    public static function shipsGetRankingData($sort, $max_rank)
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

        return db()->fetchAll($query, [
            'limit' => $max_rank,
        ], [
            'limit' => PDO::PARAM_INT,
        ]);
    }

    public static function shipCheckNewbie($ship_id)
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

    public static function shipRestoreNewbie($ship_id)
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

        db()->q($sql, [
            'ship_id' => $ship_id,
        ]);
    }

    public static function shipSetToken($shipId, $token)
    {
        db()->q("UPDATE ships SET last_login = NOW(), token = :token WHERE ship_id = :ship_id", [
            'token' => $token,
            'ship_id' => $shipId,
        ]);
    }

    public static function shipCreate($data)
    {
        return rowCreate('ships', $data);
    }

    public static function shipsGetOnlinePlayersCount()
    {
        return (int) db()->column("SELECT COUNT(*) as loggedin FROM ships WHERE (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(ships.last_login)) / 60 <= 5 AND email NOT LIKE '%@xenobe'");
    }
}
