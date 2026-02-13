<?php

declare(strict_types=1);

namespace BNT\Zone\DAO;

class ZonesSetMaxHullDAO extends \UUA\DAO
{
    use \BNT\Traits\DatabaseMainTrait;

    public int $fedMaxHull;
    public int $zone;

    #[\Override]
    public function serve(): void
    {
        $this->db()->q("UPDATE zones SET max_hull = :fed_max_hull WHERE zone_id = :zone_id", [
            'fed_max_hull' => $this->fedMaxHull,
            'zone_id' => $this->zone,
        ]);
    }
}
