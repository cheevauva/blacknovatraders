<?php

declare(strict_types=1);

namespace BNT\Sector\DAO;

class SectorsAssignZoneDAO extends \UUA\DAO
{
    use \BNT\Traits\DatabaseMainTrait;

    public int $zone;
    public int $lessThanSector;

    #[\Override]
    public function serve(): void
    {
        $this->db()->q("UPDATE universe SET zone_id = :zone WHERE sector_id < :sector", [
            'zone' => $this->zone,
            'sector' => $this->lessThanSector,
        ]);
    }
}
