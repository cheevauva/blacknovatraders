<?php

declare(strict_types=1);

namespace BNT\SectorDefence\View;

use BNT\SectorDefence\Entity\SectorDefence;
use BNT\Ship\DAO\ShipRetrieveByIdDAO;
use BNT\Ship\View\ShipView;
use BNT\SectorDefence\Enum\SectorDefenceTypeEnum;
use BNT\SectorDefence\Enum\SectorDefenceFmSettingEnum;

class SectorDefenceView
{
    protected SectorDefence $sectorDefence;

    public function __construct(SectorDefence $sectorDefence)
    {
        $this->sectorDefence = $sectorDefence;
    }

    public function id(): int
    {
        return $this->sectorDefence->defence_id;
    }

    public function type(): string
    {
        return strtolower($this->sectorDefence->defence_type->value);
    }

    public function name(): string
    {
        global $l_mines;
        global $l_fighters;
        global $l_md_toll;
        global $l_md_attack;

        return $this->sectorDefence->quantity . ' ' . match ($this->sectorDefence->defence_type) {
            SectorDefenceTypeEnum::Mines => $l_mines,
            SectorDefenceTypeEnum::Fighters => $l_fighters . ' ' . match ($this->sectorDefence->fm_setting) {
                SectorDefenceFmSettingEnum::Attack => $l_md_attack,
                SectorDefenceFmSettingEnum::Toll => $l_md_toll,
            },
        };
    }

    public function shipname(): string
    {
        return (new ShipView(ShipRetrieveByIdDAO::call($this->sectorDefence->ship_id)))->name();
    }

    public static function map(array $sectorDefences): array
    {
        return array_map(function ($sectorDefence) {
            return new static($sectorDefence);
        }, $sectorDefences);
    }
}
