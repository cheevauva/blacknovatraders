<?php

declare(strict_types=1);

namespace BNT\SectorDefence\Servant;

use BNT\ServantInterface;
use BNT\Ship\Entity\Ship;
use BNT\Ship\DAO\ShipRetrieveByIdDAO;
use BNT\Sector\DAO\SectorRetrieveByIdDAO;
use BNT\SectorDefence\DAO\SectorDefenceRetrieveManyByCriteriaDAO;
use BNT\SectorDefence\Entity\SectorDefence;
use BNT\SectorDefence\Enum\SectorDefenceTypeEnum;
use BNT\Enum\BalanceEnum;

class SectorDefenceCheckFightersServant implements ServantInterface
{
    public int $sector;
    public Ship $ship;
    public int $totalSectorFightes = 0;
    public int $fightersToll = 0;
    public bool $hasEnemy = false;
    public ?Ship $fightersOwner = null;
    public ?SectorDefence $fightersDefence = null;

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

        $this->totalSectorFightes = 0;
        $owner = true;

        foreach ($defences as $defence) {
            $defence = SectorDefence::as($defence);

            $this->totalSectorFightes += $defence->quantity;

            if ($defence->ship_id != $this->ship->ship_id) {
                $owner = false;
            }
        }
        $this->fightersToll = intval(round($this->totalSectorFightes * BalanceEnum::fighter_price->val() * 0.6));

        if ($owner || $this->totalSectorFightes < 1) {
            return;
        }

        $this->hasEnemy = true;

        foreach ($defences as $defence) {
            $defence = SectorDefence::as($defence);
            $fightersOwner = ShipRetrieveByIdDAO::call($defence->ship_id);
            $fightersDefence = $defence;

            if (!empty($this->ship->team) && $this->ship->team === $this->fightersOwner->team) {
                continue;
            }

            $this->fightersOwner = $fightersOwner;
            $this->fightersDefence = $fightersDefence;
            break;
        }
    }
}
