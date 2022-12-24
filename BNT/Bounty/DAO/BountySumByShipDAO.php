<?php

declare(strict_types=1);

namespace BNT\Bounty\DAO;

use BNT\Ship\Ship;

class BountySumByShipDAO extends BountyDAO
{

    public Ship $ship;
    public float $total;

    public function serve(): void
    {
        $this->total = floatval($this->db()->fetchOne("SELECT SUM(amount) as total_bounty FROM {$this->table()} WHERE placed_by = 0 AND bounty_on = :ship_id", [
            'ship_id' => $this->ship->ship_id,
        ]) ?? 0);
    }

    public static function call(Ship $ship): self
    {
        $self = new static;
        $self->ship = $ship;
        $self->serve();

        return $self;
    }

}
