<?php

declare(strict_types=1);

namespace BNT\Log\Entity;

use BNT\Log\Enum\LogTypeEnum;

class LogDefenceKaboom extends Log
{

    public LogTypeEnum $type = LogTypeEnum::UNDEFINED;
    public int $sector;
    public bool $dev_escapepod;

}
