<?php

declare(strict_types=1);

namespace BNT\SectorDefence\Mapper;

use BNT\ServantInterface;
use BNT\SectorDefence\SectorDefence;
use BNT\SectorDefence\SectorDefenceFmSettingEnum;
use BNT\SectorDefence\SectorDefenceTypeEnum;

class SectorDefenceMapper implements ServantInterface
{

    public array $row;
    public ?SectorDefence $defence = null;

    public function serve(): void
    {
        if (empty($this->defence) && !empty($this->row)) {
            $defence = $this->defence = new SectorDefence;
            $defence->defence_id = intval($this->row['defence_id']);
            $defence->defence_type = SectorDefenceTypeEnum::tryFrom($this->row['defence_type']);
            $defence->fm_setting = SectorDefenceFmSettingEnum::tryFrom($this->row['fm_setting']);
            $defence->ship_id = intval($this->row['ship_id']);
            $defence->sector_id = intval($this->row['sector_id']);
            $defence->quantity = intval($this->row['quantity']);
        }

        if (!empty($this->defence) && empty($this->row)) {
            $defence = $this->defence;
            $row = [];
            $row['defence_id'] = $defence->defence_id;

            $this->row = $row;
        }
    }

}
