<?php

declare(strict_types=1);

namespace BNT\Planet\DAO;

class PlanetRetrieveManyBySectorDAO extends PlanetDAO
{
    public int $sector;
    public array $planets;

    public function serve(): void
    {

        $qb = $this->db()->createQueryBuilder();
        $qb->select('*');
        $qb->from($this->table(), 'p');
        $qb->andWhere('p.sector_id = :sector_id');
        $qb->setParameters([
            'sector_id' => $this->sector,
        ]);

        $this->planets = [];

        foreach ($qb->fetchAllAssociative() as $planet) {
            $mapper = $this->mapper();
            $mapper->row = $planet;
            $mapper->serve();

            $this->planets[] = $mapper->planet;
        }
    }

    public static function call(int $sector): array
    {
        $self = new static;
        $self->sector = $sector;
        $self->serve();

        return $self->planets;
    }
}
