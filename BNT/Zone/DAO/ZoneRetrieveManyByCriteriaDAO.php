<?php

declare(strict_types=1);

namespace BNT\Zone\DAO;

use BNT\Zone\Entity\Zone;

class ZoneRetrieveManyByCriteriaDAO extends ZoneDAO
{
    public ?int $limit;
    public ?int $zone_id;
    public ?bool $corp_zone;
    public ?int $owner;
    //
    public array $zones;
    public ?Zone $firstOfZones;

    public function serve(): void
    {
        $qb = $this->db()->createQueryBuilder();
        $qb->select('*');
        $qb->from($this->table());

        if (isset($this->corp_zone)) {
            $qb->andWhere('corp_zone = :corp_zone');
            $qb->setParameter('corp_zone', fromBool($this->corp_zone));
        }

        if (isset($this->owner)) {
            $qb->andWhere('owner = :owner');
            $qb->setParameter('owner', $this->owner);
        }

        $qb->setMaxResults($this->limit);

        $this->zones = $this->asZones($qb->fetchAssociative() ?: []);
        $this->firstOfZones = $this->zones[0] ?? null;
    }
}
