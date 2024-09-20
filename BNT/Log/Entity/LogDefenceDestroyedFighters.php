<?php

declare(strict_types=1);

namespace BNT\Log\Entity;

use BNT\Log\Enum\LogTypeEnum;

class LogDefenceDestroyedFighters extends Log
{

    public LogTypeEnum $type = LogTypeEnum::DEFS_DESTROYED_F;
    public int $fighterslost;
    public int $sector;

}
