<?php

declare(strict_types=1);

namespace BNT\Zone;

class Zone
{

    public int $zone_id;
    public string $zone_name;
    public int $owner = 0;
    public bool $corp_zone = false;
    public ?bool $allow_beacon = true;
    public bool $allow_attack = true;
    public bool $allow_planetattack = true;
    public ?bool $allow_warpedit = true;
    public ?bool $allow_planet = true;
    public ?bool $allow_trade = true;
    public ?bool $allow_defenses = true;
    public int $max_hull = 0;

}
