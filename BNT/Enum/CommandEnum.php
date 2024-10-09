<?php

declare(strict_types=1);

namespace BNT\Enum;

enum CommandEnum: string
{
    case go = 'GO';
    case hostile = 'HOSTILE';
    case breakTurns = 'BREAK-TURNS';
    case breakSector = 'BREAK-SECTORS';
}
