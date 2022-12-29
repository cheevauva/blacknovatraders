<?php

declare(strict_types=1);

namespace BNT\Ship\DAO\ShipRanking;

class ShipRankingTopRatingDAO extends ShipRankingTopDAO
{

    protected function cacheKey(): string
    {
        return 'ranking_top_rating';
    }

    protected function getShips(): array
    {
        $qb = $this->db()->createQueryBuilder();
        $qb->select('*');
        $qb->from($this->table());
        $qb->orderBy('rating', 'DESC');
        $qb->setMaxResults(100);

        return $qb->fetchAllAssociative();
    }

}
