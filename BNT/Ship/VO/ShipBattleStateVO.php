<?php

declare(strict_types=1);

namespace BNT\Ship\VO;

use BNT\Ship\Ship;

class ShipBattleStateVO extends \UUA\VO
{

    public $beams;
    public $shields;
    public $numTorp;
    public $numTorpLaunchers;
    public $torpDmg;
    protected ?ShipLossesInBattleVO $losses;

    public function __construct(protected Ship $ship)
    {
        global $level_factor;
        global $torp_dmg_rate;

        $this->beams = NUM_BEAMS($this->ship->beams);
        $this->shields = NUM_SHIELDS($this->ship->shields);
        $this->numTorpLaunchers = round(mypw($level_factor, $this->ship->torp_launchers)) * 10;
        $this->numTorp = $this->numTorpLaunchers > $this->ship->torps ? $this->ship->torps : $this->numTorpLaunchers;
        $this->torpDmg = $torp_dmg_rate * $this->numTorp;
    }

    public function losses(): ShipLossesInBattleVO
    {
        return $this->losses ??= new ShipLossesInBattleVO($this->ship);
    }

    public function upgradeValue(): float
    {
        global $upgrade_cost;
        global $upgrade_factor;

        return $upgrade_cost * array_sum([
            round(mypw($upgrade_factor, $this->ship->hull)),
            round(mypw($upgrade_factor, $this->ship->engines)),
            round(mypw($upgrade_factor, $this->ship->power)),
            round(mypw($upgrade_factor, $this->ship->computer)),
            round(mypw($upgrade_factor, $this->ship->sensors)),
            round(mypw($upgrade_factor, $this->beams)),
            round(mypw($upgrade_factor, $this->ship->torpLaunchers)),
            round(mypw($upgrade_factor, $this->shields)),
            round(mypw($upgrade_factor, $this->ship->armor)),
            round(mypw($upgrade_factor, $this->ship->cloak))
        ]);
    }
}
