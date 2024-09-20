<?php

declare(strict_types=1);

namespace BNT\SectorDefence\Servant;

use BNT\Ship\Entity\Ship;
use BNT\Ship\DAO\ShipSaveDAO;
use BNT\SectorDefence\Entity\SectorDefence;
use BNT\SectorDefence\DAO\SectorDefenceSaveDAO;
use BNT\SectorDefence\Enum\SectorDefenceFmSettingEnum;
use BNT\SectorDefence\Enum\SectorDefenceTypeEnum;
use BNT\SectorDefence\Servant\SectorDefenceDeployCheckServant;

class SectorDefenceDeployServant implements \BNT\ServantInterface
{
    public Ship $ship;
    public bool $doIt = false;
    public int $numfighters = 0;
    public int $nummines = 0;
    public SectorDefenceFmSettingEnum $mode;
    public ?SectorDefence $defenceMine = null;
    public ?SectorDefence $defenceFighter = null;

    public function serve(): void
    {
        $deployCheck = new SectorDefenceDeployCheckServant;
        $deployCheck->ship = $this->ship;
        $deployCheck->serve();

        $this->defenceFighter = $this->defenceFighter ?: $deployCheck->defenceFighter;
        $this->defenceMine = $this->defenceMine ?: $deployCheck->defenceMine;

        $this->nummines = abs($this->nummines);
        $this->numfighters = abs($this->numfighters);

        if ($this->nummines > $this->ship->torps) {
            $this->nummines = 0;
        }

        if ($this->numfighters > $this->ship->ship_fighters) {
            $this->numfighters = 0;
        }

        if (!$this->defenceFighter) {
            $this->defenceFighter = new SectorDefence;
            $this->defenceFighter->ship_id = $this->ship->ship_id;
            $this->defenceFighter->sector_id = $this->ship->sector;
            $this->defenceFighter->quantity += $this->numfighters;
            $this->defenceFighter->fm_setting = $this->mode;
            $this->defenceFighter->defence_type = SectorDefenceTypeEnum::Fighters;
        } else {
            $this->defenceFighter->quantity += $this->numfighters;
            $this->defenceFighter->fm_setting = $this->mode;
        }

        if (!$this->defenceMine) {
            $this->defenceMine = new SectorDefence;
            $this->defenceMine->ship_id = $this->ship->ship_id;
            $this->defenceMine->sector_id = $this->ship->sector;
            $this->defenceMine->quantity += $this->nummines;
            $this->defenceMine->fm_setting = $this->mode;
            $this->defenceMine->defence_type = SectorDefenceTypeEnum::Mines;
        } else {
            $this->defenceMine->quantity += $this->nummines;
            $this->defenceMine->fm_setting = $this->mode;
        }

        $this->ship->last_login = new \DateTime;
        $this->ship->ship_fighters -= $this->numfighters;
        $this->ship->torps -= $this->nummines;

        $this->doIt();
    }

    private function doIt(): void
    {
        if (!$this->doIt) {
            return;
        }

        $this->ship->turn();

        ShipSaveDAO::call($this->ship);
        SectorDefenceSaveDAO::call($this->defenceFighter);
        SectorDefenceSaveDAO::call($this->defenceMine);
    }
}
