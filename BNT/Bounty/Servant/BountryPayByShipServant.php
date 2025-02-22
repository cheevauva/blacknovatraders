<?php

declare(strict_types=1);

namespace BNT\Bounty\Servant;

use BNT\Servant;
use BNT\Ship\Entity\Ship;
use BNT\Ship\DAO\ShipSaveDAO;
use BNT\Bounty\DAO\BountySumByShipDAO;
use BNT\Bounty\DAO\BountyRemoveByCriteriaDAO;
use BNT\Bounty\Exception\BountyException;

class BountryPayByShipServant extends Servant
{
    public Ship $ship;

    public function serve(): void
    {
        $ship = $this->ship;
        $amount = BountySumByShipDAO::call($this->container, $ship)->total;

        if (empty($amount)) {
            throw BountyException::notExists();
        }

        if ($ship->credits < $amount) {
            throw BountyException::notEnough($amount);
        }

        $ship->credits = intval($ship->credits - $amount);

        ShipSaveDAO::call($this->container, $ship);

        $removeBounty = BountyRemoveByCriteriaDAO::new($this->container);
        $removeBounty->bountyOn = $this->ship->ship_id;
        $removeBounty->placedBy = 0;
        $removeBounty->serve();
    }

    public static function call(\Psr\Container\ContainerInterface $container, Ship $ship): self
    {
        $self = static::new($container);
        $self->ship = $ship;
        $self->serve();

        return $self;
    }
}
