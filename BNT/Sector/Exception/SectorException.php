<?php

declare(strict_types=1);

namespace BNT\Sector\Exception;

use BNT\Sector\Enum\SectorPortTypeEnum;

class SectorException extends \Exception
{

    public static function notEnoughCreditsForPurchase($current, $needle): SectorNotEnoughCreditsForPurchaseException
    {
        $ex = new SectorNotEnoughCreditsForPurchaseException("Sector do not have enough credits for this transaction. ");        //@todo
        $ex->current = $current;
        $ex->needle = $needle;

        return $ex;
    }

    public static function notEnoughResourceForSelling(SectorPortTypeEnum $resource, $current, $needle): SectorNotEnoughResourceForSellingException
    {
        global $l_exceed_energy;
        global $l_exceed_ore;
        global $l_exceed_organics;
        global $l_exceed_goods;

        $ex = new SectorNotEnoughResourceForSellingException(match ($resource) {
            SectorPortTypeEnum::Energy => $l_exceed_energy,
            SectorPortTypeEnum::Ore => $l_exceed_ore,
            SectorPortTypeEnum::Organics => $l_exceed_organics,
            SectorPortTypeEnum::Goods => $l_exceed_goods,
        });
        $ex->current = $current;
        $ex->needle = $needle;

        return $ex;
    }

    public static function notEnoughCargoForPurchase($freeCargo, $cargo): SectorException
    {
        global $l_notenough_cargo;
        //@todo
        $ex = new SectorException($l_notenough_cargo);

        return $ex;
    }

    public static function notEnoughPowerForPurchase($freePower, $power): SectorException
    {
        global $l_notenough_power;
        //@todo
        $ex = new SectorException($l_notenough_power);

        return $ex;
    }
}
