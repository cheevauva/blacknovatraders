<?php

declare(strict_types=1);

namespace BNT\Ship\Exception;

use BNT\Ship\Entity\Ship;

class ShipPasswordIncorrectException extends ShipException
{
    public Ship $ship;
}
