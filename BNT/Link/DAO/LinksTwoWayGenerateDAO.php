<?php

declare(strict_types=1);

namespace BNT\Link\DAO;

class LinksTwoWayGenerateDAO extends \UUA\DAO
{

    use \BNT\Traits\DatabaseMainTrait;

    public $sectorMax;

    #[\Override]
    public function serve(): void
    {
        $sql = "
        INSERT INTO links (link_start, link_dest, link_type)
        SELECT 
            @k := @i AS link_start,
            @i := @i + 1 AS link_dest,
            2 AS link_type
        FROM 
            (SELECT @i := 0) AS r,
            information_schema.tables t1
        CROSS JOIN 
            information_schema.tables t2
        CROSS JOIN 
            information_schema.tables t3
        LIMIT :sector_max
        ";

        $this->db()->q($sql, [
            'sector_max' => (int) $this->sectorMax,
        ], [
            'sector_max' => \PDO::PARAM_INT,
        ]);
    }
}
