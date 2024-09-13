<?php

declare(strict_types=1);

namespace BNT\Servant;

use BNT\Ship\DAO\ShipSaveDAO;
use BNT\SectorDefence\Exception\SectorDefenceDetectYourShipException;
use BNT\Servant\SectorDefenceFightSevant;

class SectorDefenceSneakAsTurnServant extends SectorDefenceSneakServant
{
    public ?SectorDefenceFightSevant $fight;

    public function serve(): void
    {
        global $l_chf_thefightersdetectyou;

        parent::serve();

        if ($this->roll < $this->success) {
            throw new SectorDefenceDetectYourShipException($l_chf_thefightersdetectyou);
        }

        ShipSaveDAO::call($this->ship);
    }

}
