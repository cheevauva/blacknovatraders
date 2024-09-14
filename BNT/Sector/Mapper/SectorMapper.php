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
            $sector->sector_name = strval($this->row['sector_name']);
            $sector->zone_id = $this->row['zone_id'];
            $sector->beacon = $this->row['beacon'];
            $sector->angle1 = floatval($this->row['angle1']);
            $sector->angle2 = floatval($this->row['angle2']);
            $sector->port_type = SectorPortTypeEnum::tryFrom($this->row['port_type']);
            $sector->port_energy = intval($this->row['port_energy']);
            $sector->port_goods = intval($this->row['port_goods']);
            $sector->port_ore = intval($this->row['port_ore']);
            $sector->port_organics = intval($this->row['port_organics']);
            $sector->distance = intval($this->row['distance']);
            $sector->fighters = intval($this->row['fighters']);
        }

        if (!empty($this->sector) && empty($this->row)) {
            $sector = $this->sector;
            $row = [];
            $row['sector_id'] = $sector->sector_id;

            $this->row = $row;
        }
    }

}
