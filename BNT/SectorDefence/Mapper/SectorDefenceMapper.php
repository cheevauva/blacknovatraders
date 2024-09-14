<?php

declare(strict_types=1);

namespace BNT\SectorDefence\Mapper;

use BNT\ServantInterface;
use BNT\SectorDefence\SectorDefence;
use BNT\SectorDefence\SectorDefenceFmSettingEnum;
use BNT\SectorDefence\SectorDefenceTypeEnum;

class SectorDefenceMapper implements ServantInterface
{

    public ?array $row = null;
    public ?SectorDefence $defence = null;

    public function serve(): void
    {
        if (empty($this->defence) && !empty($this->row)) {
            $this->defence = new SectorDefence;
            $this->defence->defence_id = intval($this->row['defence_id']);
            $this->defence->defence_type = SectorDefenceTypeEnum::tryFrom($this->row['defence_type']);
            $this->defence->fm_setting = SectorDefenceFmSettingEnum::tryFrom($this->row['fm_setting']);
            $this->defence->ship_id = intval($this->row['ship_id']);
            $this->defence->sector_id = intval($this->row['sector_id']);
            $this->defence->quantity = intval($this->row['quantity']);
        }

        if (!empty($this->defence) && empty($this->row)) {
            $this->row = [];
            $this->row['defence_id'] = $this->defence->defence_id;
            $this->row['defence_type'] = $this->defence->defence_type->val();
            $this->row['fm_setting'] = $this->defence->fm_setting->val();
            $this->row['ship_id'] = $this->defence->ship_id;
            $this->row['sector_id'] = $this->defence->sector_id;
            $this->row['quantity'] = $this->defence->quantity;
        }
    }

}
