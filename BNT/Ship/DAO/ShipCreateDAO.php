<?php

declare(strict_types=1);

namespace BNT\Ship\DAO;

use BNT\Ship\Entity\Ship;

class ShipCreateDAO extends ShipDAO
{
    public Ship $ship;

    public function serve(): void
    {
        $mapper = $this->mapper();
        $mapper->ship = $this->ship;
        $mapper->serve();

        $this->db()->insert($this->table(), $mapper->row);

        $this->ship->ship_id = intval($this->db()->lastInsertId());
    }

    public static function call(Ship $ship): self
    {
        $self = new static;
        $self->ship = $ship;
        $self->serve();

        return $self;
    }
}
