<?php

declare(strict_types=1);

namespace BNT\Bounty\DAO;

class BountiesByCriteriaDAO extends \UUA\DAO
{

    use \BNT\Traits\DatabaseRowsSelectByCriteriaTrait;
    
    public array $bounties;

    #[\Override]
    public function serve(): void
    {
        $this->bounties = $this->selectRows('bounty');
    }
}
