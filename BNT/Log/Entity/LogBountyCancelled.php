<?php

declare(strict_types=1);

namespace BNT\Log\Entity;

use BNT\Log\Enum\LogTypeEnum;

class LogBountyCancelled extends Log
{

    public LogTypeEnum $type = LogTypeEnum::BOUNTY_CANCELLED;
    public int $amount;
    public string $characterName;

}
