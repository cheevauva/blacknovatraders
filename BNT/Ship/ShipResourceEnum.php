<?php

declare(strict_types=1);

namespace BNT\Ship;

enum ShipResourceEnum
{
    case Ore;
    case Organics;
    case Goods;
    case Energy;

    public function shipProperty(): string
    {
        return match ($this) {
            ShipResourceEnum::Ore => 'ship_ore',
            ShipResourceEnum::Organics => 'ship_organics',
            ShipResourceEnum::Energy => 'ship_energy',
            ShipResourceEnum::Goods => 'ship_goods',
        };
    }
}
