<?php

declare(strict_types=1);

namespace BNT\Log\Entity;

use BNT\Log\Enum\LogTypeEnum;

abstract class LogWithPlayer extends Log
{
    public string $player;
}
