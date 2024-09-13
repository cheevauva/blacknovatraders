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
use BNT\SectorDefence\Exception\SectorDefenceDetectYourShipException;

class SectorDefenceCheckFightersServant implements ServantInterface
{

    public int $sector;
    public Ship $ship;
    public string $response;
    public bool $ok = true;

    public function serve(): void
    {
        $sectorObj = SectorRetrieveByIdDAO::call($this->sector);

        $retrieveDefences = new SectorDefenceRetrieveManyByCriteriaDAO;
        $retrieveDefences->sector_id = $sectorObj->sector_id;
        $retrieveDefences->defence_type = SectorDefenceTypeEnum::Fighters;
        $retrieveDefences->orderByQuantityDESC = true;
        $retrieveDefences->serve();

        if (empty($retrieveDefences->defences)) {
            return;
        }

        $defences = $retrieveDefences->defences;

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
            $fightersOwner = ShipRetrieveByIdDAO::call($defence->ship_id);

            if (!empty($this->ship->team) && $this->ship->team === $fightersOwner->team) {
                continue;
            }

            $this->ship->cleared_defences = null;

            ShipSaveDAO::call($this->ship);

            switch ($this->response) {
                case 'fight':
                    $fight = new SectorDefenceFightSevant;
                    $fight->ship = $this->ship;
                    $fight->sector_id = $sectorObj->sector_id;
                    $fight->serve();
                    
                    break;
                case 'retreat':
                    SectorDefenceRetreatServant::call($this->ship);
                    break;
                case 'pay':
                    $pay = new SectorDefencePayTollServant;
                    $pay->ship = $this->ship;
                    $pay->sector = $this->sector;
                    $pay->serve();
                    break;
                case 'sneak':
                    try {
                        $sneak = new SectorDefenceSneakServant;
                        $sneak->fightersOwner = $fightersOwner;
                        $sneak->ship = $this->ship;
                        $sneak->serve();
                    } catch (SectorDefenceDetectYourShipException $ex) {
                        $fight = new SectorDefenceFightSevant;
                        $fight->ship = $this->ship;
                        $fight->sector_id = $sectorObj->sector_id;
                        $fight->serve();
                        // todo
                    }

                    break;
            }

            break;
        }
    }

}
