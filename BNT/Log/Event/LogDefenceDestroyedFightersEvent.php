<?php

declare(strict_types=1);

namespace BNT\Log\Event;

use BNT\Log\Enum\LogTypeEnum;
use BNT\Log\Entity\Log;

class LogDefenceDestroyedFightersEvent extends LogEvent
{

    public int $fighterslost;
    public int $sector;

    protected function prepareLog(): Log
    {
        $log = parent::prepareLog();
        $log->type = LogTypeEnum::DEFS_DESTROYED_F;
        //
        $log->payload['fighterslost'] = $this->fighterslost;
        $log->payload['sector'] = $this->sector;

        return $log;
    }

}
