<?php

declare(strict_types=1);

namespace BNT\Link\DAO;

class LinksOneWayGenerateDAO extends \UUA\DAO
{
    use \BNT\Traits\DatabaseMainTrait;

    public int $limit;

    #[\Override]
    public function serve(): void
    {
        $sql = "
        INSERT INTO links (link_start, link_dest, link_type)
        SELECT 
            ROUND(RAND() * :limit + 1) - 1 AS link_start,
            ROUND(RAND() * :limit + 1) - 1 AS link_dest,
            1 AS link_type
        FROM 
            information_schema.tables t1
        CROSS JOIN 
            information_schema.tables t2
        CROSS JOIN 
            information_schema.tables t3
        LIMIT :limit
        ";

        $this->db()->q($sql, [
            'limit' => (int) $this->limit,
        ], [
            'limit' => \PDO::PARAM_INT,
        ]);
    }
}
