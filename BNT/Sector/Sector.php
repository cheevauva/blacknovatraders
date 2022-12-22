<?php

declare(strict_types=1);

namespace BNT\Sector;

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

}
