<?php

declare(strict_types=1);

namespace BNT\Bounty\DAO;

class BountyDeleteByCriteriaDAO extends \UUA\DAO
{

    use \BNT\Traits\DatabaseRowsDeleteByCriteria;

    #[\Override]
    public function serve(): void
    {
        $this->deleteRows('bounty');
    }
}
