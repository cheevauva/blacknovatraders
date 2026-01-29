<?php

declare(strict_types=1);

namespace BNT\Math\Mediator;

use BNT\Math\Calculator\Servant\MathDefenceCalculateMinesServant;
use BNT\Math\DTO\MathDefencesDTO;
use BNT\Ship\Entity\Ship;
use BNT\Ship\Mapper\ShipToMathShipMapper;

class MathDefenceCalculateMinesMediator extends \BNT\Mediator
{

    public Ship $ship;
    public MathDefencesDTO $defences;


    #[\Override]
    public function serve(): void
    {
        $calculate = MathDefenceCalculateMinesServant::new($this->container);
        $calculate->defences = $this->defences;
        $calculate->ship = ShipToMathShipMapper::call($this->container, $this->ship)->mathShip;
        $calculate->serve();
    }
}
