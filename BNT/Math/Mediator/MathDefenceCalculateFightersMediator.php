<?php

declare(strict_types=1);

namespace BNT\Math\Mediator;

use BNT\Math\DTO\MathDefencesDTO;
use BNT\Math\Servant\MathDefenceCalculateFightersServant;

class MathDefenceCalculateFightersMediator extends \BNT\Mediator
{

    public MathDefencesDTO $defences;
    public int $totalFighters = 0;
    public int $fightersToll = 0;
    public bool $hasEmenyFighters = false;

    public function __construct()
    {
        $this->defences = new MathDefencesDTO();
    }

    #[\Override]
    public function serve(): void
    {
        $calculate = MathDefenceCalculateFightersServant::new($this->container);
        $calculate->totalFighters = $this->totalFightes;
        $calculate->fightersToll = $this->fightersToll;
        $calculate->hasEmenyFighters = $this->hasEmenyFighters;
        $calculate->serve();
    }
}
