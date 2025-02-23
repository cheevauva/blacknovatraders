<?php

declare(strict_types=1);

namespace BNT\Log\Event;

use BNT\Log\Enum\LogTypeEnum;
use BNT\Log\Entity\Log;

class LogTollPaidEvent extends LogEvent
{

    public int $sector;
    public int $fightersToll;

    protected function prepareLog(): Log
    {
        $log = parent::prepareLog();
        $log->type = LogTypeEnum::TOLL_PAID;
        //
        $log->payload['sector'] = $this->sector;
        $log->payload['fightersToll'] = $this->fightersToll;

        return $log;
    }

}
