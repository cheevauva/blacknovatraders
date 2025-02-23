<?php

declare(strict_types=1);

namespace BNT\Log\Event;

use BNT\Log\Enum\LogTypeEnum;
use BNT\Log\Entity\Log;

class LogBountyCancelledEvent extends LogEvent
{

    public int $amount;
    public string $characterName;

    protected function prepareLog(): Log
    {
        $log = parent::prepareLog();
        $log->type = LogTypeEnum::BOUNTY_CANCELLED;
        //
        $log->payload['amount'] = $this->amount;
        $log->payload['characterName'] = $this->characterName;

        return $log;
    }

}
