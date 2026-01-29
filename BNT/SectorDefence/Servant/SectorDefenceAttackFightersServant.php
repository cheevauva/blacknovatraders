<?php

declare(strict_types=1);

namespace BNT\SectorDefence\Servant;

use BNT\Servant;
use BNT\Ship\Entity\Ship;
use BNT\Ship\DAO\ShipRetrieveByIdDAO;
use BNT\Sector\DAO\SectorRetrieveByIdDAO;
use BNT\SectorDefence\DAO\SectorDefenceRetrieveManyByCriteriaDAO;
use BNT\SectorDefence\Entity\SectorDefence;
use BNT\SectorDefence\Enum\SectorDefenceTypeEnum;
use BNT\Math\Mediator\MathDefenceCalculateFightersMediator;
use BNT\SectorDefence\DTO\SectorDefenceWithFightersOwnerDTO;

class SectorDefenceAttackFightersServant extends Servant
{

    public int $sector;
    public Ship $ship;
    //
    public int $totalSectorFightes = 0;
    public int $fightersToll = 0;
    public protected(set) bool $hasEnemy = false;
    public ?Ship $fightersOwner = null;
    public ?SectorDefence $fightersDefence = null;

    #[\Override]
    public function serve(): void
    {
        $sectorObj = SectorRetrieveByIdDAO::call($this->container, $this->sector);

        $retrieveDefences = SectorDefenceRetrieveManyByCriteriaDAO::new($this->container);
        $retrieveDefences->sector_id = $sectorObj->sector_id;
        $retrieveDefences->defence_type = SectorDefenceTypeEnum::Fighters;
        $retrieveDefences->orderByQuantityDESC = true;
        $retrieveDefences->serve();

        $mathDefences = [];

        foreach ($retrieveDefences->defences as $defence) {
            $fightersOwner = ShipRetrieveByIdDAO::call($this->container, $defence->ship_id);

            $mathDefences[] = new SectorDefenceWithFightersOwnerDTO($defence, $fightersOwner);

            $this->fightersOwner = $fightersOwner;
            $this->fightersDefence = $defence;
        }

        $math = MathDefenceCalculateFightersMediator::new($this->container);
        $math->defences = $mathDefences;
        $math->serve();

        $this->totalSectorFightes = $math->totalFighters;
        $this->fightersToll = $math->fightersToll;
        $this->hasEnemy = $math->hasEmenyFighters;
    }
}
