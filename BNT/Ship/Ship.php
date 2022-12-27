<?php

declare(strict_types=1);

namespace BNT\Ship;

use DateTime;
use BNT\Ship\Exception\ShipException;
use BNT\Ship\ShipResourceEnum;

class Ship
{

    public int $ship_id;
    public string $ship_name;
    public bool $ship_destroyed = false;
    public string $character_name;
    public string $password;
    public string $email;
    public int $hull = 0;
    public int $engines = 0;
    public int $power = 0;
    public int $computer = 0;
    public int $sensors = 0;
    public int $beams = 0;
    public int $torp_launchers = 0;
    public int $torps = 0;
    public int $shields = 0;
    public int $armor = 0;
    public int $armor_pts = 0;
    public int $cloak = 0;
    public $credits = 0; // @todo int in db
    public int $sector = 0;
    public int $ship_ore = 0;
    public int $ship_organics = 0;
    public int $ship_goods = 0;
    public int $ship_energy = 0;
    public int $ship_colonists = 0;
    public int $ship_fighters = 0;
    public int $ship_damage = 0;
    public int $turns = 0;
    public bool $on_planet = false;
    public int $dev_warpedit = 0;
    public int $dev_genesis = 0;
    public int $dev_beacon = 0;
    public int $dev_emerwarp = 0;
    public bool $dev_escapepod = false;
    public bool $dev_fuelscoop = false;
    public int $dev_minedeflector = 0;
    public int $turns_used = 0;
    public DateTime $last_login;
    public int $rating = 0;
    public int $score = 0;
    public int $team = 0;
    public int $team_invite = 0;
    public string $interface = 'N';
    public string $ip_address = '1.1.1.1';
    public int $planet_id = 0;
    public int $preset1 = 0;
    public int $preset2 = 0;
    public int $preset3 = 0;
    public bool $trade_colonists = true;
    public bool $trade_fighters = false;
    public bool $trade_torps = false;
    public bool $trade_energy = true;
    public ?string $cleared_defences = null;
    public string $lang;
    public bool $dhtml = true;
    public bool $dev_lssd = true;

    public function __construct()
    {
        $this->last_login = new \DateTime;
    }

