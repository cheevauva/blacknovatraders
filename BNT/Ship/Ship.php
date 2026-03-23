<?php

declare(strict_types=1);

namespace BNT\Ship;

use BNT\Ship\VO\ShipBattleStateVO;

class Ship
{

    public int $id;
    public string $name;
    public int|float $beams;
    public int|float $shields;
    public int|float $energy;
    public int|float $fighters;
    public int $armor;
    public int|float $armor_pts;
    public int $ore;
    public int $organics;
    public int $goods;
    public int $credits;
    public int $colonists;
    public $hull;
    public $engines;
    public $power;
    public $computer;
    public $sensors;
    public int $torp_launchers;
    public int $torps;
    public $cloak;
    public int $sector;
    public int $dev_emerwarp;
    public bool $dev_escapepod;
    public int $turns;
    public int $turns_used;
    public string $cleared_defences;
    protected ?ShipBattleStateVO $battleState;

    public function battleState(): ShipBattleStateVO
    {
        return $this->battleState ??= new ShipBattleStateVO($this);
    }

    public function turn(int $turns = 1): void
    {
        $this->turns -= $turns;
        $this->turns_used += $turns;
    }

    public function score(): mixed
    {
        return array_sum([
            $this->hull,
            $this->engines,
            $this->power,
            $this->computer,
            $this->sensors,
            $this->armor,
            $this->shields,
            $this->beams,
            $this->torp_launchers,
            $this->cloak,
        ]) / 10;
    }
}
