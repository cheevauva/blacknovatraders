<?php

declare(strict_types=1);

namespace BNT\Sector\Servant;

use BNT\ServantInterface;
use BNT\Sector\Sector;
use BNT\Sector\SectorPortTypeEnum;

class SectorPortResourceOfferServant implements ServantInterface
{

    public Sector $sector;
    //
    public $trade_ore = 0;
    public $trade_organics = 0;
    public $trade_goods = 0;
    public $trade_energy = 0;
    //
    public $ore_price = 0;
    public $organics_price = 0;
    public $goods_price = 0;
    public $energy_price = 0;
    public $total_cost = 0;
    public $cargo_exchanged = 0;

    public function serve(): void
    {
        $sector = $this->sector;

        $this->trade_ore = $this->normalize($this->trade_ore);
        $this->trade_organics = $this->normalize($this->trade_organics);
        $this->trade_goods = $this->normalize($this->trade_goods);
        $this->trade_energy = $this->normalize($this->trade_energy);

        $this->trade_ore = $this->trade(SectorPortTypeEnum::Ore, $this->trade_ore);
        $this->trade_organics = $this->trade(SectorPortTypeEnum::Organics, $this->trade_organics);
        $this->trade_goods = $this->trade(SectorPortTypeEnum::Goods, $this->trade_goods);
        $this->trade_energy = $this->trade(SectorPortTypeEnum::Energy, $this->trade_energy);

        $this->ore_price = $sector->priceByPortType(SectorPortTypeEnum::Ore);
        $this->organics_price = $sector->priceByPortType(SectorPortTypeEnum::Organics);
        $this->goods_price = $sector->priceByPortType(SectorPortTypeEnum::Goods);
        $this->energy_price = $sector->priceByPortType(SectorPortTypeEnum::Energy);
        $this->cargo_exchanged = $this->trade_ore + $this->trade_organics + $this->trade_goods;
        $this->total_cost = $this->calculateTotal();
    }

    private function normalize($value)
    {
        return intval(round(abs(floatval($value ?? 0))));
    }

    private function calculateTotal()
    {
        return array_sum([
            $this->trade_ore * $this->ore_price,
            $this->trade_organics * $this->organics_price,
            $this->trade_goods * $this->goods_price,
            $this->trade_energy * $this->energy_price,
        ]);
    }

    private function trade(SectorPortTypeEnum $portType, $origin)
    {
        return $this->sector->port_type->is($portType) ? $origin : -$origin;
    }

    public static function as($self): self
    {
        return $self;
    }

    public static function call(Sector $sector): self
    {
        $self = new static;
        $self->sector = $sector;
        $self->serve();

        return $self;
    }

}
