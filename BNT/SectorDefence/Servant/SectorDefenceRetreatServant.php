<?php

declare(strict_types=1);

namespace BNT\SectorDefence\Servant;

use BNT\Ship\Entity\Ship;
use BNT\Ship\DAO\ShipSaveDAO;
use BNT\Servant;

class SectorDefenceRetreatServant extends Servant
{
    public bool $doIt = true;
    public Ship $ship;

    public function serve(): void
    {
        $this->ship->turns -= 2;
        $this->ship->turns_used += 2;
        $this->ship->cleared_defences = null;
        $this->ship->last_login = new \DateTime();

        if ($this->doIt) {
            ShipSaveDAO::call($this->container, $this->ship);
        }
    }

    public static function call(\Psr\Container\ContainerInterface $container, Ship $ship): self
    {
        $self = static::new($container);
        $self->ship = $ship;
        $self->serve();

        return $self;
    }
}
