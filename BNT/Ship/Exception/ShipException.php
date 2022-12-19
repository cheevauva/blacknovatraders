<?php

declare(strict_types=1);

namespace BNT\Ship\Exception;

use BNT\Ship\Ship;

class ShipException extends \Exception
{

    public static function notFound(): ShipNotFoundException
    {
        return new ShipNotFoundException;
    }

    public static function incorrectPassword(Ship $ship): ShipPasswordIncorrectException
    {
        $ex = new ShipPasswordIncorrectException;
        $ex->ship = $ship;

        return $ex;
    }
    
        public static function hasBeenDestroyed(Ship $ship): ShipHasBeenDestroyedException
    {
        $ex = new ShipHasBeenDestroyedException;
        $ex->ship = $ship;

        return $ex;
    }

}
