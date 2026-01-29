<?php

declare(strict_types=1);

namespace BNT\SectorDefence\DTO;

use BNT\SectorDefence\Entity\SectorDefence;
use BNT\Ship\Entity\Ship;

class SectorDefenceWithFightersOwnerDTO extends \BNT\DTO
{

    public function __construct(public SectorDefence $defence, public Ship $fightersOwner)
    {
        
    }
}
