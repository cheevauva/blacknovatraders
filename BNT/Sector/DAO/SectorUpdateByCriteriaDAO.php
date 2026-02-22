<?php

declare(strict_types=1);

namespace BNT\Sector\DAO;

class SectorUpdateByCriteriaDAO extends \UUA\DAO
{

    use \BNT\Traits\DatabaseRowsUpdateByCriteriaTrait;

    #[\Override]
    public function serve(): void
    {
        $this->updateRows('universe');
    }
}
