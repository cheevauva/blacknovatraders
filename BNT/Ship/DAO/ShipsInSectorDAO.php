<?php

//declare(strict_types=1);

namespace BNT\Ship\DAO;

class ShipsInSectorDAO extends \UUA\DAO
{

    use \BNT\Traits\DatabaseMainTrait;

    public $sector;
    public $excludeShip;
    public $ships;

    public function serve()
    {
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
            ships.ship_id != :excludeShip AND
            ships.sector = :sector AND
            ships.on_planet = 'N'
        ";

        $this->ships = db()->fetchAll($sql, [
            'sector' => $this->sector,
            'excludeShip' => $this->excludeShip,
        ]);
    }
}
