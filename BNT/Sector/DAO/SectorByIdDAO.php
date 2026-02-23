<?php

declare(strict_types=1);

namespace BNT\Sector\DAO;

class SectorByIdDAO extends \UUA\DAO
{

    use \BNT\Traits\DatabaseRowSelectByIdTrait;

    /**
     * @var array<string, mixed>|null
     */
    public ?array $sector;

    #[\Override]
    public function serve(): void
    {
        $this->sector = $this->selectRow('universe', 'sector_id');
    }
}
