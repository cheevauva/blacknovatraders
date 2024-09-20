<?php

declare(strict_types=1);

namespace BNT\Ship\Exception;

use BNT\Ship\Ship;

class ShipHasBeenDestroyedException extends ShipException
{
    public Ship $ship;
}
