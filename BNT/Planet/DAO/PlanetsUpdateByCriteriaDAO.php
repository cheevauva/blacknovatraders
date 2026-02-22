<?php

declare(strict_types=1);

namespace BNT\Planet\DAO;

class PlanetsUpdateByCriteriaDAO extends \UUA\DAO
{

    use \BNT\Traits\DatabaseRowsUpdateByCriteriaTrait;

    #[\Override]
    public function serve(): void
    {
        $this->updateRows('planets');
    }
}
