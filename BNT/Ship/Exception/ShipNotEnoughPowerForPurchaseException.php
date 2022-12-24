<?php

declare(strict_types=1);

namespace BNT\Ship\Exception;

class ShipNotEnoughPowerForPurchaseException extends ShipException
{

    public $current;
    public $needle;
}
