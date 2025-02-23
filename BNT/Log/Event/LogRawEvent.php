<?php

declare(strict_types=1);

namespace BNT\Log\Event;

use BNT\Log\Enum\LogTypeEnum;
use BNT\Log\Entity\Log;

class LogRawEvent extends LogEvent
{

    public string $message;

    protected function prepareLog(): Log
    {
        $log = parent::prepareLog();
        $log->type = LogTypeEnum::RAW;
        //
        $log->payload['message'] = $this->message;

        return $log;
    }

}
