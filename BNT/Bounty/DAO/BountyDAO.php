<?php

declare(strict_types=1);

namespace BNT\Bounty\DAO;

use BNT\DAO;

use BNT\Enum\TableEnum;
use BNT\Bounty\Mapper\BountyMapper;

abstract class BountyDAO extends DAO
{
    

    protected function table(): string
    {
        return TableEnum::Bounty->toDb();
    }

    protected function mapper(): BountyMapper
    {
        return new BountyMapper;
    }
}
