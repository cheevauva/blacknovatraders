<?php

declare(strict_types=1);

namespace BNT\Sector\Servant;

use BNT\ServantInterface;
use BNT\Sector\Entity\Sector;
use BNT\Sector\Enum\SectorPortTypeEnum;
use BNT\Ship\Entity\Ship;

class SectorPortResourcePreOfferServant implements ServantInterface
{
    use \BNT\Traits\AsTrait;

    public Sector $sector;
    public Ship $ship;
    public $ore_price;
    public $ore_amount;
    public $organics_price;
    public $organics_amount;
    public $goods_price;
    public $goods_amount;
    public $energy_price;
    public $energy_amount;

    public function serve(): void
    {
        $sector = $this->sector;

        $this->ore_price = $sector->priceByPortType(SectorPortTypeEnum::Ore);
        $this->organics_price = $sector->priceByPortType(SectorPortTypeEnum::Organics);
        $this->goods_price = $sector->priceByPortType(SectorPortTypeEnum::Goods);
        $this->energy_price = $sector->priceByPortType(SectorPortTypeEnum::Energy);

        // establish default amounts for each commodity
        $this->ore_amount = $this->getFreeResourceForTrade(SectorPortTypeEnum::Ore);
        $this->organics_amount = $this->getFreeResourceForTrade(SectorPortTypeEnum::Organics);
        $this->goods_amount = $this->getFreeResourceForTrade(SectorPortTypeEnum::Goods);
        $this->energy_amount = $this->getFreeResourceForTrade(SectorPortTypeEnum::Energy);

        // limit amounts to port quantities
        $this->limitResourceForTrade(SectorPortTypeEnum::Ore);
        $this->limitResourceForTrade(SectorPortTypeEnum::Organics);
        $this->limitResourceForTrade(SectorPortTypeEnum::Goods);
        $this->limitResourceForTrade(SectorPortTypeEnum::Energy);
    }

    private function getFreeResourceForTrade(SectorPortTypeEnum $portType)
    {
        $sector = $this->sector;
        $ship = $this->ship;

        if ($portType->is($sector->port_type)) {
            $amount = $ship->getFreeResourceForSelling($portType->toShipResource());
        } else {
            $amount = $ship->getFreeResourceForBuying($portType->toShipResource());
        }

        return min($amount, $sector->amount($portType));
    }

    private function limitResourceForTrade(SectorPortTypeEnum $portType)
    {
        $sector = $this->sector;
        $ship = $this->ship;

        if (!$portType->is($sector->port_type)) {
            return;
        }

        $cost = 0;
        $cost += $this->getCostByPortType(SectorPortTypeEnum::Ore);
        $cost += $this->getCostByPortType(SectorPortTypeEnum::Organics);
        $cost += $this->getCostByPortType(SectorPortTypeEnum::Goods);
        $cost += $this->getCostByPortType(SectorPortTypeEnum::Energy);
        $cost -= $this->getCostByPortType($portType);

        if ($portType->is($sector->port_type)) {
            $amount = round(($ship->credits + $cost) / $sector->priceByPortType($portType));
        }

        match ($portType) {
            SectorPortTypeEnum::Energy => $this->energy_amount = min($this->energy_amount, $amount),
            SectorPortTypeEnum::Ore => $this->ore_amount = min($this->ore_amount, $amount),
            SectorPortTypeEnum::Organics => $this->organics_amount = min($this->organics_amount, $amount),
            SectorPortTypeEnum::Goods => $this->goods_amount = min($this->goods_amount, $amount),
        };
    }

    private function getCostByPortType(SectorPortTypeEnum $portType)
    {
        return match ($portType) {
            SectorPortTypeEnum::Energy => $this->energy_amount * $this->energy_price,
            SectorPortTypeEnum::Ore => $this->ore_amount * $this->ore_price,
            SectorPortTypeEnum::Organics => $this->organics_amount * $this->organics_price,
            SectorPortTypeEnum::Goods => $this->goods_amount * $this->goods_price,
        };
    }

    public static function call(Sector $sector, Ship $ship): self
    {
        $self = new static();
        $self->sector = $sector;
        $self->ship = $ship;
        $self->serve();

        return $self;
    }
}
