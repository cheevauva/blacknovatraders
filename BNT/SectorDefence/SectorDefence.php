<?php

declare(strict_types=1);

namespace BNT\SectorDefence;

class SectorDefence
{

    public int $defence_id;
    public int $ship_id;
    public int $sector_id;
    public SectorDefenceTypeEnum $defence_type = SectorDefenceTypeEnum::Mines;
    public int $quantity;
    public SectorDefenceFmSettingEnum $fm_setting = SectorDefenceFmSettingEnum::Toll;

}
