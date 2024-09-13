<?php

declare(strict_types=1);

namespace BNT\DTO;

use BNT\SectorDefence\SectorDefence;
use BNT\Ship\Ship;

class SectorDefenceDistributeTollDTO
{

    public Ship $ship;
    public SectorDefence $defence;
    public int $tollAmount;
    public int $sector;

    public static function as(self $self): static
    {
        return $self;
    }

}
