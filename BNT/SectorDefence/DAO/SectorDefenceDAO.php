<?php

declare(strict_types=1);

namespace BNT\SectorDefence\DAO;

use BNT\DAO;

use BNT\SectorDefence\Mapper\SectorDefenceMapper;
use BNT\SectorDefence\Entity\SectorDefence;
use BNT\Enum\TableEnum;


abstract class SectorDefenceDAO extends DAO
{
    

    
    protected function table(): string
    {
        return TableEnum::SectorDefences->toDb();
    }

    protected function mapper(): SectorDefenceMapper
    {
        return new SectorDefenceMapper;
    }

    protected function asSectorDefence(array $row): SectorDefence
    {
        $mapper = $this->mapper();
        $mapper->row = $row;
        $mapper->serve();

        return $mapper->defence;
    }

    protected function asSectorDefences(array $rows): array
    {
        $sectorDefences = [];
        
        foreach ($rows as $row) {
            $sectorDefences[] = $this->asSectorDefence($row);
        }
        
        return $sectorDefences;
    }

    protected function asRow(SectorDefence $defence): array
    {
        $mapper = $this->mapper();
        $mapper->defence = $defence;
        $mapper->serve();

        return $mapper->row;
    }
}
