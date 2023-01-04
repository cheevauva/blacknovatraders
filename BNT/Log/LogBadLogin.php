<?php

declare(strict_types=1);

namespace BNT\Log;

class LogBadLogin extends LogWithIP
{

    public LogTypeEnum $type = LogTypeEnum::BADLOGIN;

}
