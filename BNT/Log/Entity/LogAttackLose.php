<?php

declare(strict_types=1);

namespace BNT\Log\Entity;

use BNT\Log\Enum\LogTypeEnum;

class LogAttackLose extends Log
{

    public LogTypeEnum $type = LogTypeEnum::ATTACK_LOSE;
    public bool $escapepod;
    public string $player;

}
