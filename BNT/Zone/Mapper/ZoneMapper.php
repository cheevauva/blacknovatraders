<?php

declare(strict_types=1);

namespace BNT\Zone\Mapper;

use BNT\ServantInterface;
use BNT\Zone\Zone;

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
            $zone->allow_trade = $this->row['allow_trade'] === 'L' ? null : toBool($this->row['allow_trade']);
            $zone->allow_defenses = $this->row['allow_defenses'] === 'L' ? null : toBool($this->row['allow_defenses']);
            $zone->allow_planet = $this->row['allow_planet'] === 'L' ? null : toBool($this->row['allow_planet']);
            $zone->allow_warpedit = $this->row['allow_warpedit'] === 'L' ? null : toBool($this->row['allow_warpedit']);
            $zone->allow_beacon = $this->row['allow_beacon'] === 'L' ? null : toBool($this->row['allow_beacon']);
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
