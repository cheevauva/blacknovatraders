<?php

declare(strict_types=1);

namespace BNT\Log;

class LogLogin extends LogWithIP
{

    public LogTypeEnum $type = LogTypeEnum::LOGIN;

}
