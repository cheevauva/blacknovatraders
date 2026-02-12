<?php

declare(strict_types=1);

namespace BNT\Sector\DAO;

class SectorByIdDAO extends \UUA\DAO
{

    use \BNT\Traits\DatabaseMainTrait;

    public int $id;
    public ?array $sector;

    #[\Override]
    public function serve(): void
    {
        $this->sector = $this->db()->fetch('SELECT * FROM universe WHERE sector_id = :sectorId LIMIT 1', [
            'sectorId' => $this->id,
        ]);
    }
}
