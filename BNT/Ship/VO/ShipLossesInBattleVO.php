<?php

declare(strict_types=1);

namespace BNT\Ship\VO;

use BNT\Ship\Ship;
use BNT\Ship\VO\ShipBattleStateVO;

class ShipLossesInBattleVO extends \UUA\VO
{

    public $energy = 0;
    public $fighters = 0;
    public $armorPts = 0;
    public $beams = 0;
    public $shields = 0;
    public $torpDmg = 0;
    public $torps = 0;

    public function __construct(protected Ship $ship)
    {
        
    }

    public function energy($energy): void
    {
        if ($this->ship->energy - $energy < 0) {
            $energy = $this->ship->energy;
        }

        $this->energy += $energy;
        $this->ship->energy -= $energy;
    }

    public function torps($torps): void
    {
        if ($this->battleState()->numTorp - $torps < 0) {
            $torps = $this->battleState()->numTorp;
        }

        $this->torps += $torps;
        $this->battleState()->numTorp -= $torps;
    }

    public function torpDmg($torpDmg): void
    {
        if ($this->battleState()->torpDmg - $torpDmg < 0) {
            $torpDmg = $this->battleState()->torpDmg;
        }

        $this->torpDmg += $torpDmg;
        $this->battleState()->torpDmg -= $torpDmg;
    }

    public function armorPts($armorPts): void
    {
        if ($this->ship->armor_pts - $armorPts < 0) {
            $armorPts = $this->ship->armor_pts;
        }

        $this->armorPts += $armorPts;
        $this->ship->armor_pts -= $armorPts;
    }

    public function fighters($fighters): void
    {
        if ($this->ship->fighters - $fighters < 0) {
            $fighters = $this->ship->fighters;
        }

        $this->fighters += $fighters;
        $this->ship->fighters -= $fighters;
    }

    public function beams($beams): void
    {
        if ($this->battleState()->beams - $beams < 0) {
            $beams = $this->battleState()->beams;
        }

        $this->beams += $beams;
        $this->battleState()->beams -= $beams;
    }

    public function shields($shields): void
    {
        if ($this->battleState()->shields - $shields < 0) {
            $shields = $this->battleState()->shields;
        }

        $this->shields += $shields;
        $this->battleState()->shields -= $shields;
    }

    protected function battleState(): ShipBattleStateVO
    {
        return $this->ship->battleState();
    }
}
