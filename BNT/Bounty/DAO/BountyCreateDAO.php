<?php

declare(strict_types=1);

namespace BNT\Bounty\DAO;

class BountyCreateDAO extends \UUA\DAO
{

    use \BNT\Traits\DatabaseRowCreateTrait;
    
    #[\Override]
    public function serve(): void
    {
        $this->rowCreate('bounty');
    }
}
