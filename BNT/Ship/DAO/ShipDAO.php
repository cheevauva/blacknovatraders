<?php

declare(strict_types=1);

namespace BNT\Ship\DAO;

use BNT\ServantInterface;
use BNT\DatabaseTrait;
use BNT\Ship\Mapper\ShipMapper;

abstract class ShipDAO implements ServantInterface
{

    use DatabaseTrait;

    abstract public function serve(): void;

    protected function table(): string
    {
        global $dbtables;

        return $dbtables['ships'];
    }

    protected function mapper(): ShipMapper
    {
        return new ShipMapper;
    }

}
