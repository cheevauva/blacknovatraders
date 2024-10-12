<?php

declare(strict_types=1);

namespace BNT\Ship\DAO;

use BNT\Ship\Entity\Ship;

class ShipRetrieveByEmailDAO extends ShipDAO
{
    public string $email;
    public ?Ship $ship;

    public function serve(): void
    {
        $qb = $this->db()->createQueryBuilder();
        $qb->select('*');
        $qb->from($this->table());
        $qb->andWhere('email = :email');
        $qb->setParameters([
            'email' => $this->email,
        ]);
        $qb->setMaxResults(1);

        $this->ship = $this->asShip($qb->fetchAssociative() ?: []);
    }

    public static function call(string $email): ?Ship
    {
        $self = new static();
        $self->email = $email;
        $self->serve();

        return $self->ship;
    }
}
