<?php

declare(strict_types=1);

namespace BNT\SectorDefence\DAO;

class SectorDefencesUpdateDAO extends \UUA\DAO
{

    use \BNT\Traits\DatabaseRowsUpdateByCriteriaTrait;

    #[\Override]
    public function serve(): void
    {
        $this->updateRows('sector_defence');
    }
}
