<?php

declare(strict_types=1);

namespace BNT\Ship\VO;

use BNT\Ship\Ship;

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
        if ($this->ship->numTorp - $torps < 0) {
            $torps = $this->ship->numTorp;
        }

        $this->torps += $torps;
        $this->ship->numTorp -= $torps;
    }

    public function torpDmg($torpDmg): void
    {
        if ($this->ship->torpDmg - $torpDmg < 0) {
            $torpDmg = $this->ship->torpDmg;
        }

        $this->torpDmg += $torpDmg;
        $this->ship->torpDmg -= $torpDmg;
    }

    public function armorPts($armorPts): void
    {
        if ($this->ship->armorPts - $armorPts < 0) {
            $armorPts = $this->ship->armorPts;
        }

        $this->armorPts += $armorPts;
        $this->ship->armorPts -= $armorPts;
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
        if ($this->ship->numBeams - $beams < 0) {
            $beams = $this->ship->numBeams;
        }

        $this->beams += $beams;
        $this->ship->numBeams -= $beams;
    }

    public function shields($shields): void
    {
        if ($this->ship->numShields - $shields < 0) {
            $shields = $this->ship->numShields;
        }

        $this->shields += $shields;
        $this->ship->numShields -= $shields;
    }
}
