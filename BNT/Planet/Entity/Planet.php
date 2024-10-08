<?php

declare(strict_types=1);

namespace BNT\Planet\Entity;

class Planet
{
    use \BNT\Traits\AsTrait;

    public int $planet_id;
    public int $sector_id = 0;
    public ?string $name = null;
    public int $organics = 0;
    public int $ore = 0;
    public int $goods = 0;
    public int $energy = 0;
    public int $colonists = 0;
    public int $credits = 0;
    public int $fighters = 0;
    public int $torps = 0;
    public int $owner = 0;
    public int $corp = 0;
    public bool $base = false;
    public bool $sells = false;
    public int $prod_organics = 0;
    public int $prod_ore = 0;
    public int $prod_goods = 0;
    public int $prod_energy = 0;
    public int $prod_fighters = 0;
    public int $prod_torp = 0;
    public bool $defeated = false;
}
