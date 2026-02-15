<?php

declare(strict_types=1);

namespace BNT\Sector\DAO;

class SectorGenerateDAO extends \UUA\DAO
{

    use \BNT\Traits\DatabaseMainTrait;

    public int $sectorMax;
    public int $universe_size;
    public int $zone;

    #[\Override]
    public function serve(): void
    {
        $sql = "
        INSERT INTO universe (sector_id, zone_id, angle1, angle2, distance) 
        SELECT 
            null,
            :zone_id,
            FLOOR(RAND() * 180) AS angle1,
            FLOOR(RAND() * 90) AS angle2,
            ROUND(RAND() * :universe_size) AS distance
        FROM 
            information_schema.tables t1
        CROSS JOIN 
            information_schema.tables t2
        CROSS JOIN 
            information_schema.tables t3
        LIMIT :sector_max
        ";

        $this->db()->q($sql, [
            'zone_id' => $this->zone,
            'sector_max' => $this->sectorMax,
            'universe_size' => $this->universe_size,
        ], [
            'sector_max' => \PDO::PARAM_INT,
        ]);
    }
}
