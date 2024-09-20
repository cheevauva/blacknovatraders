<?php

declare(strict_types=1);

namespace BNT\Ship\Exception;

use BNT\Ship\Entity\Ship;
use BNT\Ship\Enum\ShipResourceEnum;

class ShipException extends \Exception
{

    public static function notEnoughPowerForPurchase($current, $needle): ShipNotEnoughPowerForPurchaseException
    {
        global $l_notenough_power;

        $ex = new ShipNotEnoughPowerForPurchaseException($l_notenough_power);
        $ex->current = $current;
        $ex->needle = $needle;

        return $ex;
    }

    public static function notEnoughResourceForSelling(ShipResourceEnum $resource, $current, $needle): ShipNotEnoughResourceForSellingException
    {
        global $l_notenough_energy;
        global $l_notenough_ore;
        global $l_notenough_organics;
        global $l_notenough_goods;

        $ex = new ShipNotEnoughResourceForSellingException(match ($resource) {
            ShipResourceEnum::Energy => $l_notenough_energy,
            ShipResourceEnum::Ore => $l_notenough_ore,
            ShipResourceEnum::Organics => $l_notenough_organics,
            ShipResourceEnum::Goods => $l_notenough_goods,
        });
        $ex->current = $current;
        $ex->needle = $needle;

        return $ex;
    }

    public static function notEnoughCredits($credits, $cost): ShipNotEnoughCreditsException
    {
        $ex = new ShipNotEnoughCreditsException;
        $ex->credits = $credits;
        $ex->cost = $cost;

        return $ex;
    }

    public static function notAllowTurn(): ShipNotAllowTurnException
    {
        return new ShipNotAllowTurnException;
    }

    public static function notFound(): ShipNotFoundException
    {
        global $l_login_noone;

        return new ShipNotFoundException($l_login_noone);
    }

    public static function incorrectPassword(Ship $ship): ShipPasswordIncorrectException
    {
        global $l_login_4gotpw1;

        $ex = new ShipPasswordIncorrectException($l_login_4gotpw1);
        $ex->ship = $ship;

        return $ex;
    }

    public static function hasBeenDestroyed(Ship $ship): ShipHasBeenDestroyedException
    {
        global $l_login_died;

        $ex = new ShipHasBeenDestroyedException($l_login_died);
        $ex->ship = $ship;

        return $ex;
    }
}
