<?php

declare(strict_types=1);

namespace BNT\Planet\DAO;

use BNT\ServantInterface;
use BNT\DatabaseTrait;
use BNT\TableEnum;
use BNT\Planet\Mapper\PlanetMapper;

abstract class PlanetDAO implements ServantInterface
{

    use DatabaseTrait;

    protected function table(): string
    {
        return TableEnum::Planets->toDb();
    }

    protected function mapper(): PlanetMapper
    {
        return new PlanetMapper;
    }

}
