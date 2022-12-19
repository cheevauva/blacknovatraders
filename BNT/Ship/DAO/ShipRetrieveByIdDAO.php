<?php

declare(strict_types=1);

namespace BNT\Ship\DAO;

use BNT\Ship\Ship;

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

        $mapper = $this->mapper();
        $mapper->row = $qb->fetchAssociative() ?: [];
        $mapper->serve();

        $this->ship = $mapper->ship;
    }

}
