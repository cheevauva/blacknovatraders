<?php

declare(strict_types=1);

namespace BNT\Zone\Servant;

use BNT\Servant;
use BNT\Zone\Entity\Zone;
use BNT\Zone\Exception\ZoneException;
use BNT\Ship\Entity\Ship;
use BNT\Ship\DAO\ShipRetrieveByIdDAO;

class ZonePortTradeServant extends Servant
{
    public Zone $zone;
    public Ship $ship;

    public function serve(): void
    {
        $zone = $this->zone;
        $ship = $this->ship;

        if ($zone->zone_id == 4) {
            throw ZoneException::warzone();
        }

        if ($zone->allow_trade === true) {
            return;
        }

        if ($zone->allow_trade === false) {
            throw ZoneException::notAllowTrading();
        }

        if (!$this->isAllowForOutsiders($zone, $ship)) {
            throw ZoneException::notAllowTradingForOutsiders();
        }

        if ($ship->turns < 1) {
            global $l_trade_turnneed;
            throw new \Exception($l_trade_turnneed);
        }
    }

    private function isAllowForOutsiders(Zone $zone, Ship $ship): bool
    {
        if (empty($zone->corp_zone)) {
            $owner = ShipRetrieveByIdDAO::call($this->container, $zone->owner);

            if (empty($owner)) {
                return true;
            }

            if ($ship->team !== $owner->team) {
                return false;
            }

            if (empty($ship->team) && $ship->ship_id !== $zone->owner) {
                return false;
            }
        } else {
            if ($ship->team !== $zone->owner) {
                return false;
            }
        }

        return true;
    }

    public static function call(\Psr\Container\ContainerInterface $container, Zone $zone, Ship $ship): self
    {
        $self = static::new($container);
        $self->zone = $zone;
        $self->ship = $ship;
        $self->serve();

        return $self;
    }
}
