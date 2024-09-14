<?php

declare(strict_types=1);

namespace BNT\SectorDefence;

enum SectorDefenceTypeEnum: string
{

    case Mines = 'M';
    case Fighters = 'F';

    public function val(): string
    {
        return match ($this) {
            SectorDefenceTypeEnum::Mines => 'M',
            SectorDefenceTypeEnum::Fighters => 'F',
        };
    }

}
