<?php

declare(strict_types=1);

namespace BNT\Ship\ShipRanking\DAO;

use BNT\Enum\BalanceEnum;

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
        $qb->andWhere('ship_destroyed=:ship_destroyed');
        $qb->andWhere('email NOT LIKE :email');
        $qb->setParameters([
            'ship_destroyed' => 'N',
            'email' => '%@xenobe',
        ]);
        $qb->setMaxResults(BalanceEnum::max_rank->val());

        return $qb->fetchAllAssociative();
    }
}
