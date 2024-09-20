<?php

declare(strict_types=1);

namespace BNT\Zone\Servant;

use BNT\ServantInterface;
use BNT\Zone\Zone;
use BNT\Zone\Exception\ZoneException;
use BNT\Ship\Ship;
use BNT\Ship\DAO\ShipRetrieveByIdDAO;

class ZonePortTradeServant implements ServantInterface
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
            $owner = ShipRetrieveByIdDAO::call($zone->owner);

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

    public static function call(Zone $zone, Ship $ship): self
    {
        $self = new static;
        $self->zone = $zone;
        $self->ship = $ship;
        $self->serve();

        return $self;
    }
}
