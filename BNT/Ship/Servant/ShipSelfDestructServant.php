<?php

declare(strict_types=1);

namespace BNT\Ship\Servant;

use Psr\Container\ContainerInterface;
use BNT\Servant;
use BNT\Ship\Entity\Ship;
use BNT\Bounty\Servant\BountyCancelServant;
use BNT\Log\Event\LogHarakiriEvent;

class ShipSelfDestructServant extends Servant
{

    public Ship $ship;
    public string $ip;

    public function serve(): void
    {
        BountyCancelServant::call($this->container, $this->ship);
        ShipKillServant::call($this->container, $this->ship);

        $harakiri = new LogHarakiriEvent();
        $harakiri->shipId = $this->ship->ship_id;
        $harakiri->ip = $this->ip;
        $harakiri->dispatch($this->eventDispatcher());
    }

    public static function call(ContainerInterface $container, Ship $ship, string $ip): self
    {
        $self = static::new($container);
        $self->ship = $ship;
        $self->ip = $ip;
        $self->serve();

        return $self;
    }

}
