<?php

declare(strict_types=1);

namespace BNT\Traderoute\DAO;

use BNT\ServantInterface;
use BNT\DatabaseTrait;
use BNT\TableEnum;

abstract class TraderouteDAO implements ServantInterface
{

    use DatabaseTrait;

    protected function table(): string
    {
        return TableEnum::Traderoutes->toDb();
    }

}
