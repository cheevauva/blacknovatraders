<?php

declare(strict_types=1);

namespace BNT\Ship;

class Ship
{

    public $name;
    public $beams;
    public $energy;
    public $shields;
    public $torpNum;
    public $fighters;
    public $torpDmg;
    public $armorPts;
    public array $ship;

    public function __construct(array $ship)
    {
        global $level_factor;
        global $torp_dmg_rate;

        $this->ship = $ship;
        $this->name = $ship['ship_name'];
        $this->energy = $this->ship['ship_energy'];
        $this->beams = NUM_BEAMS($this->ship['beams']);
        $this->shields = NUM_SHIELDS($this->ship['shields']);
        $this->armorPts = $this->ship['armor_pts'];
        $this->fighters = $this->ship['ship_fighters'];

        if ($this->beams > $this->energy) {
            $this->beams = $this->energy;
        }

        $this->energy -= $this->beams;

        if ($this->shields > $this->energy) {
            $this->shields = $this->energy;
        }

        $this->energy -= $this->shields;

        $this->torpNum = round(mypw($level_factor, $ship['torp_launchers'])) * 10;

        if ($this->torpNum > $ship['torps']) {
            $this->torpNum = $ship['torps'];
        }

        $this->torpDmg = $torp_dmg_rate * $this->torpNum;
    }
}
