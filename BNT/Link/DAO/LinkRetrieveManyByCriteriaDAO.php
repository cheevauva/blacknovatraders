<?php

declare(strict_types=1);

namespace BNT\Link\DAO;

class LinkRetrieveManyByCriteriaDAO extends LinkDAO
{

    public ?int $link_start;
    public ?int $link_dest;
    public array $links;

    public function serve(): void
    {
        $qb = $this->db()->createQueryBuilder();
        $qb->select('*');
        $qb->from($this->table());

        if (isset($this->link_start)) {
            $qb->andWhere('link_start = :link_start');
            $qb->setParameter('link_start', $this->link_start);
        }
        
        if (isset($this->link_dest)) {
            $qb->andWhere('link_dest = :link_dest');
            $qb->setParameter('link_dest', $this->link_dest);
        }
        
        $qb->orderBy('link_dest', 'ASC');

        $this->links = [];

        foreach ($qb->fetchAllAssociative() as $link) {
            $mapper = $this->mapper();
            $mapper->row = $link;
            $mapper->serve();

            $this->links[] = $mapper->link;
        }
    }

}
