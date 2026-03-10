<?php

declare(strict_types=1);

namespace BNT\Game\DAO;

class GameLongRangeScanDAO extends \UUA\DAO
{

    use \BNT\Traits\DatabaseMainTrait;

    public int $sector;
    public int $ship;
    public array $links;

    #[\Override]
    public function serve(): void
    {
        $sql = "
        SELECT 
            *,
            (SELECT COUNT(*) AS count FROM links AS links2 WHERE links2.link_start = links.link_dest) AS num_links,
            (SELECT COUNT(*) AS count FROM ships WHERE sector = links.link_dest AND on_planet = 'N' and ship_destroyed = 'N') AS num_ships,
            (SELECT port_type FROM universe WHERE sector_id = links.link_dest) AS port_type,
            (SELECT IF(planet_id, 1, 0) FROM planets WHERE sector_id = links.link_dest LIMIT 1) AS has_planet,
            (SELECT SUM(quantity) FROM sector_defence WHERE sector_id = links.link_dest and defence_type = 'M') AS has_mines,
            (SELECT SUM(quantity)FROM sector_defence WHERE sector_id = links.link_dest and defence_type = 'F') AS has_fighters,
            (SELECT s.ship_name FROM movement_log AS ml INNER JOIN ships AS s ON s.ship_id = ml.ship_id WHERE ml.ship_id != :ship_id AND ml.sector_id = links.link_dest ORDER BY time DESC LIMIT 1) AS lssd_ship_name
        FROM 
            links 
        WHERE 
            link_start = :sector 
        ORDER BY 
            link_dest
        ";

        $this->links = $this->db()->fetchAll($sql, [
            'ship_id' => $this->ship,
            'sector' => $this->sector,
        ]);
    }
}
