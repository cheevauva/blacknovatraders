<?php

declare(strict_types=1);

namespace BNT\Ship\Exception;

class ShipNotEnoughResourceForSellingException extends ShipException
{

    public $current;
    public $needle;

}
