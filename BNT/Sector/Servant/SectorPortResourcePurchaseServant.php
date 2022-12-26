<?php

declare(strict_types=1);

namespace BNT\Sector\Servant;

use BNT\ServantInterface;
use BNT\Sector\DAO\SectorSaveDAO;
use BNT\Sector\SectorPortTypeEnum;
use BNT\Sector\Servant\SectorPortResourceOfferServant;
use BNT\Sector\Exception\SectorException;
use BNT\Sector\Sector;
use BNT\Ship\Ship;
use BNT\Ship\DAO\ShipSaveDAO;

class SectorPortResourcePurchaseServant implements ServantInterface
{

    public Ship $ship;
    public SectorPortResourceOfferServant $offer;

    public function serve(): void
    {
        $offer = $this->offer;
        $offer->serve();

        $sector = $offer->sector;
        $ship = $this->ship;

        $offer->total_cost < 0 ? $ship->acceptPayment(abs($offer->total_cost)) : $ship->payment($offer->total_cost);
        $offer->total_cost < 0 ? $sector->payment(abs($offer->total_cost)) : $sector->acceptPayment($offer->total_cost);

        $ship->rating += 1;

        $this->trade($sector, $ship, SectorPortTypeEnum::Ore, $offer->trade_ore);
        $this->trade($sector, $ship, SectorPortTypeEnum::Organics, $offer->trade_organics);
        $this->trade($sector, $ship, SectorPortTypeEnum::Goods, $offer->trade_goods);
        $this->trade($sector, $ship, SectorPortTypeEnum::Energy, $offer->trade_energy);

        echo '<pre>';
        print_r($offer);
        print_r($ship);
        die;

//        ShipSaveDAO::call($ship);
//        SectorSaveDAO::call($sector);
    }

    private function trade(Sector $sector, Ship $ship, SectorPortTypeEnum $resource, $amount): void
    {
        $amount < 0 ? $ship->sell($resource->toShipResource(), abs($amount)) : $ship->buy($resource->toShipResource(), abs($amount));
        $amount < 0 ? $sector->buy($resource, abs($amount)) : $sector->sell($resource, abs($amount));
    }

    private function validateOpportunity(SectorPortResourceOfferServant $offer, Ship $ship): void
    {



        $freePower = $ship->getFreePower();
        $freeHolds = $ship->getFreeHolds();

        if ($freeHolds < $offer->cargo_exchanged) {
            throw SectorException::notEnoughCargoForPurchase($freeHolds, $offer->cargo_exchanged);
        }

        if ($offer->trade_energy > $freePower) {
            throw SectorException::notEnoughPowerForPurchase($freePower, $offer->trade_energy);
        }
    }

    public static function call(SectorPortResourceOfferServant $offer, Ship $ship): self
    {
        $self = new static;
        $self->offer = $offer;
        $self->ship = $ship;
        $self->serve();

        return $self;
    }

}
