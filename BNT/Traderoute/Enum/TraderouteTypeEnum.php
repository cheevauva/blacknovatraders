<?php

declare(strict_types=1);

namespace BNT\Traderoute\Enum;

enum TraderouteTypeEnum: string
{
    case Port = 'P';
    case Defense = 'D';
    case Personal = 'L';
    case Corperate = 'C';
}
