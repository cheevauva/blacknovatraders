<?php

declare(strict_types=1);

namespace BNT\Math\Event;

use BNT\Math\DTO\MathDefencesDTO;
use BNT\Math\Servant\MathDefenceCalculateFightersServant;

class MathDefenceCalculateFightersEvent extends \BNT\Event
{

    public MathDefencesDTO $defences;
    public int $totalFighters = 0;
    public int $fightersToll = 0;
    public bool $hasEmenyFighters = false;

    public function __construct()
    {
        $this->defences = new MathDefencesDTO();
    }

    public function from(object $object): void
    {
        parent::from($object);

        if ($object instanceof MathDefenceCalculateFightersServant) {
            $this->totalFighters = $object->totalFightes;
            $this->fightersToll = $object->fightersToll;
            $this->hasEmenyFighters = $object->hasEmenyFighters;
        }
    }

}
