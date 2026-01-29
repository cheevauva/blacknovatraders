<?php

declare(strict_types=1);

namespace BNT\Ship\DAO;

use BNT\Ship\Entity\Ship;
use Psr\Container\ContainerInterface;

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

    public static function call(ContainerInterface $container, Ship $ship): self
    {
        $self = static::new($container);
        $self->ship = $ship;
        $self->serve();

        return $self;
    }
}
