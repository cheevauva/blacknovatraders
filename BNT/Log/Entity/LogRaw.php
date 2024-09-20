<?php

declare(strict_types=1);

namespace BNT\Log\Entity;

use BNT\Log\Enum\LogTypeEnum;

class LogRaw extends Log
{

    public LogTypeEnum $type = LogTypeEnum::RAW;
}
