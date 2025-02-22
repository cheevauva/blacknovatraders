<?php

declare(strict_types=1);

namespace BNT\Ship\DAO;

use BNT\DAO;

use BNT\Ship\Mapper\ShipMapper;
use BNT\Enum\TableEnum;
use BNT\Ship\Entity\Ship;


abstract class ShipDAO extends DAO
{
    


    protected function table(): string
    {
        return TableEnum::Ships->toDb();
    }

    protected function mapper(): ShipMapper
    {
        return new ShipMapper;
    }

    protected function asShips(array $rows): array
    {
        $ships = [];

        foreach ($rows as $row) {
            $ships[] = $this->asShip($row);
        }

        return $ships;
    }

    protected function asShip(array $row): ?Ship
    {
        $mapper = $this->mapper();
        $mapper->row = $row;
        $mapper->serve();

        return $mapper->ship;
    }
}
