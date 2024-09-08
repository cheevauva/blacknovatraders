<?php

declare(strict_types=1);

namespace BNT\Ship\Servant;

use BNT\ServantInterface;
use BNT\Ship\Ship;
use BNT\Ship\DAO\ShipSaveDAO;
use BNT\Bounty\Servant\BountyCancelServant;
use BNT\Ship\Servant\ShipKillServant;

class ShipDestroyServant implements ServantInterface
{

    public Ship $ship;

    public function serve(): void
    {
        if ($this->ship->dev_escapepod) {
            $rating = round($this->ship->rating / 2);

            $this->result->shipDestroyed = false;
            $this->result->hasEscapePod = true;
            $this->result->ok = 0;

            $this->ship->resetWithEscapePod();
            $this->ship->rating = $rating;

            ShipSaveDAO::call($this->ship);
            BountyCancelServant::call($this->ship);
        } else {
            $this->result->shipDestroyed = true;
            $this->result->ok = 0;

            BountyCancelServant::call($this->ship);
            ShipKillServant::call($this->ship);
        }
    }

    public static function call(Ship $ship): void
    {
        $self = new static;
        $self->ship = $ship;
        $self->serve();
    }

}
