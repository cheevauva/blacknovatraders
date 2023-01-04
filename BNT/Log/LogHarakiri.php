<?php

declare(strict_types=1);

namespace BNT\Log;

class LogHarakiri extends LogWithIP
{

    public LogTypeEnum $type = LogTypeEnum::HARAKIRI;

}
