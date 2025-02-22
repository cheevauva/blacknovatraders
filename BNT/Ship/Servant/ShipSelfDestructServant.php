<?php

declare(strict_types=1);

namespace BNT\Ship\Servant;

use BNT\Servant;
use BNT\Ship\Entity\Ship;
use BNT\Log\LogHarakiri;
use BNT\Bounty\Servant\BountyCancelServant;
use BNT\Log\DAO\LogCreateDAO;

class ShipSelfDestructServant extends Servant
{
    public Ship $ship;
    public string $ip;

    public function serve(): void
    {
        BountyCancelServant::call($this->container, $this->ship);
        ShipKillServant::call($this->container, $this->ship);

        $harakiri = new LogHarakiri;
        $harakiri->ship_id = $this->ship->ship_id;
        $harakiri->ip = $this->ip;
        
        LogCreateDAO::call($this->container, $harakiri);
    }

    public static function call(\Psr\Container\ContainerInterface $container, Ship $ship, string $ip): self
    {
        $self = static::new($container);
        $self->ship = $ship;
        $self->ip = $ip;
        $self->serve();

        return $self;
    }
}
