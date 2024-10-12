<?php

declare(strict_types=1);

namespace BNT\Log\Entity;

use BNT\Traits\EventTrait;
use BNT\Traits\AsTrait;
use BNT\Log\Enum\LogTypeEnum;

abstract class Log
{
    use EventTrait;
    use AsTrait;

    public int $log_id;
    public int $ship_id = 0;
    public LogTypeEnum $type = LogTypeEnum::UNDEFINED;
    public ?\DateTime $time = null;
    public $message;

    public function __construct()
    {
        $this->time = new \DateTime();
    }
}
