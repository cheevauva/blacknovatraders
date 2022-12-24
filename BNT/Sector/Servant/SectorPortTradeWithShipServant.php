<?php

declare(strict_types=1);

namespace BNT\Sector\Servant;

use BNT\ServantInterface;
use BNT\Sector\Sector;
use BNT\Sector\SectorPortTypeEnum;
use BNT\Ship\Ship;

class SectorPortTradeWithShipServant implements ServantInterface
{

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
        global $organics_price;
        global $ore_price;
        global $goods_price;
        global $energy_price;

        $sector = $this->sector;
        $ship = $this->ship;

        $this->ore_price = $sector->priceByPortType(SectorPortTypeEnum::Ore);
        $this->organics_price = $sector->priceByPortType(SectorPortTypeEnum::Organics);;
        $this->goods_price = $sector->priceByPortType(SectorPortTypeEnum::Goods);
        $this->energy_price = $sector->priceByPortType(SectorPortTypeEnum::Energy);

        // establish default amounts for each commodity
        $this->ore_amount = $ship->ship_ore;
        $this->organics_amount = $ship->ship_organics;
        $this->goods_amount = $ship->ship_goods;
        $this->energy_amount = $ship->ship_energy;

        match ($sector->port_type) {
            SectorPortTypeEnum::Ore => $this->ore_amount = NUM_HOLDS($ship->hull) - $ship->ship_ore - $ship->ship_colonists,
            SectorPortTypeEnum::Organics => $this->organics_amount = NUM_HOLDS($ship->hull) - $ship->ship_organics - $ship->ship_colonists,
            SectorPortTypeEnum::Goods => $this->goods_amount = NUM_HOLDS($ship->hull) - $ship->ship_goods - $ship->ship_colonists,
            SectorPortTypeEnum::Energy => $this->energy_amount = $ship->getFreePower(),
        };

        // limit amounts to port quantities
        $this->ore_amount = min($this->ore_amount, $sector->port_ore);
        $this->organics_amount = min($this->organics_amount, $sector->port_organics);
        $this->goods_amount = min($this->goods_amount, $sector->port_goods);
        $this->energy_amount = min($this->energy_amount, $sector->port_energy);

        // limit amounts to what the player can afford
        match (true) {
            $sector->port_type != SectorPortTypeEnum::Ore => $this->ore_amount = min($this->ore_amount, floor(($ship->credits + $this->organics_amount * $organics_price + $this->goods_amount * $goods_price + $this->energy_amount * $energy_price) / $ore_price)),
            $sector->port_type != SectorPortTypeEnum::Organics => $this->organics_amount = min($this->organics_amount, floor(($ship->credits + $this->ore_amount * $ore_price + $this->goods_amount * $goods_price + $this->energy_amount * $energy_price) / $organics_price)),
            $sector->port_type != SectorPortTypeEnum::Goods => $this->goods_amount = min($this->goods_amount, floor(($ship->credits + $this->ore_amount * $ore_price + $this->organics_amount * $organics_price + $this->energy_amount * $energy_price) / $goods_price)),
            $sector->port_type != SectorPortTypeEnum::Energy => $this->energy_amount = min($this->energy_amount, floor(($ship->credits + $this->ore_amount * $ore_price + $this->organics_amount * $organics_price + $this->goods_amount * $goods_price) / $energy_price)),
        };
    }

    public static function call(Sector $sector, Ship $ship): self
    {
        $self = new static;
        $self->sector = $sector;
        $self->ship = $ship;
        $self->serve();

        return $self;
    }

    public static function as($self): self
    {
        return $self;
    }

    public function old()
    {
        if ($sectorinfo->port_type == "ore") {
            $ore_price = $ore_price - $ore_delta * $sectorinfo->port_ore / $ore_limit * $inventory_factor;
            $sb_ore = $l_selling;
        } else {
            $ore_price = $ore_price + $ore_delta * $sectorinfo->port_ore / $ore_limit * $inventory_factor;
            $sb_ore = $l_buying;
        }
        if ($sectorinfo->port_type == "organics") {
            $organics_price = $organics_price - $organics_delta * $sectorinfo->port_organics / $organics_limit * $inventory_factor;
            $sb_organics = $l_selling;
        } else {
            $organics_price = $organics_price + $organics_delta * $sectorinfo->port_organics / $organics_limit * $inventory_factor;
            $sb_organics = $l_buying;
        }
        if ($sectorinfo->port_type == "goods") {
            $goods_price = $goods_price - $goods_delta * $sectorinfo->port_goods / $goods_limit * $inventory_factor;
            $sb_goods = $l_selling;
        } else {
            $goods_price = $goods_price + $goods_delta * $sectorinfo->port_goods / $goods_limit * $inventory_factor;
            $sb_goods = $l_buying;
        }
        if ($sectorinfo->port_type == BNT\Sector\SectorPortTypeEnum::Energy) {
            $energy_price = $energy_price - $energy_delta * $sectorinfo->port_energy / $energy_limit * $inventory_factor;
            $sb_energy = $l_selling;
        } else {
            $energy_price = $energy_price + $energy_delta * $sectorinfo->port_energy / $energy_limit * $inventory_factor;
            $sb_energy = $l_buying;
        }
        // establish default amounts for each commodity
        if ($sb_ore == $l_buying) {
            $amount_ore = $playerinfo->ship_ore;
        } else {
            $amount_ore = NUM_HOLDS($playerinfo->hull) - $playerinfo->ship_ore - $playerinfo->ship_colonists;
        }

        if ($sb_organics == $l_buying) {
            $amount_organics = $playerinfo->ship_organics;
        } else {
            $amount_organics = NUM_HOLDS($playerinfo->hull) - $playerinfo->ship_organics - $playerinfo->ship_colonists;
        }

        if ($sb_goods == $l_buying) {
            $amount_goods = $playerinfo->ship_goods;
        } else {
            $amount_goods = NUM_HOLDS($playerinfo->hull) - $playerinfo->ship_goods - $playerinfo->ship_colonists;
        }

        if ($sb_energy == $l_buying) {
            $amount_energy = $playerinfo->ship_energy;
        } else {
            $amount_energy = NUM_ENERGY($playerinfo->power) - $playerinfo->ship_energy;
        }

        // limit amounts to port quantities
        $amount_ore = min($amount_ore, $sectorinfo->port_ore);
        $amount_organics = min($amount_organics, $sectorinfo->port_organics);
        $amount_goods = min($amount_goods, $sectorinfo->port_goods);
        $amount_energy = min($amount_energy, $sectorinfo->port_energy);

        // limit amounts to what the player can afford
        if ($sb_ore == $l_selling) {
            $amount_ore = min($amount_ore, floor(($playerinfo->credits + $amount_organics * $organics_price + $amount_goods * $goods_price + $amount_energy * $energy_price) / $ore_price));
        }
        if ($sb_organics == $l_selling) {
            $amount_organics = min($amount_organics, floor(($playerinfo->credits + $amount_ore * $ore_price + $amount_goods * $goods_price + $amount_energy * $energy_price) / $organics_price));
        }
        if ($sb_goods == $l_selling) {
            $amount_goods = min($amount_goods, floor(($playerinfo->credits + $amount_ore * $ore_price + $amount_organics * $organics_price + $amount_energy * $energy_price) / $goods_price));
        }
        if ($sb_energy == $l_selling) {
            $amount_energy = min($amount_energy, floor(($playerinfo->credits + $amount_ore * $ore_price + $amount_organics * $organics_price + $amount_goods * $goods_price) / $energy_price));
        }
    }

}
