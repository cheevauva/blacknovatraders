<?php

declare(strict_types=1);

namespace BNT\Log;

class LogTollRecieve extends Log
{

    public LogTypeEnum $type = LogTypeEnum::TOLL_RECV;
    public int $tollAmount;
    public int $sector;

}
