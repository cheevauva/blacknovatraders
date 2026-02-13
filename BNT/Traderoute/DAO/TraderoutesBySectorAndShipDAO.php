<?php

declare(strict_types=1);

namespace BNT\Traderoute\DAO;

class TraderoutesBySectorAndShipDAO extends \UUA\DAO
{
    use \BNT\Traits\DatabaseMainTrait;

    public $sector;
    public $ship;
    public $traderoutes;

    public function serve(): void
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

        $this->traderoutes = $this->db()->fetchAll($sql, [
            'sector_id' => $this->sector,
            'ship_id' => $this->sector,
        ]);
    }
}
