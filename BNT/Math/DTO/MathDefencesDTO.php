<?php

declare(strict_types=1);

namespace BNT\Math\DTO;

use BNT\Math\DTO\MathDefenceDTO;
use BNT\Math\DTO\MathShipDTO;

class MathDefencesDTO extends \BNT\DTO
{

    protected array $defences = [];
    public MathShipDTO $ship;

    public function defence(): MathDefenceDTO
    {
        return $this->defences[] = $defence = new MathDefenceDTO();
    }

    public function ship(): MathShipDTO
    {
        return $this->ship ??= new MathShipDTO;
    }

}
