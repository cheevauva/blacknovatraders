<?php

declare(strict_types=1);

namespace BNT\Math\Calculator\DTO;

use BNT\Math\DTO\MathCalcDefenceDTO;
use BNT\Math\DTO\MathShipDTO;

class MathCalcDefencesDTO extends \BNT\DTO
{

    protected array $defences = [];
    public MathShipDTO $ship;

    public function defence(): MathCalcDefenceDTO
    {
        return $this->defences[] = $defence = new MathCalcDefenceDTO();
    }

    public function ship(): MathShipDTO
    {
        return $this->ship ??= new MathShipDTO;
    }

}
