<?php

declare(strict_types=1);

namespace BNT\Sector\Servant;

use BNT\ServantInterface;
use BNT\Ship\Ship;

class SectorPortSpecialServant implements ServantInterface
{

    public Ship $ship;
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
        global $max_emerwarp;
        global $fighter_max;
        global $torpedo_max;
        global $armor_max;

        $playerinfo = $this->ship;

        $this->emerwarp_free = $max_emerwarp - $playerinfo->dev_emerwarp;
        $this->fighter_max = NUM_FIGHTERS($playerinfo->computer);
        $this->fighter_free = $fighter_max - $playerinfo->ship_fighters;
        $this->torpedo_max = NUM_TORPEDOES($playerinfo->torp_launchers);
        $this->torpedo_free = $torpedo_max - $playerinfo->torps;
        $this->armor_max = NUM_ARMOUR($playerinfo->armor);
        $this->armor_free = $armor_max - $playerinfo->armor_pts;
        $this->colonist_max = NUM_HOLDS($playerinfo->hull) - $playerinfo->ship_ore - $playerinfo->ship_organics - $playerinfo->ship_goods;
        $this->colonist_free = $playerinfo->getFreeHolds();
    }

    public static function call(Ship $ship): self
    {
        $self = new static;
        $self->ship = $ship;
        $self->serve();

        return $self;
    }

}
