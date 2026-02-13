<?php

declare(strict_types=1);

namespace BNT;

use PDO;

class ShipFunc
{
    public static function shipDevWarpeditSub($ship, $devWarpedit)
    {
        return db()->q('UPDATE ships SET dev_warpedit = dev_warpedit - :dev_warpedit WHERE ship_id= :ship', [
            'ship' => $ship,
            'dev_warpedit' => $devWarpedit,
        ]);
    }

    public static function shipDevBeaconSub($ship, $beacon)
    {
        return db()->q('UPDATE ships SET dev_beacon = dev_beacon - :beacon WHERE ship_id= :ship', [
            'ship' => $ship,
            'beacon' => $beacon,
        ]);
    }

    public static function shipTurn($shipId, $turns)
    {
        return db()->q('UPDATE ships SET last_login = NOW(), turns = turns - :turns, turns_used = turns_used + :turns WHERE ship_id = :shipId', [
            'turns' => $turns,
            'shipId' => $shipId,
        ]);
    }

    public static function shipCreditsAdd($shipId, $credits)
    {
        db()->q("UPDATE ships SET last_login = NOW(), credits = credits + :credits WHERE ship_id = :shipId", [
            'credits' => $credits,
            'shipId' => $shipId,
        ]);
    }

    public static function shipCreditsSub($shipId, $credits)
    {
        db()->q("UPDATE ships SET last_login = NOW(), credits = credits - :credits WHERE ship_id = :shipId", [
            'credits' => $credits,
            'shipId' => $shipId,
        ]);
    }

    public static function shipToSector($shipId, $sector)
    {
        db()->q("UPDATE ships SET last_login=NOW(), sector=:sector WHERE ship_id = :shipId", [
            'sector' => $sector,
            'shipId' => $shipId,
        ]);
    }

    public static function shipRetreatToSector($shipId, $sector)
    {
        db()->q("UPDATE ships SET last_login=NOW(), turns=turns - 2, turns_used = turns_used + 2, sector=:sector where ship_id = :shipId", [
            'sector' => $sector,
            'shipId' => $shipId,
        ]);
    }

    public static function shipResetClearedDefences($shipId)
    {
        db()->q("UPDATE ships SET last_login = NOW(), cleared_defences = '' WHERE ship_id= :shipId", [
            'shipId' => $shipId,
        ]);
    }

    public static function shipSetClearedDefences($shipId, $cleared_defences)
    {
        db()->q("UPDATE ships SET last_login = NOW(), cleared_defences = :cleared_defences WHERE ship_id= :shipId", [
            'shipId' => $shipId,
            'cleared_defences' => $cleared_defences,
        ]);
    }

    public static function shipMoveToSector($shipId, $sector)
    {
        db()->q("UPDATE ships SET last_login = NOW(), turns=turns - 1, turns_used = turns_used + 1, sector=:sector WHERE ship_id = :shipId", [
            'sector' => $sector,
            'shipId' => $shipId,
        ]);
    }
}
