<?php

declare(strict_types=1);

namespace BNT\Log\Event;

use BNT\Log\Enum\LogTypeEnum;
use BNT\Log\Entity\Log;

class LogDefenceKaboomEvent extends LogEvent
{

    public int $sector;
    public bool $dev_escapepod;

    protected function prepareLog(): Log
    {
        $log = parent::prepareLog();
        $log->type = LogTypeEnum::UNDEFINED;
        //
        $log->payload['sector'] = $this->sector;
        $log->payload['dev_escapepod'] = $this->dev_escapepod;

        return $log;
    }

}
