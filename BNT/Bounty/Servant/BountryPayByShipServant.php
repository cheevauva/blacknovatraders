<?php

declare(strict_types=1);

namespace BNT\Bounty\Servant;

use BNT\ServantInterface;
use BNT\Ship\Ship;
use BNT\Ship\DAO\ShipSaveDAO;
use BNT\Bounty\DAO\BountySumByShipDAO;
use BNT\Bounty\DAO\BountyRemoveByShipDAO;
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
        BountyRemoveByShipDAO::call($ship);
    }

    public static function call(Ship $ship): self
    {
        $self = new static;
        $self->ship = $ship;
        $self->serve();

        return $self;
    }

}
