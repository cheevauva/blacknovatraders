<?php

declare(strict_types=1);

namespace BNT\Ship\Mapper;

use BNT\Ship\Entity\Ship;
use BNT\Math\DTO\MathShipDTO;

class ShipToMathShipMapper extends \BNT\Mapper
{

    public Ship $ship;
    public MathShipDTO $mathShip;

    public function serve(): void
    {
        $this->mathShip->armor = $this->ship->armor;
        $this->mathShip->beams = $this->ship->beams;
        $this->mathShip->cloak = $this->ship->cloak;
        $this->mathShip->computer = $this->ship->computer;
        $this->mathShip->engines = $this->ship->engines;
        $this->mathShip->hull = $this->ship->hull;
        $this->mathShip->power = $this->ship->power;
        $this->mathShip->sensors = $this->ship->sensors;
        $this->mathShip->shields = $this->ship->shields;
        $this->mathShip->torpLaunchers = $this->ship->torp_launchers;
        $this->mathShip->cloak = $this->ship->cloak;
        $this->mathShip->dev_minedeflector = $this->ship->dev_minedeflector;
        $this->mathShip->energy = $this->ship->ship_energy;
        $this->mathShip->armorPts = $this->ship->armor_pts;
        $this->mathShip->fighters = $this->ship->ship_fighters;
    }

}
