<?php

declare(strict_types=1);

namespace BNT\Zone\DAO;

use BNT\ServantInterface;
use BNT\Traits\DatabaseTrait;
use BNT\Enum\TableEnum;
use BNT\Zone\Mapper\ZoneMapper;
use BNT\Zone\Entity\Zone;
use BNT\Traits\BuildTrait;

abstract class ZoneDAO implements ServantInterface
{
    use DatabaseTrait;
    use BuildTrait;

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
