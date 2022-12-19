<?php

declare(strict_types=1);

namespace BNT\Ship;

class Ship
{

    public int $id;
    public string $language;
    public string $ip;
    public string $password;
    public bool $isDestroyed = false;
    public \DateTime $dateLastLogin;
    public bool $hasEscapePod;
    public int $hull = 0;
    public int $engines = 0;
    public int $power = 0;
    public int $computer = 0;
    public int $sensors = 0;
    public int $beams = 0;
    public int $torpLaunchers = 0;
    public int $torps = 0;
    public int $armor = 0;
    public int $armorPts = 0;
    public int $cloak = 0;
    public int $shields = 0;
    public int $sector = 0;
    public int $shipOre = 0;
    public int $shipOrganics = 0;
    public int $shipEnergy = 0;
    public int $shipColonists = 0;
    public int $shipGoods = 0;
    public int $shipFighters = 0;
    public int $shipDamage = 0;
    public bool $onPlanet = false;
    public int $dev_warpedit = 0;
    public int $dev_genesis = 0;
    public int $dev_beacon = 0;
    public int $dev_emerwarp = 0;
    public bool $dev_fuelscoop = false;
    public int $dev_minedeflector = 0;
    public bool $dev_lssd = false;
    public int $credits = 0;

    public function __construct()
    {
        $this->dateLastLogin = new \DateTime;
    }

    protected function reset(): void
    {
        $this->hull = 0;
        $this->engines = 0;
        $this->power = 0;
        $this->computer = 0;
        $this->sensors = 0;
        $this->beams = 0;
        $this->torpLaunchers = 0;
        $this->torps = 0;
        $this->armor = 0;
        $this->armorPts = 100;
        $this->cloak = 0;
        $this->shields = 0;
        $this->sector = 0;
        $this->shipOre = 0;
        $this->shipOrganics = 0;
        $this->shipEnergy = 1000;
        $this->shipColonists = 0;
        $this->shipGoods = 0;
        $this->shipFighters = 100;
        $this->shipDamage = 0;
        $this->onPlanet = false;
        $this->isDestroyed = false;
        $this->hasEscapePod = false;
        $this->dev_warpedit = 0;
        $this->dev_genesis = 0;
        $this->dev_beacon = 0;
        $this->dev_emerwarp = 0;
        $this->dev_fuelscoop = false;
        $this->dev_minedeflector = 0;
        $this->dev_lssd = false;
    }

    public function resetWithEscapePod(): void
    {
        $this->reset();
    }

    public function resetWithoutEscapePod(): void
    {
        $this->reset();
        $this->credits = 1000;
    }

}
