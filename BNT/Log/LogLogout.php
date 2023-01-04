<?php

declare(strict_types=1);

namespace BNT\Log;

class LogLogout extends LogWithIP
{

    public LogTypeEnum $type = LogTypeEnum::LOGOUT;

}
