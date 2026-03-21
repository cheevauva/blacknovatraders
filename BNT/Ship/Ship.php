<?php

declare(strict_types=1);

namespace BNT\Ship;

use BNT\Ship\VO\ShipLossesInBattleVO;

class Ship
{

    public int $id;
    public $name;
    public $numBeams;
    public $numShields;
    public $numTorp;
    public $numTorpLaunchers;
    public $energy;
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
    public int $sector;
    public int $dev_emerwarp;
    public int $turns;
    public int $turns_used;
    protected ?ShipLossesInBattleVO $lossesInBattle;
    public array $ship;

    public function __construct(array $ship)
    {
        global $level_factor;
        global $torp_dmg_rate;

        $this->ship = $ship;
        $this->id = $ship['ship_id'];
        $this->name = $ship['ship_name'];
        $this->energy = $this->ship['ship_energy'];
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
        $this->sector = $ship['sector'];
        $this->dev_emerwarp = $ship['dev_emerwarp'];
        $this->turns = $ship['turns'];
        $this->turns_used = $ship['turns_used'];
        //
        $this->numBeams = NUM_BEAMS($this->ship['beams']);
        $this->numShields = NUM_SHIELDS($this->ship['shields']);
        $this->numTorpLaunchers = round(mypw($level_factor, $this->torpLaunchers)) * 10;
        $this->numTorp = $this->numTorpLaunchers > $this->torps ? $this->torps : $this->numTorpLaunchers;
        $this->torpDmg = $torp_dmg_rate * $this->numTorp;
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
            round(mypw($upgrade_factor, $this->numBeams)),
            round(mypw($upgrade_factor, $this->torpLaunchers)),
            round(mypw($upgrade_factor, $this->numShields)),
            round(mypw($upgrade_factor, $this->armor)),
            round(mypw($upgrade_factor, $this->cloak))
        ]);
    }

    public function lossesInBattle(): ShipLossesInBattleVO
    {
        return $this->lossesInBattle ??= new ShipLossesInBattleVO($this);
    }
}
