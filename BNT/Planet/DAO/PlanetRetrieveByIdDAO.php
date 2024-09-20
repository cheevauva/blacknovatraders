<?php

declare(strict_types=1);

namespace BNT\Planet\DAO;

use BNT\Planet\Entity\Planet;

class PlanetRetrieveByIdDAO extends PlanetDAO
{
    public int $id;
    public ?Planet $planet;

    #[\Override]
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

        $this->planet = $this->asPlanet($qb->fetchAssociative() ?: []);
    }

    public static function call(int $id): ?Planet
    {
        $self = new static;
        $self->id = $id;
        $self->serve();

        return $self->planet;
    }
}
