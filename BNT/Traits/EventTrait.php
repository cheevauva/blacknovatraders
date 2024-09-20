<?php

declare(strict_types=1);

namespace BNT\Traits;

use BNT\Log\Log;
use BNT\Log\DAO\LogCreateDAO;

trait EventTrait
{

    public function dispatch(): void
    {
        if ($this instanceof Log) {
            LogCreateDAO::call($this); // @todo replace on event dispatcher
        }
    }

}
