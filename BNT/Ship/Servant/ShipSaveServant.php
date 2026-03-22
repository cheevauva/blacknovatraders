<?php

declare(strict_types=1);

namespace BNT\Ship\Servant;

use BNT\Ship\Ship;
use BNT\Ship\DAO\ShipUpdateDAO;
use BNT\Ship\DAO\ShipCreateDAO;
use Psr\Container\ContainerInterface;

class ShipSaveServant extends \UUA\Servant
{

    public Ship $ship;

    #[\Override]
    public function serve(): void
    {
        $ship = ShipToRowMapper::new($this->container, $this->ship)->row;

        if ($this->ship->id) {
            ShipUpdateDAO::call($this->container, $ship, $this->ship->id);
        } else {
            $this->ship->id = ShipCreateDAO::call($this->container, $ship)->id;
        }
    }

    public static function call(ContainerInterface $container, Ship $ship): self
    {
        $self = self::new($container);
        $self->ship = $ship;
        $self->serve();

        return $self;
    }
}
