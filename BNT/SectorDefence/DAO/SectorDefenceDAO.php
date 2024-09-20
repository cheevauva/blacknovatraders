<?php

declare(strict_types=1);

namespace BNT\SectorDefence\DAO;

use BNT\ServantInterface;
use BNT\Traits\DatabaseTrait;
use BNT\SectorDefence\Mapper\SectorDefenceMapper;
use BNT\Enum\TableEnum;

abstract class SectorDefenceDAO implements ServantInterface
{
    use DatabaseTrait;

    protected function table(): string
    {
        return TableEnum::SectorDefences->toDb();
    }

    protected function mapper(): SectorDefenceMapper
    {
        return new SectorDefenceMapper;
    }
}
