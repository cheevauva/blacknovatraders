<?php

declare(strict_types=1);

namespace BNT\SectorDefence\DAO;

class SectorDefencesByCriteriaDAO extends \UUA\DAO
{

    use \BNT\Traits\DatabaseRowsSelectByCriteriaTrait;

    public array $defences;

    #[\Override]
    public function serve(): void
    {
        $this->defences = $this->selectRows('sector_defence');
    }
}
