<?php

declare(strict_types=1);

namespace BNT\Ship\View;

use BNT\Ship\Ship;

class ShipView
{

    protected Ship $ship;

    public function __construct(Ship $ship)
    {
        $this->ship = $ship;
    }

    public function rank(): string
    {
        return player_insignia_name($this->ship);
    }

    public function id(): int
    {
        return $this->ship->ship_id;
    }

    public function name(): string
    {
        global $l_unnamed;

        return $this->ship->ship_name ?: $l_unnamed;
    }

    public function character(): string
    {
        return $this->ship->character_name;
    }

    public function turnsUsed(): string
    {
        return NUMBER($this->ship->turns_used);
    }

    public function turns(): string
    {
        return NUMBER($this->ship->turns);
    }

    public function score(): string
    {
        return NUMBER($this->ship->score);
    }

    public function sector(): string
    {
        return strval($this->ship->sector);
    }

    public function rating(): string
    {
        return strval($this->ship->rating);
    }

    public function team(): string
    {
        return '!!';
    }

    public function ore(): string
    {
        return NUMBER($this->ship->ship_ore);
    }

    public function organics(): string
    {
        return NUMBER($this->ship->ship_organics);
    }

    public function goods(): string
    {
        return NUMBER($this->ship->ship_goods);
    }

    public function credits(): string
    {
        return NUMBER($this->ship->credits);
    }

    public function colonists(): string
    {
        return NUMBER($this->ship->ship_colonists);
    }

    public function energy(): string
    {
        return NUMBER($this->ship->ship_energy);
    }

    public function level(): int
    {
        return $this->ship->getLevel();
    }

    public function preset(int $preset): int
    {
        return match ($preset) {
            1 => $this->ship->preset1,
            2 => $this->ship->preset2,
            3 => $this->ship->preset3,
        };
    }

    public function isDisplayed(): bool
    {
        $success = SCAN_SUCCESS($this->ship->sensors, $this->ship->cloak);
        $success = $success < 5 ? 5 : $success;
        $success = $success < 95 ? 95 : $success;

        return rand(1, 100) < $success;
    }

    public static function map(array $ships): array
    {
        return array_map(function ($ship) {
            return new static($ship);
        }, $ships);
    }

}
