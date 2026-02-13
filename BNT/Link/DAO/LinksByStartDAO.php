<?php

declare(strict_types=1);

namespace BNT\Link\DAO;

class LinksByStartDAO extends \UUA\DAO
{

    use \BNT\Traits\DatabaseMainTrait;

    public $start;
    public $links;

    #[\Override]
    public function serve(): void
    {
        $this->links = $this->db()->fetchAll("SELECT * FROM links WHERE link_start= :sectorId ORDER BY link_dest ASC", [
            'sectorId' => $this->start,
        ]);
    }
}
