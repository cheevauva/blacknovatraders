<?php

declare(strict_types=1);

namespace BNT\Traderoute\DAO;

use BNT\ServantInterface;
use BNT\DatabaseTrait;
use BNT\TableEnum;
use BNT\Traderoute\Mapper\TraderouteMapper;

abstract class TraderouteDAO implements ServantInterface
{

    use DatabaseTrait;

    protected function table(): string
    {
        return TableEnum::Traderoutes->toDb();
    }

    protected function mapper(): TraderouteMapper
    {
        return new TraderouteMapper;
    }

}
