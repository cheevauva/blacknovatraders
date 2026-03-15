<?php

declare(strict_types=1);

namespace BNT\Game\DAO;

class GameBiggerPlayersInRestrictedZonesDAO extends \UUA\DAO
{

    use \BNT\Traits\DatabaseMainTrait;
    use \BNT\Traits\UnitSimpleCallTrait;
    
    public protected(set) array $rows;

    #[\Override]
    public function serve(): void
    {
        $sql = "
        SELECT 
            ships.ship_id,
            ships.ship_name,
            ships.hull,
            ships.sector,
            universe.zone_id,
            zones.max_hull
        FROM 
            ships
        INNER JOIN
            universe
        ON
            universe.sector_id = ships.sector
        INNER JOIN
            zones
        ON
            zones.zone_id  = universe.zone_id AND
            zones.max_hull != 0 AND 
            (
                (
                    ships.hull + 
                    ships.engines + 
                    ships.computer + 
                    ships.beams + 
                    ships.torp_launchers + 
                    ships.shields + 
                    ships.armor
                ) / 7
            ) > zones.max_hull  
        WHERE
            ships.ship_destroyed = 'N'
        ";

        $this->rows = $this->db()->fetchAll($sql);
    }
}
