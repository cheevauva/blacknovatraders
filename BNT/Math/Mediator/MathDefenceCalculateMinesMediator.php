<?php

declare(strict_types=1);

namespace BNT\Math\Mediator;

use BNT\Math\DTO\MathDefencesDTO;
use BNT\Math\DTO\MathShipDTO;
use BNT\Math\Servant\MathDefenceCalculateMinesServant;

class MathDefenceCalculateMinesMediator extends \BNT\Mediator
{

    public MathShipDTO $ship;
    public MathDefencesDTO $defences;

    public function __construct()
    {
        $this->ship = new MathShipDTO;
        $this->defences = new MathDefencesDTO();
    }

    #[\Override]
    public function serve(): void
    {
        $calculate = MathDefenceCalculateMinesServant::new($this->container);
        $calculate->defences = $this->defences;
        $calculate->ship = $this->ship;
        $calculate->serve();
    }
}
