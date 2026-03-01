<?php

declare(strict_types=1);

namespace BNT\SectorDefence\DAO;

class SectorDefenceByIdDAO extends \UUA\DAO
{

    use \BNT\Traits\DatabaseRowSelectByIdTrait;

    public ?array $defence;

    #[\Override]
    public function serve(): void
    {
        $this->defence = $this->selectRow('sector_defence', 'defence_id');
    }
}
