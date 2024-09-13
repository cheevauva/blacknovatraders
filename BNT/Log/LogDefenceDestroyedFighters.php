<?php

declare(strict_types=1);

namespace BNT\Log;

use BNT\Log\LogTypeEnum;

class LogDefenceDestroyedFighters extends Log
{

    public LogTypeEnum $type = LogTypeEnum::DEFS_DESTROYED_F;
    public int $fighterslost;
    public int $sector;

}
