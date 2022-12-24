<?php

declare(strict_types=1);

namespace BNT\Sector\Exception;

class SectorNotEnoughCreditsForPurchaseException extends SectorException
{

    public $credits;
    public $cost;

}
