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
        
        $ship->pay($offer->total_cost);
        $sector->pay(-$offer->total_cost);

        $ship->rating += 1;

        $this->trade($sector, $ship, SectorPortTypeEnum::Ore, $offer->trade_ore);
        $this->trade($sector, $ship, SectorPortTypeEnum::Organics, $offer->trade_organics);
        $this->trade($sector, $ship, SectorPortTypeEnum::Goods, $offer->trade_goods);
        $this->trade($sector, $ship, SectorPortTypeEnum::Energy, $offer->trade_energy);
        
        var_dump($ship->credits);
        var_dump($sector->credits);
        var_dump($ship->getFreePower());
        die;
//
//        ShipSaveDAO::call($ship);
//        SectorSaveDAO::call($sector);
    }

    private function trade(Sector $sector, Ship $ship, SectorPortTypeEnum $resource, $amount): void
    {
        $ship->trade($resource->toShipResource(), $amount);
        $sector->trade($resource, -$amount);
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
