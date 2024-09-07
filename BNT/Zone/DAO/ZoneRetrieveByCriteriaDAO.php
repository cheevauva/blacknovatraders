<?php

declare(strict_types=1);

namespace BNT\Zone\DAO;

use BNT\Zone\Zone;

class ZoneRetrieveByCriteriaDAO extends ZoneDAO
{

    public ?bool $corp;
    public ?int $owner;
    //
    public Zone $zone;

    public function serve(): void
    {
        $qb = $this->db()->createQueryBuilder();
        $qb->select('*');
        $qb->from($this->table());

        if (isset($this->corp)) {
            $qb->andWhere('corp_zone = :corp');
            $qb->setParameter('corp', $this->corp ? 'Y' : 'N');
        }

        if (isset($this->owner)) {
            $qb->andWhere('owner = :owner');
            $qb->setParameter('owner', $this->owner);
        }
        
        $qb->setMaxResults(1);

        $mapper = $this->mapper();
        $mapper->row = $qb->fetchAssociative() ?: [];
        $mapper->serve();

        $this->zone = $mapper->zone;
    }

}
