<?php

declare(strict_types=1);

namespace BNT\Sector\DAO;

use BNT\Sector\Sector;

class SectorRetrieveByCriteriaDAO extends SectorDAO
{

    public ?int $zone_id;
    public ?Sector $sector;

    public function serve(): void
    {
        $qb = $this->db()->createQueryBuilder();
        $qb->select('*');
        $qb->from($this->table());

        if (isset($this->zone_id)) {
            $qb->andWhere('zone_id = :zone_id');
            $qb->setParameter('zone_id', $this->zone_id);
        }
        
        $qb->setMaxResults(1);

        $mapper = $this->mapper();
        $mapper->row = $qb->fetchAssociative() ?: [];
        $mapper->serve();

        $this->sector = $mapper->sector;
    }


}
