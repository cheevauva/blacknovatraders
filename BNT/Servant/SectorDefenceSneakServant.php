<?php

declare(strict_types=1);

namespace BNT\Servant;

use BNT\Ship\Ship;
use BNT\Ship\DAO\ShipSaveDAO;
use BNT\Servant\SectorDefenceFightSevant;

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
            $this->fight = new SectorDefenceFightSevant;
            $this->fight->sector_id = $this->sectorId;
            $this->fight->ship = $this->ship;
            $this->fight->doIt = $this->doIt;
            $this->fight->serve();
        }

        if ($this->doIt) {
            ShipSaveDAO::call($this->ship);
        }
    }

}
