<?php

declare(strict_types=1);

namespace BNT\Zone\DAO;

use BNT\DAO;

use BNT\Enum\TableEnum;
use BNT\Zone\Mapper\ZoneMapper;
use BNT\Zone\Entity\Zone;


abstract class ZoneDAO extends DAO
{

    


    protected function table(): string
    {
        return TableEnum::Zones->toDb();
    }

    protected function mapper(): ZoneMapper
    {
        return new ZoneMapper;
    }

    protected function asZones(array $rows): array
    {
        $zones = [];

        foreach ($rows as $row) {
            $zones[] = $this->asZone($row);
        }

        return $zones;
    }

    protected function asZone(array $row): Zone
    {
        $mapper = $this->mapper();
        $mapper->row = $row;
        $mapper->serve();

        return $mapper->zone;
    }

}
