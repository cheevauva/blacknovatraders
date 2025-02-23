<?php

declare(strict_types=1);

namespace BNT\Log\Event;

use BNT\Log\Enum\LogTypeEnum;
use BNT\Log\Entity\Log;

class LogAttackLoseEvent extends LogEvent
{

    public bool $escapepod;
    public string $player;

    protected function prepareLog(): Log
    {
        $log = parent::prepareLog();
        $log->type = LogTypeEnum::ATTACK_LOSE;
        //
        $log->payload['player'] = $this->player;
        $log->payload['escapepod'] = $this->escapepod;

        return $log;
    }

}
