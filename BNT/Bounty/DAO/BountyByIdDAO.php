<?php

declare(strict_types=1);

namespace BNT\Bounty\DAO;

class BountyByIdDAO extends \UUA\DAO
{

    use \BNT\Traits\DatabaseRowSelectByIdTrait;

    public ?array $bounty;
    
    #[\Override]
    public function serve(): void
    {
        $this->bounty = $this->selectRow('bounty', 'bounty_id');
    }
}
