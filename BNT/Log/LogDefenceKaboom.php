<?php

declare(strict_types=1);

namespace BNT\Log;

use BNT\Log\LogTypeEnum;

class LogDefenceKaboom extends Log
{

    public LogTypeEnum $type = LogTypeEnum::UNDEFINED;
    public int $sector;
    public bool $dev_escapepod;

}
