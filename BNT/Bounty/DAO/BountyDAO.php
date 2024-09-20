<?php

declare(strict_types=1);

namespace BNT\Bounty\DAO;

use BNT\ServantInterface;
use BNT\Traits\DatabaseTrait;
use BNT\Enum\TableEnum;
use BNT\Bounty\Mapper\BountyMapper;

abstract class BountyDAO implements ServantInterface
{
    use DatabaseTrait;

    protected function table(): string
    {
        return TableEnum::Bounty->toDb();
    }

    protected function mapper(): BountyMapper
    {
        return new BountyMapper;
    }
}
