<?php

declare(strict_types=1);

namespace BNT\Planet\DAO;

use BNT\ServantInterface;
use BNT\DatabaseTrait;

abstract class PlanetDAO implements ServantInterface
{

    use DatabaseTrait;

    protected function table(): string
    {
        global $dbtables;

        return $dbtables['planets'];
    }

}
