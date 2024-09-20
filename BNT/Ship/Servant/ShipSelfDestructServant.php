<?php

declare(strict_types=1);

namespace BNT\Ship\Servant;

use BNT\ServantInterface;
use BNT\Ship\Ship;
use BNT\Log\LogHarakiri;
use BNT\Bounty\Servant\BountyCancelServant;

class ShipSelfDestructServant implements ServantInterface
{
    public Ship $ship;
    public string $ip;

    public function serve(): void
    {
        BountyCancelServant::call($this->ship);
        ShipKillServant::call($this->ship);

        $harakiri = new LogHarakiri;
        $harakiri->ship_id = $this->ship->ship_id;
        $harakiri->ip = $this->ip;
        $harakiri->dispatch();
    }

    public static function call(Ship $ship, string $ip): self
    {
        $self = new static;
        $self->ship = $ship;
        $self->ip = $ip;
        $self->serve();

        return $self;
    }
}
