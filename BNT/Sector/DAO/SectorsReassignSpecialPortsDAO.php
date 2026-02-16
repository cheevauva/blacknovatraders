<?php

declare(strict_types=1);

namespace BNT\Sector\DAO;

class SectorsReassignSpecialPortsDAO extends \UUA\DAO
{
    use \BNT\Traits\DatabaseMainTrait;

    public int $zone;
    public int $limit;

    #[\Override]
    public function serve(): void
    {
        $sql = "
        UPDATE 
            universe 
        SET 
            zone_id = :zone,
            port_type = 'special' 
        WHERE 
            port_type = 'none' AND 
            sector_id IN (
                SELECT 
                    s.sector_id 
                FROM 
                    (
                        SELECT 
                            sector_id 
                        FROM 
                            universe 
                        WHERE 
                            port_type = 'none' 
                        ORDER BY 
                            rand() DESC 
                        LIMIT :limit
                    ) AS s
            )
        ";

        $this->db()->q($sql, [
            'zone' => $this->zone,
            'limit' => (int) $this->limit,
        ], [
            'limit' => \PDO::PARAM_INT,
        ]);
    }
}
