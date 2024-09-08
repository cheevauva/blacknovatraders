<?php

declare(strict_types=1);

namespace BNT\Log;

use BNT\Log\LogTypeEnum;

class LogBountyCancelled extends Log
{

    public LogTypeEnum $type = LogTypeEnum::BOUNTY_CANCELLED;

}
