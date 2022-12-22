<?php

declare(strict_types=1);

namespace BNT\Sector;

enum SectorPortTypeEnum: string
{

    case Ore = 'ore';
    case Organics = 'organics';
    case Goods = 'goods';
    case Energy = 'energy';
    case None = 'none';
    case Special = 'special';

}
