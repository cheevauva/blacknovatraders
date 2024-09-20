<?php

declare(strict_types=1);

namespace BNT\Log\Entity;

use BNT\Log\Enum\LogTypeEnum;

class LogTollRecieve extends Log
{
    public LogTypeEnum $type = LogTypeEnum::TOLL_RECV;
    public int $tollAmount;
    public int $sector;
}
