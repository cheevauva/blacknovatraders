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

    public function defenceSectorLabel(): string
    {
        global $l_mines;
        global $l_fighters;
        global $l_md_toll;
        global $l_md_attack;

        return $this->quantity . ' ' . match ($this->defence_type) {
            SectorDefenceTypeEnum::Mines => $l_mines,
            SectorDefenceTypeEnum::Fighters => $l_fighters . ' ' . match ($this->fm_setting) {
                SectorDefenceFmSettingEnum::Attack => $l_md_attack,
                SectorDefenceFmSettingEnum::Toll => $l_md_toll,
            },
        };
    }

}
