<?php

declare(strict_types=1);

namespace BNT\Zone\Mapper;

use BNT\ServantInterface;
use BNT\Zone\Entity\Zone;
use BNT\Zone\Enum\ZoneAllowEnum;

class ZoneMapper implements ServantInterface
{
    public array $row;
    public ?Zone $zone = null;

    public function serve(): void
    {
        if (empty($this->zone) && !empty($this->row)) {
            $zone = $this->zone = new Zone;
            $zone->zone_id = intval($this->row['zone_id']);
            $zone->zone_name = $this->row['zone_name'];
            $zone->corp_zone = toBool($this->row['corp_zone']);
            $zone->owner = intval($this->row['owner']);
            $zone->allow_trade = ZoneAllowEnum::from($this->row['allow_trade']);
            $zone->allow_defenses = ZoneAllowEnum::from($this->row['allow_defenses']);
            $zone->allow_planet = ZoneAllowEnum::from($this->row['allow_planet']);
            $zone->allow_planetattack = ZoneAllowEnum::from($this->row['allow_planetattack']);
            $zone->allow_warpedit = ZoneAllowEnum::from($this->row['allow_warpedit']);
            $zone->allow_beacon = ZoneAllowEnum::from($this->row['allow_beacon']);
        }

        if (!empty($this->zone) && empty($this->row)) {
            $zone = $this->zone;
            $row = [];
            $row['zone_id'] = $zone->zone_id;
            $row['zone_name'] = $zone->zone_name;

            $this->row = $row;
        }
    }
}
