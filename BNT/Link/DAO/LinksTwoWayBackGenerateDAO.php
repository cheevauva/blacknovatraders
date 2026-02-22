<?php

declare(strict_types=1);

namespace BNT\Link\DAO;

class LinksTwoWayBackGenerateDAO extends \UUA\DAO
{
    use \BNT\Traits\DatabaseMainTrait;
    use \BNT\Traits\UnitSimpleCallTrait;

    #[\Override]
    public function serve(): void
    {
        $sql = "
        INSERT INTO links (link_start, link_dest, link_type)
        SELECT
            links.link_dest AS link_start,
            links.link_start AS link_dest,
            links.link_type
        FROM
            links
        WHERE
            links.link_type = 2
        ";

        $this->db()->q($sql);
    }
}
