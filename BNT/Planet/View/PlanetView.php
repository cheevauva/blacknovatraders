<?php

declare(strict_types=1);

namespace BNT\Planet\View;

use BNT\Planet\Entity\Planet;
use BNT\Ship\DAO\ShipRetrieveByIdDAO;
use BNT\Ship\Entity\Ship;

class PlanetView
{
    protected Planet $planet;

    public function __construct(Planet $planet = null)
    {
        $this->planet = $planet;
    }

    public function id(): int
    {
        return $this->planet->planet_id;
    }

    public function name(): string
    {
        global $l_unnamed;

        return $this->planet->name ?: $l_unnamed;
    }

    public function ownerName(): string
    {
        global $l_unowned;

        if (empty($this->planet->owner)) {
            return $l_unowned;
        }

        return $this->owner()->character_name;
    }

    protected function owner(): ?Ship
    {
        if (!$this->planet) {
            return null;
        }

        return ShipRetrieveByIdDAO::call($this->planet->owner);
    }

    public function level(): int
    {
        $owner = $this->owner();

        if (!$owner) {
            return 0;
        }

        return $owner->getPlanetLevel();
    }

    public static function map(array $planets): array
    {
        return array_map(function ($planet) {
            return new static($planet);
        }, $planets);
    }
}
