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
