<?php

declare(strict_types=1);

namespace BNT\Ship;

use DateTime;

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
    public int $credits = 0;
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

}
