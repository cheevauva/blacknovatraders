<?php

declare(strict_types=1);

namespace BNT\SectorDefence\Servant;

use BNT\Ship\Entity\Ship;
use BNT\Ship\DAO\ShipRetrieveByIdDAO;
use BNT\SectorDefence\Entity\SectorDefence;
use BNT\SectorDefence\Enum\SectorDefenceTypeEnum;
use BNT\SectorDefence\DAO\SectorDefenceRetrieveManyByCriteriaDAO;
use BNT\Sector\Entity\Sector;
use BNT\Sector\DAO\SectorRetrieveByIdDAO;
use BNT\Zone\Entity\Zone;
use BNT\Zone\DAO\ZoneRetrieveByIdDAO;
use Exception;
use BNT\Servant;

class SectorDefenceDeployCheckServant extends Servant
{

    public Ship $ship;
    public ?Ship $fightersOwner = null;
    public ?Ship $zoneOwner = null;
    public Sector $sector;
    public Zone $zone;
    public ?SectorDefence $defenceMine = null;
    public ?SectorDefence $defenceFighter = null;
    public int $totalSectorFighters = 0;
    public int $totalSectorMines = 0;
    public bool $hasOtherOwner = false;
    public int $numDefences = 0;
    public bool $allowDefenses = true;
    public bool $hasEmenyShipInSector = false;
    public bool $quite = false;

    public function serve(): void
    {
        $this->sector = SectorRetrieveByIdDAO::call($this->container, $this->ship->sector);
        $this->zone = ZoneRetrieveByIdDAO::call($this->container, $this->sector->zone_id);
        $this->zoneOwner = ShipRetrieveByIdDAO::call($this->container, $this->zone->owner);

        $defencesBySector = SectorDefenceRetrieveManyByCriteriaDAO::new($this->container);
        $defencesBySector->sector_id = $this->sector->sector_id;
        $defencesBySector->serve();

        foreach ($defencesBySector->defences as $defence) {
            $defence = SectorDefence::as($defence);

            if ($this->ship->ship_id != $defence->ship_id) {
                $this->hasOtherOwner = true;
                $this->fightersOwner = ShipRetrieveByIdDAO::call($this->container, $defence->ship_id);
            }

            if ($defence->defence_type === SectorDefenceTypeEnum::Fighters) {
                $this->totalSectorFighters += $defence->quantity;
                $this->defenceFighter = $defence;
            }

            if ($defence->defence_type === SectorDefenceTypeEnum::Mines) {
                $this->totalSectorMines += $defence->quantity;
                $this->defenceMine = $defence;
            }

            $this->numDefences++;
        }

        if ($this->zone->allow_defenses === false) {
            $this->allowDefenses = false;
        }

        $this->hasEmenyShipInSector = empty($this->ship->team) && !empty($this->fightersOwner) && $this->ship->team !== $this->fightersOwner->team;

        if (is_null($this->zone->allow_defenses) && $this->zone->owner != $this->ship->ship_id) {
            $this->allowDefenses = empty($this->ship->team) && !empty($this->zoneOwner) && $this->ship->team !== $this->zoneOwner->team;
        }

        $this->validate();
    }

    protected function validate(): void
    {
        global $l_mines_nodeploy;
        global $l_mines_nopermit;

        if ($this->quite) {
            return;
        }

        if (!$this->allowDefenses) {
            throw new Exception($l_mines_nodeploy);
        }

        if ($this->hasEmenyShipInSector) {
            throw new Exception($l_mines_nopermit);
        }
    }

}
