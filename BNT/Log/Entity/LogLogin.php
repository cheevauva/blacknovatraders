<?php

declare(strict_types=1);

namespace BNT\Log\Entity;

use BNT\Log\Enum\LogTypeEnum;

class LogLogin extends LogWithIP
{

    public LogTypeEnum $type = LogTypeEnum::LOGIN;

}
