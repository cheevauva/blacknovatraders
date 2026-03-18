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
        $this->torpDmg += $torpDmg;
        $this->ship->torpDmg -= $torpDmg;
    }

    public function armorPts($armorPts): void
    {
        $this->armorPts += $armorPts;
        $this->ship->armorPts -= $armorPts;
    }

    public function fighters($fighters): void
    {
        $this->fighters += $fighters;
        $this->ship->fighters -= $fighters;
    }

    public function beams($beams): void
    {
        $this->beams += $beams;
        $this->ship->beams -= $beams;
    }

    public function shields($shields): void
    {
        $this->shields += $shields;
        $this->ship->shields -= $shields;
    }
}
