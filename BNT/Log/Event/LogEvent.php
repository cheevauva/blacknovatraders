<?php

declare(strict_types=1);

namespace BNT\Log\Event;

use BNT\Log\DAO\LogCreateDAO;
use BNT\Log\Entity\Log;

abstract class LogEvent extends \BNT\Event
{

    public int $shipId;

    public function to(object $object): void
    {
        parent::to($object);

        if ($object instanceof LogCreateDAO) {
            $object->log = $this->prepareLog();
        }
    }

    protected function prepareLog(): Log
    {
        $log = new Log();
        $log->ship_id = $this->shipId;
        //
        $log->payload['shipId'] = $this->shipId;

        return $log;
    }

}
