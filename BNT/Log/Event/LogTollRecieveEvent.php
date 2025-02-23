<?php

declare(strict_types=1);

namespace BNT\Log\Event;

use BNT\Log\Enum\LogTypeEnum;
use BNT\Log\Entity\Log;

class LogTollRecieveEvent extends LogEvent
{

    public int $tollAmount;
    public int $sector;

    protected function prepareLog(): Log
    {
        $log = parent::prepareLog();
        $log->type = LogTypeEnum::TOLL_RECV;
        //
        $log->payload['sector'] = $this->sector;
        $log->payload['tollAmount'] = $this->tollAmount;

        return $log;
    }

}
