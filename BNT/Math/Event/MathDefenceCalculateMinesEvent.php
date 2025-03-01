<?php

declare(strict_types=1);

namespace BNT\Math\Event;

use BNT\Math\DTO\MathDefencesDTO;
use BNT\Math\DTO\MathShipDTO;
use BNT\Math\Servant\MathDefenceCalculateMinesServant;

class MathDefenceCalculateMinesEvent extends \BNT\Event
{

    public MathShipDTO $ship;
    public MathDefencesDTO $defences;

    public function __construct()
    {
        $this->ship = new MathShipDTO;
        $this->defences = new MathDefencesDTO();
    }

    public function to(object $object): void
    {
        parent::to($object);

        if ($object instanceof MathDefenceCalculateMinesServant) {
            $object->defences = $this->defences;
            $object->ship = $this->ship;
        }
    }

}
