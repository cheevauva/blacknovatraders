<?php

declare(strict_types=1);

namespace BNT;

enum TableEnum
{

    case Ships;
    case Traderoutes;
    case Links;
    case Planets;
    case Sectors;
    case Zones;
    case SectorDefences;
    case Bounty;

    public function toDb(): string
    {
        global $dbtables;

        return match ($this) {
            TableEnum::Ships => $dbtables['ships'],
            TableEnum::Traderoutes => $dbtables['traderoutes'],
            TableEnum::Links => $dbtables['links'],
            TableEnum::Planets => $dbtables['planets'],
            TableEnum::Sectors => $dbtables['universe'],
            TableEnum::Zones => $dbtables['zones'],
            TableEnum::SectorDefences => $dbtables['sector_defence'],
            TableEnum::Bounty => $dbtables['bounty'],
        };
    }

}
