<?php

declare(strict_types=1);

namespace BNT\Bounty\Servant;

use BNT\ServantInterface;
use BNT\Ship\Entity\Ship;
use BNT\Ship\DAO\ShipSaveDAO;
use BNT\Bounty\DAO\BountySumByShipDAO;
use BNT\Bounty\DAO\BountyRemoveByCriteriaDAO;
use BNT\Bounty\Exception\BountyException;

class BountryPayByShipServant implements ServantInterface
{
    public Ship $ship;

    public function serve(): void
    {
        $ship = $this->ship;
        $amount = BountySumByShipDAO::call($ship)->total;

        if (empty($amount)) {
            throw BountyException::notExists();
        }

        if ($ship->credits < $amount) {
            throw BountyException::notEnough($amount);
        }

        $ship->credits = intval($ship->credits - $amount);

        ShipSaveDAO::call($ship);

        $removeBounty = new BountyRemoveByCriteriaDAO;
        $removeBounty->bountyOn = $this->ship->ship_id;
        $removeBounty->placedBy = 0;
        $removeBounty->serve();
    }

    public static function call(Ship $ship): self
    {
        $self = new static;
        $self->ship = $ship;
        $self->serve();

        return $self;
    }
}
