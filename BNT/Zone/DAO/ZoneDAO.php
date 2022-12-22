<?php

declare(strict_types=1);

namespace BNT\Zone\DAO;

use BNT\ServantInterface;
use BNT\DatabaseTrait;
use BNT\TableEnum;
use BNT\Zone\Mapper\ZoneMapper;

abstract class ZoneDAO implements ServantInterface
{

    use DatabaseTrait;

    protected function table(): string
    {
        return TableEnum::Zones->toDb();
    }

    protected function mapper(): ZoneMapper
    {
        return new ZoneMapper;
    }

}
