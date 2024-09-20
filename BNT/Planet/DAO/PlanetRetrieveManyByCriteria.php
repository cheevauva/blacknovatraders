<?php

declare(strict_types=1);

namespace BNT\Planet\DAO;

class PlanetRetrieveManyByCriteria extends PlanetDAO
{
    public ?int $owner;
    public ?bool $base;
    public array $planets;

    public function serve(): void
    {
        $qb = $this->db()->createQueryBuilder();
        $qb->select('*');
        $qb->from($this->table(), 'p');

        if (isset($this->owner)) {
            $qb->andWhere('p.owner = :owner');
            $qb->setParameter('owner', $this->owner);
        }

        if (isset($this->base)) {
            $qb->andWhere('base = :base');
            $qb->setParameter('base', $this->base ? 'Y' : 'N');
        }

        $this->planets = [];

        foreach ($qb->fetchAllAssociative() as $planet) {
            $mapper = $this->mapper();
            $mapper->row = $planet;
            $mapper->serve();

            $this->planets[] = $mapper->planet;
        }
    }
}
