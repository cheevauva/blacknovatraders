<?php

declare(strict_types=1);

namespace BNT\SectorDefence;

enum SectorDefenceFmSettingEnum: string
{

    case Toll = 'toll';
    case Attack = 'attack';

    public function val(): string
    {
        return match ($this) {
            SectorDefenceFmSettingEnum::Toll => 'toll',
            SectorDefenceFmSettingEnum::Attack => 'attack',
        };
    }

}
