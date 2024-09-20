<?php

declare(strict_types=1);

namespace BNT\Sector;

use BNT\Sector\Exception\SectorException;
use BNT\Sector\SectorPortTypeEnum;
use BNT\Enum\BalanceEnum;

class Sector
{
    public int $sector_id;
    public string $sector_name;
    public int $zone_id = 0;
    public SectorPortTypeEnum $port_type = SectorPortTypeEnum::None;
    public ?string $beacon = null;
    public float $angle1 = 0.00;
    public float $angle2 = 0.00;
    public int $distance = 0;
    public int $fighters = 0;
    public int $port_organics = 0;
    public int $port_ore = 0;
    public int $port_goods = 0;
    public int $port_energy = 0;
    public $credits = 100000000;

    private function setAmount(SectorPortTypeEnum $resource, $amount)
    {
        match ($resource) {
            SectorPortTypeEnum::Energy => $this->port_energy = $amount,
            SectorPortTypeEnum::Ore => $this->port_ore = $amount,
            SectorPortTypeEnum::Organics => $this->port_organics = $amount,
            SectorPortTypeEnum::Goods => $this->port_goods = $amount,
        };
    }

    public function amount(?SectorPortTypeEnum $portType = null)
    {
        if (is_null($portType)) {
            $portType = $this->port_type;
        }

        return match ($portType) {
            SectorPortTypeEnum::Ore => $this->port_ore,
            SectorPortTypeEnum::Organics => $this->port_organics,
            SectorPortTypeEnum::Goods => $this->port_goods,
            SectorPortTypeEnum::Energy => $this->port_energy,
            default => 0,
        };
    }

    public function priceByPortType(SectorPortTypeEnum $portType)
    {
        $margin = $portType->delta() * ($this->port_type->is($portType) ? 1 : -1);
        $margin *= $this->amount($portType);
        $margin /= $portType->limit();
        $margin *= BalanceEnum::inventory_factor->val();

        return $portType->price() + $margin;
    }

    public function sell(SectorPortTypeEnum $resource, $amount): void
    {
        $current = $this->amount($resource);

        if ($current < $amount) {
            throw SectorException::notEnoughResourceForSelling($resource, $current, $amount);
        }

        switch ($resource) {
            case SectorPortTypeEnum::Energy:
            case SectorPortTypeEnum::Ore:
            case SectorPortTypeEnum::Organics:
            case SectorPortTypeEnum::Goods:
                $this->setAmount($resource, $current - $amount);
                break;
        }
    }

    public function buy(SectorPortTypeEnum $resource, $amount): void
    {
        $current = $this->amount($resource);

        switch ($resource) {
            case SectorPortTypeEnum::Energy:
            case SectorPortTypeEnum::Ore:
            case SectorPortTypeEnum::Organics:
            case SectorPortTypeEnum::Goods:
                $this->setAmount($resource, $current + $amount);
                break;
        }
    }

    public function acceptPayment($cost): void
    {
        $this->credits += $cost;
    }

    public function payment($cost): void
    {
        if ($this->credits < $cost) {
            throw SectorException::notEnoughCreditsForPurchase($this->credits, $cost);
        }

        $this->credits -= $cost;
    }
}
