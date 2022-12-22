<?php

declare(strict_types=1);

namespace BNT\Sector\Mapper;

use BNT\ServantInterface;
use BNT\Sector\Sector;
use BNT\Sector\SectorPortTypeEnum;

class SectorMapper implements ServantInterface
{

    public array $row;
    public ?Sector $sector = null;

    public function serve(): void
    {
        if (empty($this->sector) && !empty($this->row)) {
            $sector = $this->sector = new Sector;
            $sector->sector_id = intval($this->row['sector_id']);
            $sector->zone_id = $this->row['zone_id'];
            $sector->port_type = SectorPortTypeEnum::tryFrom($this->row['port_type']);
        }

        if (!empty($this->sector) && empty($this->row)) {
            $sector = $this->sector;
            $row = [];
            $row['sector_id'] = $sector->sector_id;

            $this->row = $row;
        }
    }

}
