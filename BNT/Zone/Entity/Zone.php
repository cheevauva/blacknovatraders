<?php

declare(strict_types=1);

namespace BNT\Zone\Entity;

use BNT\Zone\Enum\ZoneAllowEnum;

class Zone
{
    public int $zone_id;
    public string $zone_name;
    public int $owner = 0;
    public bool $corp_zone = false;
    public ZoneAllowEnum $allow_beacon = ZoneAllowEnum::Y;
    public bool $allow_attack = true;
    public ZoneAllowEnum $allow_planetattack = ZoneAllowEnum::Y;
    public ZoneAllowEnum $allow_warpedit = ZoneAllowEnum::Y;
    public ZoneAllowEnum $allow_planet = ZoneAllowEnum::Y;
    public ZoneAllowEnum $allow_trade = ZoneAllowEnum::Y;
    public ZoneAllowEnum $allow_defenses = ZoneAllowEnum::Y;
    public int $max_hull = 0;
}
