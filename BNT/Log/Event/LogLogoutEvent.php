<?php

declare(strict_types=1);

namespace BNT\Log\Event;

use BNT\Log\Enum\LogTypeEnum;
use BNT\Log\Entity\Log;

class LogLogoutEvent extends LogWithIpEvent
{

    protected function prepareLog(): Log
    {
        $log = parent::prepareLog();
        $log->type = LogTypeEnum::LOGOUT;

        return $log;
    }

}
