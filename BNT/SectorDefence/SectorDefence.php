<?php

declare(strict_types=1);

namespace BNT\SectorDefence;

use BNT\SectorDefence\Enum\SectorDefenceFmSettingEnum;
use BNT\SectorDefence\Enum\SectorDefenceTypeEnum;

class SectorDefence
{
    use \BNT\Traits\AsTrait;

    public ?int $defence_id = null;
    public int $ship_id;
    public int $sector_id;
    public SectorDefenceTypeEnum $defence_type = SectorDefenceTypeEnum::Mines;
    public int $quantity = 0;
    public SectorDefenceFmSettingEnum $fm_setting = SectorDefenceFmSettingEnum::Toll;
}
