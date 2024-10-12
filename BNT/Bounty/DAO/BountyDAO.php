<?php

declare(strict_types=1);

namespace BNT\Bounty\DAO;

use BNT\ServantInterface;
use BNT\Traits\DatabaseTrait;
use BNT\Enum\TableEnum;
use BNT\Bounty\Mapper\BountyMapper;
use BNT\Traits\BuildTrait;

abstract class BountyDAO implements ServantInterface
{
    use DatabaseTrait;
    use BuildTrait;

    protected function table(): string
    {
        return TableEnum::Bounty->toDb();
    }

    protected function mapper(): BountyMapper
    {
        return new BountyMapper;
    }
}
