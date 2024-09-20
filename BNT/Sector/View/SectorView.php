<?php

declare(strict_types=1);

namespace BNT\Sector\View;

use BNT\Sector\Entity\Sector;
use BNT\Sector\Enum\SectorPortTypeEnum;

class SectorView
{
    protected Sector $sector;

    public function __construct(Sector $sector)
    {
        $this->sector = $sector;
    }

    public function id(): string
    {
        return strval($this->sector->sector_id);
    }

    public function portType(): string
    {
        return $this->sector->port_type->value;
    }

    public function ore(): string
    {
        return NUMBER($this->sector->port_ore);
    }

    public function organics(): string
    {
        return NUMBER($this->sector->port_organics);
    }

    public function goods(): string
    {
        return NUMBER($this->sector->port_goods);
    }

    public function energy(): string
    {
        return NUMBER($this->sector->port_energy);
    }

    public function isPort(): bool
    {
        return match ($this->sector->port_type) {
            SectorPortTypeEnum::None => false,
            default => true,
        };
    }

    public function portTypeName(): string
    {
        global $l_ore;
        global $l_none;
        global $l_energy;
        global $l_organics;
        global $l_goods;
        global $l_special;

        return match ($this->sector->port_type) {
            SectorPortTypeEnum::Ore => $l_ore,
            SectorPortTypeEnum::None => $l_none,
            SectorPortTypeEnum::Energy => $l_energy,
            SectorPortTypeEnum::Organics => $l_organics,
            SectorPortTypeEnum::Goods => $l_goods,
            SectorPortTypeEnum::Special => $l_special,
        };
    }
}
