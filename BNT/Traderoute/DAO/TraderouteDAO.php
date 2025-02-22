<?php

declare(strict_types=1);

namespace BNT\Traderoute\DAO;

use BNT\DAO;

use BNT\Enum\TableEnum;
use BNT\Traderoute\Mapper\TraderouteMapper;

abstract class TraderouteDAO extends DAO
{
    

    protected function table(): string
    {
        return TableEnum::Traderoutes->toDb();
    }

    protected function mapper(): TraderouteMapper
    {
        return new TraderouteMapper;
    }
}
