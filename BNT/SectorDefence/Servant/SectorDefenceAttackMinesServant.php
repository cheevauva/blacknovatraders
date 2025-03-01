<?php

declare(strict_types=1);

namespace BNT\SectorDefence\Servant;

use BNT\Servant;
use BNT\Ship\Entity\Ship;
use BNT\SectorDefence\DAO\SectorDefenceRetrieveManyByCriteriaDAO;
use BNT\Math\Event\MathDefenceCalculateMinesEvent;
use BNT\SectorDefence\Enum\SectorDefenceTypeEnum;
use BNT\SectorDefence\Entity\SectorDefence;
use BNT\Ship\Mapper\ShipToMathShipMapper;

class SectorDefenceAttackMinesServant extends Servant
{

    public int $sector;
    public Ship $ship;

    public function serve(): void
    {
        $retrieveDefences = SectorDefenceRetrieveManyByCriteriaDAO::new($this->container);
        $retrieveDefences->sector_id = $this->sector;
        $retrieveDefences->defence_type = SectorDefenceTypeEnum::Mines;
        $retrieveDefences->orderByQuantityDESC = true;
        $retrieveDefences->serve();

        $math = new MathDefenceCalculateMinesEvent();
        
        $shipMapper = ShipToMathShipMapper::new($this->container);
        $shipMapper->mathShip = $math->ship;
        $shipMapper->ship = $this->ship;
        $shipMapper->serve();

        foreach ($retrieveDefences->defences as $defence) {
            $defence = SectorDefence::as($retrieveDefences);

            $fightersOwner = ShipRetrieveByIdDAO::call($this->container, $defence->ship_id);

            $mathMapper = SectorDefenceToMathDefenceMapper::new($this->container);
            $mathMapper->sectorDefence = $defence;
            $mathMapper->mathDefence = $math->defences->defence();
            $mathMapper->ship = $this->ship;
            $mathMapper->defenceShip = $fightersOwner;
            $mathMapper->serve();
            
            break;
        }
        
    }

}
