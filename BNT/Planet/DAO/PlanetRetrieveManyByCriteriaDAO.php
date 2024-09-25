<?php

declare(strict_types=1);

namespace BNT\Planet\DAO;

use BNT\Planet\Entity\Planet;

class PlanetRetrieveManyByCriteriaDAO extends PlanetDAO
{

    public ?int $owner;
    public ?int $sector_id;
    public ?bool $base;
    public array $planets;
    public ?Planet $firstOfPlanets;

    #[\Override]
    public function serve(): void
    {
        $qb = $this->db()->createQueryBuilder();
        $qb->select('*');
        $qb->from($this->table(), 'p');

        if (isset($this->owner)) {
            $qb->andWhere('p.owner = :owner');
            $qb->setParameter('owner', $this->owner);
        }

        if (isset($this->owner)) {
            $qb->andWhere('p.owner = :owner');
            $qb->setParameter('owner', $this->owner);
        }

        if (isset($this->sector_id)) {
            $qb->andWhere('p.sector_id = :sector_id');
            $qb->setParameter('sector_id', $this->sector_id);
        }

        $this->planets = $this->asPlanets($qb->fetchAllAssociative());
        $this->firstOfPlanets = $this->planets[0] ?? null;
    }

}
