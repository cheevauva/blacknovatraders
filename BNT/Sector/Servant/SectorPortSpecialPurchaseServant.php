<?php

declare(strict_types=1);

namespace BNT\Sector\Servant;

use BNT\ServantInterface;
use BNT\Sector\Exception\SectorException;
use BNT\Ship\DAO\ShipSaveDAO;

class SectorPortSpecialPurchaseServant implements ServantInterface
{

    public SectorPortSpecialOfferServant $offer;

    public function serve(): void
    {
        $offer = $this->offer;
        $offer->serve();

        $ship = $offer->ship;

        if ($offer->total_cost > $ship->credits) {
            throw SectorException::notEnoughCreditsForPurchase($ship->credits, $offer->total_cost);
        }
        
        $ship->hull = max($offer->hull_upgrade, $ship->hull);
        $ship->engines = max($offer->engine_upgrade, $ship->engines);
        $ship->power = max($offer->power_upgrade, $ship->power);
        $ship->computer = max($offer->computer_upgrade, $ship->computer);
        $ship->sensors = max($offer->sensors_upgrade, $ship->sensors);
        $ship->beams = max($offer->beams_upgrade, $ship->beams);
        $ship->armor = max($offer->armor_upgrade, $ship->armor);
        $ship->cloak = max($offer->cloak_upgrade, $ship->cloak);
        $ship->torp_launchers = max($offer->torp_launchers_upgrade, $ship->torp_launchers);
        $ship->shields = max($offer->shields_upgrade, $ship->shields);
        //
        $ship->ship_fighters = $ship->ship_fighters + $offer->fighter_number;
        $ship->torps = $ship->torps + $offer->torpedo_number;
        $ship->armor_pts = $ship->armor_pts + $offer->armor_number;
        $ship->ship_colonists = $ship->ship_colonists + $offer->colonist_number;
        $ship->dev_genesis = $ship->dev_genesis + $offer->dev_genesis_number;
        $ship->dev_beacon = $ship->dev_beacon + $offer->dev_beacon_number;
        $ship->dev_emerwarp = $ship->dev_emerwarp + $offer->dev_emerwarp_number;
        $ship->dev_warpedit = $ship->dev_warpedit + $offer->dev_warpedit_number;
        //
        $ship->dev_minedeflector = $ship->dev_minedeflector + $offer->dev_minedeflector_number;
        $ship->dev_escapepod = $ship->dev_escapepod ?: !empty($offer->escapepod_purchase);
        $ship->dev_fuelscoop = $ship->dev_fuelscoop ?: !empty($offer->fuelscoop_purchase);
        $ship->dev_lssd = $ship->dev_lssd ?: !empty($offer->lssd_purchase);
        //
        $ship->credits = $ship->credits - $offer->total_cost;
        $ship->turns--;
        $ship->turns_used++;

        ShipSaveDAO::call($ship);
    }

    public static function call(SectorPortSpecialOfferServant $offer): self
    {
        $self = new static;
        $self->offer = $offer;
        $self->serve();

        return $self;
    }

}
