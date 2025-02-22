<?php

declare(strict_types=1);

namespace BNT\Planet\DAO;

use BNT\DAO;

use BNT\Enum\TableEnum;
use BNT\Planet\Mapper\PlanetMapper;
use BNT\Planet\Entity\Planet;


abstract class PlanetDAO extends DAO
{
    


    protected function table(): string
    {
        return TableEnum::Planets->toDb();
    }

    protected function mapper(): PlanetMapper
    {
        return new PlanetMapper;
    }

    protected function asPlanet(array $row): ?Planet
    {
        if (empty($row)) {
            return null;
        }

        $mapper = $this->mapper();
        $mapper->row = $row;
        $mapper->serve();

        return $mapper->planet;
    }

    protected function asPlanets(array $rows): array
    {
        $planets = [];

        foreach ($rows as $row) {
            $planets[] = $this->asPlanet($row);
        }

        return $planets;
    }

    protected function asRow(Planet $planet): array
    {
        $mapper = $this->mapper();
        $mapper->planet = $planet;
        $mapper->serve();

        return $mapper->row;
    }
}
