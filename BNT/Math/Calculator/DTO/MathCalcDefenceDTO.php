<?php

declare(strict_types=1);

namespace BNT\Math\Calculator\DTO;

class MathCalcDefenceDTO extends \BNT\DTO
{

    public int $quantity = 0;
    public bool $isOwner = false;
    public bool $isFighters = false;
    public bool $isMines = false;
    public bool $isOwnerTeam = false;
    public MathShipDTO $ship;

}
