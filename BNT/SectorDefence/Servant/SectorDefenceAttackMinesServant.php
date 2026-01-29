<?php

declare(strict_types=1);

namespace BNT\SectorDefence\Servant;

use BNT\Servant;
use BNT\Ship\Entity\Ship;
use BNT\SectorDefence\DAO\SectorDefenceRetrieveManyByCriteriaDAO;
use BNT\Math\Mediator\MathDefenceCalculateMinesMediator;
use BNT\SectorDefence\Enum\SectorDefenceTypeEnum;
use BNT\SectorDefence\Entity\SectorDefence;
use BNT\Ship\DAO\ShipRetrieveByIdDAO;

class SectorDefenceAttackMinesServant extends Servant
{

    public int $sector;
    public Ship $ship;

    #[\Override]
    public function serve(): void
    {
        $retrieveDefences = SectorDefenceRetrieveManyByCriteriaDAO::new($this->container);
        $retrieveDefences->sector_id = $this->sector;
        $retrieveDefences->defence_type = SectorDefenceTypeEnum::Mines;
        $retrieveDefences->orderByQuantityDESC = true;
        $retrieveDefences->serve();

        $mathDefences = [];

        foreach ($retrieveDefences->defences as $defence) {
            $mathDefences[] = [
                $defence,
                $this->ship,
                ShipRetrieveByIdDAO::call($this->container, SectorDefence::as($defence)->ship_id)
            ];
            break;
        }

        $math = new MathDefenceCalculateMinesMediator();
        $math->ship = $this->ship;
        $math->defences = $mathDefences;
        $math->serve();
    }
}
