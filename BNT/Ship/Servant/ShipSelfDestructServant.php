<?php

declare(strict_types=1);

namespace BNT\Ship\Servant;

use BNT\ServantInterface;
use BNT\Ship\Ship;
use BNT\Log\LogHarakiri;

class ShipSelfDestructServant implements ServantInterface
{

    public Ship $ship;
    public string $ip;

    public function serve(): void
    {
//        db_kill_player($playerinfo['ship_id']);
//        cancel_bounty($playerinfo['ship_id']);
//        
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
