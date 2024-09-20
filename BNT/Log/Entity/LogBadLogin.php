<?php

declare(strict_types=1);

namespace BNT\Log\Entity;

use BNT\Log\Enum\LogTypeEnum;

class LogBadLogin extends LogWithIP
{
    public LogTypeEnum $type = LogTypeEnum::BADLOGIN;
}
