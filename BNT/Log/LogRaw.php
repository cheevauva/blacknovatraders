<?php

declare(strict_types=1);

namespace BNT\Log;

use BNT\Log\LogTypeEnum;

class LogRaw extends Log
{

    public LogTypeEnum $type = LogTypeEnum::RAW;
    public string $message;

}
