<?php

declare(strict_types=1);

namespace BNT\Log\Event;

use BNT\Log\Entity\Log;

class LogWithIpEvent extends LogEvent
{

    public string $ip;

    protected function prepareLog(): Log
    {
        $log = parent::prepareLog();
        //
        $log->payload['ip'] = $this->ip;

        return $log;
    }

}
