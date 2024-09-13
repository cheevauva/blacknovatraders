<?php

declare(strict_types=1);

namespace BNT\Log;

class LogTollPaid extends Log
{

    public LogTypeEnum $type = LogTypeEnum::TOLL_PAID;
    public int $sector;
    public int $fightersToll;

}
