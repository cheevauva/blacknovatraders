<?php

declare(strict_types=1);

namespace BNT\Ship\DAO;

use BNT\Ship\Entity\Ship;

class ShipSaveDAO extends ShipDAO
{
    public Ship $ship;

    public function serve(): void
    {
        $mapper = $this->mapper();
        $mapper->ship = $this->ship;
        $mapper->serve();

        $this->db()->update($this->table(), $mapper->row, [
            'ship_id' => $this->ship->ship_id,
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
