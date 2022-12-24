<?php

declare(strict_types=1);

namespace BNT\Bounty\DAO;

use BNT\Ship\Ship;

class BountyRemoveByShipDAO extends BountyDAO
{

    public Ship $ship;

    public function serve(): void
    {
        $this->db()->delete($this->table(), [
            'bounty_on' => $this->ship->ship_id,
            'placed_by' => 0,
        ]);
    }

    public static function call(Ship $ship): self
    {
        $self = new static;
        $self->ship = $ship;
        $self->serve();

        return $self;
    }

}
