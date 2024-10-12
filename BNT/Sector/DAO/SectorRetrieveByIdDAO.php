<?php

declare(strict_types=1);

namespace BNT\Sector\DAO;

use BNT\Sector\Entity\Sector;

class SectorRetrieveByIdDAO extends SectorDAO
{
    public int $id;
    public ?Sector $sector;

    public function serve(): void
    {
        $qb = $this->db()->createQueryBuilder();
        $qb->select('*');
        $qb->from($this->table());
        $qb->andWhere('sector_id = :id');
        $qb->setParameters([
            'id' => $this->id,
        ]);
        $qb->setMaxResults(1);

        $mapper = $this->mapper();
        $mapper->row = $qb->fetchAssociative() ?: [];
        $mapper->serve();

        $this->sector = $mapper->sector;
    }

    public static function call(int $id): ?Sector
    {
        $self = new static();
        $self->id = $id;
        $self->serve();

        return $self->sector;
    }
}
