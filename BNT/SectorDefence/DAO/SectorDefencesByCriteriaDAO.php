<?php

declare(strict_types=1);

namespace BNT\SectorDefence\DAO;

class SectorDefencesByCriteriaDAO extends \UUA\DAO
{

    use \BNT\Traits\DatabaseRowsSelectByCriteriaTrait;

    #[\Override]
    public function serve(): void
    {
        $this->selectRows('sector_defence');
    }
}
