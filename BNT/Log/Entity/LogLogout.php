<?php

declare(strict_types=1);

namespace BNT\Log\Entity;

use BNT\Log\Enum\LogTypeEnum;

class LogLogout extends LogWithIP
{

    public LogTypeEnum $type = LogTypeEnum::LOGOUT;

}
