<?php

declare(strict_types=1);

namespace BNT\SectorDefence\Servant;

use BNT\Ship\Entity\Ship;

class SectorDefenceCheckMinesServant implements \BNT\ServantInterface
{
    public int $sector;
    public Ship $ship;

    public function serve(): void
    {
        
    }
}
