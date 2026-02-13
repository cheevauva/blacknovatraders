<?php

declare(strict_types=1);

namespace BNT\Zone\DAO;

class ZoneByIdDAO extends \UUA\DAO
{
    use \BNT\Traits\DatabaseMainTrait;

    public int $id;
    public ?array $zone;

    #[\Override]
    public function serve(): void
    {
        $this->zone = $this->db()->fetch('SELECT * FROM zones WHERE zone_id = :zoneId', [
            'zoneId' => $this->id,
        ]);
    }
}
