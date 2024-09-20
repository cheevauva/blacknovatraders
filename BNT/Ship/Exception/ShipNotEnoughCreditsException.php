<?php

declare(strict_types=1);

namespace BNT\Ship\Exception;

class ShipNotEnoughCreditsException extends ShipException
{
    public $credits;
    public $cost;
}
