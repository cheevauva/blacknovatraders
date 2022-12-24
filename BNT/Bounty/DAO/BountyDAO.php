<?php

declare(strict_types=1);

namespace BNT\Bounty\DAO;

use BNT\ServantInterface;
use BNT\DatabaseTrait;
use BNT\TableEnum;

abstract class BountyDAO implements ServantInterface
{

    use DatabaseTrait;

    protected function table(): string
    {
        return TableEnum::Bounty->toDb();
    }

}
