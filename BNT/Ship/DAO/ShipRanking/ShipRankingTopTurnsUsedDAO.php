<?php

declare(strict_types=1);

namespace BNT\Ship\DAO\ShipRanking;

class ShipRankingTopTurnsUsedDAO extends ShipRankingTopDAO
{

    protected function cacheKey(): string
    {
        return 'ranking_top_turns_used';
    }

    protected function getShips(): array
    {
        $qb = $this->db()->createQueryBuilder();
        $qb->select('*');
        $qb->from($this->table());
        $qb->orderBy('turns_used', 'DESC');
        $qb->setMaxResults(100);

        return $qb->fetchAllAssociative();
    }

}
