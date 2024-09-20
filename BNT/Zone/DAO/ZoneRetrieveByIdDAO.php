<?php

declare(strict_types=1);

namespace BNT\Zone\DAO;

use BNT\Zone\Entity\Zone;

class ZoneRetrieveByIdDAO extends ZoneDAO
{
    public int $id;
    public Zone $zone;

    public function serve(): void
    {
        $qb = $this->db()->createQueryBuilder();
        $qb->select('*');
        $qb->from($this->table());
        $qb->andWhere('zone_id = :id');
        $qb->setParameters([
            'id' => $this->id,
        ]);
        $qb->setMaxResults(1);

        $mapper = $this->mapper();
        $mapper->row = $qb->fetchAssociative() ?: [];
        $mapper->serve();

        $this->zone = $mapper->zone;
    }

    public static function call(int $id): ?Zone
    {
        $self = new static;
        $self->id = $id;
        $self->serve();

        return $self->zone;
    }
}
