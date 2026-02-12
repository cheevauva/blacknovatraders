<?php

declare(strict_types=1);

namespace BNT\Planet\DAO;

class PlanetsBySectorDAO extends \UUA\DAO
{

    use \BNT\Traits\DatabaseMainTrait;

    public $sector;
    public $planets;

    public function serve(): void
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

        $this->planets = $this->db()->fetchAll($sql, [
            'sectorId' => $this->sector,
        ]);
    }
}
