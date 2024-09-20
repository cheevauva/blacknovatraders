<?php

declare(strict_types=1);

namespace BNT\Ship\Exception;

use BNT\Ship\Entity\Ship;

class ShipHasBeenDestroyedException extends ShipException
{
    public Ship $ship;
}
