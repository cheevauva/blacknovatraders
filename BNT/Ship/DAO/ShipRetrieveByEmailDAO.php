<?php

declare(strict_types=1);

namespace BNT\Ship\DAO;

use BNT\Ship\Ship;

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

        $mapper = $this->mapper();
        $mapper->row = $qb->fetchAssociative() ?: [];
        $mapper->serve();

        $this->ship = $mapper->ship;
    }

}
