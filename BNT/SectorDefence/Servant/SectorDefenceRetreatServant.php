<?php

declare(strict_types=1);

namespace BNT\SectorDefence\Servant;

use BNT\Ship\Entity\Ship;
use BNT\Ship\DAO\ShipSaveDAO;

class SectorDefenceRetreatServant implements \BNT\ServantInterface
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
            ShipSaveDAO::call($this->ship);
        }
    }

    public static function call(Ship $ship): self
    {
        $self = new static();
        $self->ship = $ship;
        $self->serve();

        return $self;
    }
}
