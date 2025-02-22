<?php

declare(strict_types=1);

namespace BNT\SectorDefence\Servant;

use BNT\Servant;
use BNT\Ship\Entity\Ship;

class SectorDefenceCheckMinesServant extends Servant
{
    public int $sector;
    public Ship $ship;

    public function serve(): void
    {
        
    }
}
