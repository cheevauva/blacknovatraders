<?php

declare(strict_types=1);

namespace BNT\Ship\Servant;

use BNT\Servant;
use BNT\Ship\Entity\Ship;
use BNT\Ship\DAO\ShipSaveDAO;
use BNT\Bounty\Servant\BountyCancelServant;
use BNT\Ship\Servant\ShipKillServant;
use Psr\Container\ContainerInterface;

class ShipDestroyServant extends Servant
{

    public Ship $ship;
    public bool $shipDestroyed = false;

    public function serve(): void
    {
        if ($this->ship->dev_escapepod) {
            $this->shipDestroyed = false;
            $this->ship->resetWithEscapePod();
            $this->ship->rating = intval(round($this->ship->rating / 2));
            BountyCancelServant::call($this->container, $this->ship);
        } else {
            $this->shipDestroyed = true;
            BountyCancelServant::call($this->container, $this->ship);
            ShipKillServant::call($this->container, $this->ship);
        }

        ShipSaveDAO::call($this->container, $this->ship);
    }

    public static function call(ContainerInterface $container, Ship $ship): self
    {
        $self = static::new($container);
        $self->ship = $ship;
        $self->serve();

        return $self;
    }
}
