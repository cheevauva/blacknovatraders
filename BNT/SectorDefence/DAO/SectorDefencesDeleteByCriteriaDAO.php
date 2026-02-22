<?php

declare(strict_types=1);

namespace BNT\SectorDefence\DAO;

class SectorDefencesDeleteByCriteriaDAO extends \UUA\DAO
{

    use \BNT\Traits\DatabaseRowsDeleteByCriteria;

    #[\Override]
    public function serve(): void
    {
        $this->deleteRows('sector_defence');
    }
}
