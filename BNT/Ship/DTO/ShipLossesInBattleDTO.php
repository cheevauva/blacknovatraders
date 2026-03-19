<?php

declare(strict_types=1);

namespace BNT\Ship\DTO;

use BNT\Ship\Ship;

class ShipLossesInBattleDTO extends \UUA\DTO
{

    public $fighters = 0;
    public $armorPts = 0;
    public $beams = 0;
    public $shields = 0;
    public $torpDmg = 0;

    public function __construct(protected Ship $ship)
    {
        
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
        if ($this->ship->beams - $beams < 0) {
            $beams = $this->ship->beams;
        }

        $this->beams += $beams;
        $this->ship->beams -= $beams;
    }

    public function shields($shields): void
    {
        if ($this->ship->shields - $shields < 0) {
            $shields = $this->ship->shields;
        }

        $this->shields += $shields;
        $this->ship->shields -= $shields;
    }
}
