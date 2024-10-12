<?php

declare(strict_types=1);

namespace BNT\SectorDefence\Servant;

use BNT\Ship\Entity\Ship;
use BNT\Ship\DAO\ShipSaveDAO;
use BNT\SectorDefence\Servant\SectorDefenceFightSevant;

class SectorDefenceSneakServant implements \BNT\ServantInterface
{
    public Ship $ship;
    public Ship $fightersOwner;
    public int $sectorId;
    public ?SectorDefenceFightSevant $fight;
    public bool $doIt = true;
    public int $success;
    public int $roll;

    public function serve(): void
    {
        $this->ship->cleared_defences = null;

        $this->success = SCAN_SUCCESS($this->fightersOwner->sensors, $this->ship->cloak);

        if ($this->success < 5) {
            $this->success = 5;
        }

        if ($this->success > 95) {
            $this->success = 95;
        }

        $this->roll = rand(1, 100);

        if ($this->roll < $this->success) {
            $fight = $this->fight = SectorDefenceFightSevant::build();
            $fight->sector_id = $this->sectorId;
            $fight->ship = $this->ship;
            $fight->doIt = $this->doIt;
            $fight->serve();
        }

        if ($this->doIt) {
            ShipSaveDAO::call($this->ship);
        }
    }
}
