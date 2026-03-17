<?php

declare(strict_types=1);

namespace BNT\Ship;

class Ship
{

    public int $id;
    public $name;
    public $beams;
    public $energy;
    public $shields;
    public $torpNum;
    public $fighters;
    public $torpDmg;
    public $armor;
    public $armorPts;
    public $ore;
    public $organics;
    public $goods;
    public $credits;
    public $colonists;
    public $hull;
    public $engines;
    public $power;
    public $computer;
    public $sensors;
    public $torpLaunchers;
    public $torps;
    public $cloak;
    public $rating;
    public array $ship;

    public function __construct(array $ship)
    {
        global $level_factor;
        global $torp_dmg_rate;

        $this->ship = $ship;
        $this->id = $ship['ship_id'];
        $this->name = $ship['ship_name'];
        $this->energy = $this->ship['ship_energy'];
        $this->beams = NUM_BEAMS($this->ship['beams']);
        $this->shields = NUM_SHIELDS($this->ship['shields']);
        $this->armorPts = $this->ship['armor_pts'];
        $this->armor = $this->ship['armor'];
        $this->fighters = $this->ship['ship_fighters'];
        $this->ore = $this->ship['ship_ore'];
        $this->organics = $this->ship['ship_organics'];
        $this->goods = $this->ship['ship_goods'];
        $this->engines = $this->ship['engines'];
        $this->power = $this->ship['power'];
        $this->computer = $this->ship['computer'];
        $this->sensors = $this->ship['sensors'];
        $this->colonists = $this->ship['ship_colonists'];
        $this->hull = $this->ship['hull'];
        $this->torpLaunchers = $ship['torp_launchers'];
        $this->torps = $ship['torps'];
        $this->cloak = $ship['cloak'];
        $this->rating = $ship['rating'];

        if ($this->beams > $this->energy) {
            $this->beams = $this->energy;
        }

        $this->energy -= $this->beams;

        if ($this->shields > $this->energy) {
            $this->shields = $this->energy;
        }

        $this->energy -= $this->shields;

        $this->torpNum = round(mypw($level_factor, $this->torpLaunchers)) * 10;

        if ($this->torpNum > $this->torps) {
            $this->torpNum = $this->torps;
        }

        $this->torpDmg = $torp_dmg_rate * $this->torpNum;
    }

    public function upgradeValue(): float
    {
        global $upgrade_cost;
        global $upgrade_factor;

        return $upgrade_cost * array_sum([
            round(mypw($upgrade_factor, $this->hull)),
            round(mypw($upgrade_factor, $this->engines)),
            round(mypw($upgrade_factor, $this->power)),
            round(mypw($upgrade_factor, $this->computer)),
            round(mypw($upgrade_factor, $this->sensors)),
            round(mypw($upgrade_factor, $this->beams)),
            round(mypw($upgrade_factor, $this->torpLaunchers)),
            round(mypw($upgrade_factor, $this->shields)),
            round(mypw($upgrade_factor, $this->armor)),
            round(mypw($upgrade_factor, $this->cloak))
        ]);
    }
}
