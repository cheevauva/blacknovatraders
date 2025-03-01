<?php

declare(strict_types=1);

namespace BNT\Math\DTO;

class MathDefenceDTO extends \BNT\DTO
{

    public int $quantity = 0;
    public bool $isOwner = false;
    public bool $isFighters = false;
    public bool $isMines = false;
    public bool $isOwnerTeam = false;
    public MathShipDTO $ship;

}
