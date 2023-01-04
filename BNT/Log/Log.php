<?php

declare(strict_types=1);

namespace BNT\Log;

use BNT\EventTrait;

abstract class Log
{

    use EventTrait;

    public int $log_id;
    public int $ship_id = 0;
    public LogTypeEnum $type = LogTypeEnum::UNDEFINED;
    public ?\DateTime $time = null;
    
    public function __construct()
    {
        $this->time = new \DateTime;
    }

}
