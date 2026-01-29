<?php

declare(strict_types=1);

namespace BNT\SectorDefence\Servant;

use BNT\Ship\Entity\Ship;
use BNT\Ship\DAO\ShipSaveDAO;
use BNT\SectorDefence\Servant\SectorDefenceFightServant;
use BNT\Servant;

class SectorDefenceSneakServant extends Servant
{

    public Ship $ship;
    public Ship $fightersOwner;
    public int $sectorId;
    public ?SectorDefenceFightServant $fight;
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
            $fight = $this->fight = SectorDefenceFightServant::new($this->container);
            $fight->sector_id = $this->sectorId;
            $fight->ship = $this->ship;
            $fight->serve();
        }

        ShipSaveDAO::call($this->container, $this->ship);
    }
}
