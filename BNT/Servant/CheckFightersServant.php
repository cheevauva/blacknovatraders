<?php

declare(strict_types=1);

namespace BNT\Servant;

use BNT\ServantInterface;
use BNT\Ship\Ship;
use BNT\Ship\DAO\ShipRetrieveByIdDAO;
use BNT\Ship\DAO\ShipSaveDAO;
use BNT\Sector\DAO\SectorRetrieveByIdDAO;
use BNT\SectorDefence\DAO\SectorDefenceRetrieveManyByCriteriaDAO;
use BNT\SectorDefence\SectorDefence;
use BNT\SectorDefence\SectorDefenceTypeEnum;
use BNT\Enum\CalledFromEnum;

class CheckFightersServant implements ServantInterface
{

    public int $sector;
    public Ship $ship;
    public string $response;
    public CalledFromEnum $calledFrom;
    public bool $ok = true;

    public function serve(): void
    {
        $sectorObj = SectorRetrieveByIdDAO::call($this->sector);

        $retrieveSectorDefences = new SectorDefenceRetrieveManyByCriteriaDAO;
        $retrieveSectorDefences->sector_id = $sectorObj->sector_id;
        $retrieveSectorDefences->defence_type = SectorDefenceTypeEnum::Fighters;
        $retrieveSectorDefences->orderByQuantityDESC = true;
        $retrieveSectorDefences->serve();

        if (empty($retrieveSectorDefences->defences)) {
            return;
        }

        $defences = $retrieveSectorDefences->defences;

        $totalSectorFighters = 0;
        $owner = true;

        foreach ($defences as $defence) {
            $defence = SectorDefence::as($defence);

            $totalSectorFighters += $defence->quantity;

            if ($defence->ship_id != $this->ship->id) {
                $owner = false;
            }
        }

        if ($owner || $totalSectorFighters < 0) {
            return;
        }


        foreach ($defences as $defence) {
            $defence = SectorDefence::as($defence);
            $fighters_owner = ShipRetrieveByIdDAO::call($defence->ship_id);

            if (!empty($this->ship->team) && $this->ship->team === $fighters_owner->team) {
                continue;
            }

            switch ($this->response) {
                case 'fight':
                    $this->ship->cleared_defences = null;
                    
                    ShipSaveDAO::call($this->ship);

                    $sectorFightes = new SectorFightersSevant;
                    $sectorFightes->sector_id = $sectorObj->sector_id;
                    $sectorFightes->calledFrom = $this->calledFrom;

                    break;
            }

            break;
        }
    }

}
