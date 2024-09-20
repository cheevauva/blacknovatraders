<?php

declare(strict_types=1);

namespace BNT\Traderoute\View;

use BNT\Traderoute\Traderoute;
use BNT\Traderoute\TraderouteCircuitEnum;
use BNT\Traderoute\TraderouteTypeEnum;
use BNT\Planet\DAO\PlanetRetrieveByIdDAO;
use BNT\Planet\View\PlanetView;

class TraderouteView
{
    private Traderoute $traderoute;

    public function __construct(Traderoute $traderoute)
    {
        $this->traderoute = $traderoute;
    }

    public function id(): int
    {
        return $this->traderoute->traderoute_id;
    }

    public function direction(): string
    {
        return match ($this->traderoute->circuit) {
            TraderouteCircuitEnum::One => '=>',
            TraderouteCircuitEnum::Two => '<=>',
        };
    }

    public function src(): string
    {
        global $l_port;
        global $l_defense;

        return match ($this->traderoute->source_type) {
            TraderouteTypeEnum::Port => $l_port,
            TraderouteTypeEnum::Defense => $l_defense,
            TraderouteTypeEnum::Personal, TraderouteTypeEnum::Corperate => (new PlanetView(PlanetRetrieveByIdDAO::call($this->traderoute->source_id)))->name(),
        };
    }

    public function dst(): string
    {
        global $l_defense;

        return match ($this->traderoute->dest_type) {
            TraderouteTypeEnum::Port => strval($this->traderoute->dest_id),
            TraderouteTypeEnum::Defense => sprintf('%s [%s]', $l_defense, $this->traderoute->dest_id),
            TraderouteTypeEnum::Personal, BNT\Traderoute\TraderouteTypeEnum::Corperate => (new PlanetView(PlanetRetrieveByIdDAO::call($this->traderoute->dest_id)))->name(),
        };
    }

    public static function map(array $traderoutes): array
    {
        return array_map(function ($traderoute) {
            return new static($traderoute);
        }, $traderoutes);
    }
}
