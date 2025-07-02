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
use BNT\SectorDefence\Mapper\SectorDefenceToMathDefenceMapper;

class SectorDefenceAttackFightersServant extends Servant
{

    public int $sector;
    public Ship $ship;
    //
    public int $totalSectorFightes = 0;
    public int $fightersToll = 0;
    public bool $hasEnemy = false;
    public ?Ship $fightersOwner = null;
    public ?SectorDefence $fightersDefence = null;

    public function serve(): void
    {
        $sectorObj = SectorRetrieveByIdDAO::call($this->container, $this->sector);

        $retrieveDefences = SectorDefenceRetrieveManyByCriteriaDAO::new($this->container);
        $retrieveDefences->sector_id = $sectorObj->sector_id;
        $retrieveDefences->defence_type = SectorDefenceTypeEnum::Fighters;
        $retrieveDefences->orderByQuantityDESC = true;
        $retrieveDefences->serve();

        $math = new MathDefenceCalculateFightersMediator();

        foreach ($retrieveDefences->defences as $defence) {
            $defence = SectorDefence::as($defence);

            $fightersOwner = ShipRetrieveByIdDAO::call($this->container, $defence->ship_id);
            $fightersDefence = $defence;

            $mathMapper = SectorDefenceToMathDefenceMapper::new($this->container);
            $mathMapper->sectorDefence = $defence;
            $mathMapper->mathDefence = $math->defences->defence();
            $mathMapper->ship = $this->ship;
            $mathMapper->defenceShip = $fightersOwner;
            $mathMapper->serve();

            $this->fightersOwner = $fightersOwner;
            $this->fightersDefence = $fightersDefence;
        }

        $math->serve();

        $this->totalSectorFightes = $math->totalFighters;
        $this->fightersToll = $math->fightersToll;
        $this->hasEnemy = $math->hasEmenyFighters;
    }

}
