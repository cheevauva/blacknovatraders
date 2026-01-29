<?php

declare(strict_types=1);

namespace BNT\Math\Calculator\Servant;

use BNT\Math\DTO\MathShipDTO;
use BNT\Enum\BalanceEnum;

class MathPortSpecialServant extends \BNT\Servant
{

    public MathShipDTO $ship;
    public float $totalBounty;
    public $emerwarp_free;
    public $fighter_max;
    public $fighter_free;
    public $torpedo_max;
    public $torpedo_free;
    public $armor_max;
    public $armor_free;
    public $colonist_max;
    public $colonist_free;

    public function serve(): void
    {
        $this->emerwarp_free = BalanceEnum::max_emerwarp->val() - $this->ship->dev_emerwarp;
        $this->fighter_max = NUM_FIGHTERS($this->ship->computer);
        $this->fighter_free = BalanceEnum::fighter_max->val() - $this->ship->fighters;
        $this->torpedo_max = NUM_TORPEDOES($this->ship->torp_launchers);
        $this->torpedo_free = BalanceEnum::torpedo_max->val() - $this->ship->torps;
        $this->armor_max = NUM_ARMOUR($this->ship->armor);
        $this->armor_free = BalanceEnum::armor_max->val() - $this->ship->armorPts;
        $this->colonist_max = NUM_HOLDS($this->ship->hull) - $this->ship->ore - $this->ship->organics - $this->ship->goods;
        $this->colonist_free = $this->ship->getFreeHolds();
    }

}
