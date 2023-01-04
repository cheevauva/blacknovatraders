<?php

declare(strict_types=1);

namespace BNT\Log;

class LogAttackLose extends Log
{

    public LogTypeEnum $type = LogTypeEnum::ATTACK_LOSE;
    public bool $escapepod;
    public string $player;

}
