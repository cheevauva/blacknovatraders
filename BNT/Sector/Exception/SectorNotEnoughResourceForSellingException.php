<?php

declare(strict_types=1);

namespace BNT\Sector\Exception;

class SectorNotEnoughResourceForSellingException extends SectorException
{
    public $current;
    public $needle;
}