    protected function reset(): void
    {
        $this->hull = 0;
        $this->engines = 0;
        $this->power = 0;
        $this->computer = 0;
        $this->sensors = 0;
        $this->beams = 0;
        $this->torp_launchers = 0;
        $this->torps = 0;
        $this->armor = 0;
        $this->armor_pts = 100;
        $this->cloak = 0;
        $this->shields = 0;
        $this->sector = 0;
        $this->ship_ore = 0;
        $this->ship_organics = 0;
        $this->ship_energy = 1000;
        $this->ship_colonists = 0;
        $this->ship_goods = 0;
        $this->ship_fighters = 100;
        $this->ship_damage = 0;
        $this->ship_destroyed = false;
        $this->on_planet = false;
        $this->dev_escapepod = false;
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

    public function getPlanetLevel(): int
    {
        $planetavg = array_sum([
            $this->hull,
            $this->engines,
            $this->computer,
            $this->beams,
            $this->torp_launchers,
            $this->shields,
            $this->armor
        ]);
        $planetavg /= 7;

        if ($planetavg < 8) {
            return 0;
        } else if ($planetavg < 12) {
            return 1;
        } else if ($planetavg < 16) {
            return 2;
        } else if ($planetavg < 20) {
            return 3;
        } else {
            return 4;
        }
    }

    public function getLevel(): int
    {
        $shipavg = $this->hull + $this->engines + $this->power + $this->computer + $this->sensors + $this->armor + $this->shields + $this->beams + $this->torp_launchers + $this->cloak;
        $shipavg /= 10;

        if ($shipavg < 8) {
            return 0;
        } else if ($shipavg < 12) {
            return 1;
        } else if ($shipavg < 16) {
            return 2;
        } else if ($shipavg < 20) {
            return 3;
        } else {
            return 4;
        }
    }

    public function getFreeResourceForBuying(ShipResourceEnum $resource)
    {
        return match ($resource) {
            ShipResourceEnum::Energy => $this->getFreePower(),
            ShipResourceEnum::Ore => NUM_HOLDS($this->hull) - $this->ship_ore - $this->ship_colonists,
            ShipResourceEnum::Organics => NUM_HOLDS($this->hull) - $this->ship_organics - $this->ship_colonists,
            ShipResourceEnum::Goods => NUM_HOLDS($this->hull) - $this->ship_goods - $this->ship_colonists,
        };
    }

    public function getFreeResourceForSelling(ShipResourceEnum $resource)
    {
        return match ($resource) {
            ShipResourceEnum::Energy => $this->ship_energy,
            ShipResourceEnum::Ore => $this->ship_ore,
            ShipResourceEnum::Organics => $this->ship_organics,
            ShipResourceEnum::Goods => $this->ship_goods,
        };
    }

    public function getFreeHolds(): float
    {
        return NUM_HOLDS($this->hull) - $this->ship_ore - $this->ship_organics - $this->ship_goods - $this->ship_colonists;
    }

    public function getFreePower(): float
    {
        return NUM_ENERGY($this->power) - $this->ship_energy;
    }

    public function getFighterMax()
    {
        $fighterMax = NUM_FIGHTERS($this->computer) - $this->ship_fighters;

        if ($fighterMax < 0) {
            $fighterMax = 0;
        }

        return $fighterMax;
    }

    public function getTorpedoesMax()
    {
        $torpedoMax = NUM_TORPEDOES($this->torp_launchers) - $this->torps;

        if ($torpedoMax < 0) {
            $torpedoMax = 0;
        }

        return $torpedoMax;
    }

    public function getArmorMax()
    {
        $armorMax = NUM_ARMOUR($this->armor) - $this->armor_pts;

        if ($armorMax < 0) {
            $armorMax = 0;
        }

        return $armorMax;
    }

    public function getColonistMax()
    {
        $colonistMax = $this->getFreeHolds();

        if ($colonistMax < 0) {
            $colonistMax = 0;
        }

        return $colonistMax;
    }

    public function turn(): void
    {
        if ($this->turns < 1) {
            throw ShipException::notAllowTurn();
        }

        $this->turns--;
        $this->turns_used++;
    }

    private function setAmount(ShipResourceEnum $resource, $amount)
    {
        match ($resource) {
            ShipResourceEnum::Energy => $this->ship_energy = $amount,
            ShipResourceEnum::Ore => $this->ship_ore = $amount,
            ShipResourceEnum::Organics => $this->ship_organics = $amount,
            ShipResourceEnum::Goods => $this->ship_goods = $amount,
        };
    }

    public function sell(ShipResourceEnum $resource, $amount): void
    {
        $current = $this->amount($resource);

        if ($current < $amount) {
            throw ShipException::notEnoughResourceForSelling($resource, $current, $amount);
        }

        switch ($resource) {
            case ShipResourceEnum::Energy:
            case ShipResourceEnum::Ore:
            case ShipResourceEnum::Organics:
            case ShipResourceEnum::Goods:
                $this->setAmount($resource, $current - $amount);
                break;
        }
    }

    public function buy(ShipResourceEnum $resource, $amount): void
    {
        $current = $this->amount($resource);

        switch ($resource) {
            case ShipResourceEnum::Energy:
                if ($amount > $this->getFreePower()) {
                    throw ShipException::notEnoughPowerForPurchase($this->getFreePower(), $amount);
                }
                break;
        }

        switch ($resource) {
            case ShipResourceEnum::Energy:
            case ShipResourceEnum::Ore:
            case ShipResourceEnum::Organics:
            case ShipResourceEnum::Goods:
                $this->setAmount($resource, $current + $amount);
                break;
        }
    }

    public function amount(ShipResourceEnum $resource)
    {
        return match ($resource) {
            ShipResourceEnum::Energy => $this->ship_energy,
            ShipResourceEnum::Ore => $this->ship_ore,
            ShipResourceEnum::Organics => $this->ship_organics,
            ShipResourceEnum::Goods => $this->ship_goods,
        };
    }

    public function acceptPayment($cost): void
    {
        $this->credits += $cost;
    }

    public function payment($cost): void
    {
        if ($this->credits < $cost) {
            throw ShipException::notEnoughCredits($this->credits, $cost);
        }

        $this->turn();
        $this->credits -= $cost;
    }
    
    public function password(string $password): void
    {
        $this->password = password_hash($password, PASSWORD_DEFAULT);
    }

}
