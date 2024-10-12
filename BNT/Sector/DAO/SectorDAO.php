<?php

declare(strict_types=1);

namespace BNT\Sector\DAO;

use BNT\ServantInterface;
use BNT\Traits\DatabaseTrait;
use BNT\Enum\TableEnum;
use BNT\Sector\Mapper\SectorMapper;
use BNT\Traits\BuildTrait;

abstract class SectorDAO implements ServantInterface
{
    use DatabaseTrait;
    use BuildTrait;

    protected function table(): string
    {
        return TableEnum::Sectors->toDb();
    }

    protected function mapper(): SectorMapper
    {
        return new SectorMapper;
    }
}
