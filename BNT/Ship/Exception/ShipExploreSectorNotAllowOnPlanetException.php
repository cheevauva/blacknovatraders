<?php

declare(strict_types=1);

namespace BNT\Ship\Exception;

class ShipExploreSectorNotAllowOnPlanetException extends \Exception
{

    public function __construct(public int $planet)
    {
        parent::__construct();
    }
}
