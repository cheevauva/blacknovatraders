<?php

declare(strict_types=1);

namespace BNT\Bounty\DAO;

class BountyRetrieveManyByCriteriaDAO extends BountyDAO
{
    public ?int $placed_by;
    public ?int $bounty_on;
    public ?int $bounty_id;
    public array $bounties;

    public function serve(): void
    {
        $qb = $this->db()->createQueryBuilder();
        $qb->select('*');
        $qb->from($this->table());

        if (isset($this->bounty_on)) {
            $criteria['bounty_on'] = $this->bounty_on;
        }

        if (isset($this->bounty_id)) {
            $criteria['bounty_id'] = $this->bounty_id;
        }

        if (isset($this->placed_by)) {
            $criteria['placed_by'] = $this->placed_by;
        }

        $this->bounties = [];

        foreach ($qb->fetchAllAssociative() as $bounty) {
            $mapper = $this->mapper();
            $mapper->row = $bounty;
            $mapper->serve();

            $this->bounties[] = $mapper->bounty;
        }
    }
}
