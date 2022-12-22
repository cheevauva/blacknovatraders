<?php

declare(strict_types=1);

namespace BNT\Ship\DAO;

use BNT\ServantInterface;
use BNT\DatabaseTrait;
use BNT\Ship\Mapper\ShipMapper;
use BNT\TableEnum;

abstract class ShipDAO implements ServantInterface
{

    use DatabaseTrait;

    protected function table(): string
    {
        return TableEnum::Ships->toDb();
    }

    protected function mapper(): ShipMapper
    {
        return new ShipMapper;
    }

}
