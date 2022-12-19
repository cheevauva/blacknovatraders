<?php

declare(strict_types=1);

namespace BNT\Ship\Exception;

class ShipHasBeenDestroyedException extends ShipException
{

    public Ship $ship;

}
