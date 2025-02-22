<?php

declare(strict_types=1);

namespace BNT\Ship\DAO;

use BNT\Ship\Entity\Ship;

class ShipRetrieveByIdDAO extends ShipDAO
{
    public int $id;
    public ?Ship $ship;

    public function serve(): void
    {
        $qb = $this->db()->createQueryBuilder();
        $qb->select('*');
        $qb->from($this->table());
        $qb->andWhere('ship_id = :id');
        $qb->setParameters([
            'id' => $this->id,
        ]);
        $qb->setMaxResults(1);

        $this->ship = $this->asShip($qb->fetchAssociative() ?: []);
    }

    public static function call(\Psr\Container\ContainerInterface $container, int $id): ?Ship
    {
        $self = static::new($container);
        $self->id = $id;
        $self->serve();

        return $self->ship;
    }
}
