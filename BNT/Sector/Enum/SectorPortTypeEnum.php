<?php

declare(strict_types=1);

namespace BNT\Sector\Enum;

use BNT\Enum\BalanceEnum;
use BNT\Ship\Enum\ShipResourceEnum;

enum SectorPortTypeEnum: string
{
    case Ore = 'ore';
    case Organics = 'organics';
    case Goods = 'goods';
    case Energy = 'energy';
    case None = 'none';
    case Special = 'special';

    public function price()
    {
        return match ($this) {
            SectorPortTypeEnum::Energy => BalanceEnum::energy_price->val(),
            SectorPortTypeEnum::Goods => BalanceEnum::goods_price->val(),
            SectorPortTypeEnum::Ore => BalanceEnum::ore_price->val(),
            SectorPortTypeEnum::Organics => BalanceEnum::organics_price->val(),
        };
    }

    public function limit()
    {
        return match ($this) {
            SectorPortTypeEnum::Energy => BalanceEnum::energy_limit->val(),
            SectorPortTypeEnum::Goods => BalanceEnum::goods_limit->val(),
            SectorPortTypeEnum::Ore => BalanceEnum::ore_limit->val(),
            SectorPortTypeEnum::Organics => BalanceEnum::organics_limit->val(),
        };
    }

    public function delta()
    {
        return match ($this) {
            SectorPortTypeEnum::Energy => BalanceEnum::energy_delta->val(),
            SectorPortTypeEnum::Goods => BalanceEnum::goods_delta->val(),
            SectorPortTypeEnum::Ore => BalanceEnum::ore_delta->val(),
            SectorPortTypeEnum::Organics => BalanceEnum::organics_delta->val(),
        };
    }

    public function is(SectorPortTypeEnum $portType): bool
    {
        return $this === $portType;
    }

    public function toShipResource(): ShipResourceEnum
    {
        return match ($this) {
            SectorPortTypeEnum::Energy => ShipResourceEnum::Energy,
            SectorPortTypeEnum::Goods => ShipResourceEnum::Goods,
            SectorPortTypeEnum::Ore => ShipResourceEnum::Ore,
            SectorPortTypeEnum::Organics => ShipResourceEnum::Organics
        };
    }
}
