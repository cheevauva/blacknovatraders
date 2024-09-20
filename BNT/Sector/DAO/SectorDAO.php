<?php

declare(strict_types=1);

namespace BNT\Sector\DAO;

use BNT\ServantInterface;
use BNT\Traits\DatabaseTrait;
use BNT\Enum\TableEnum;
use BNT\Sector\Mapper\SectorMapper;

abstract class SectorDAO implements ServantInterface
{
    use DatabaseTrait;

    protected function table(): string
    {
        return TableEnum::Sectors->toDb();
    }

    protected function mapper(): SectorMapper
    {
        return new SectorMapper;
    }
}
