<?php

declare(strict_types=1);

namespace BNT\Ship\DAO\ShipRanking;

use BNT\BalanceEnum;

class ShipRankingTopScoreDAO extends ShipRankingTopDAO
{

    protected function cacheKey(): string
    {
        return 'ranking_top_score';
    }

    protected function getShips(): array
    {
        $qb = $this->db()->createQueryBuilder();
        $qb->select('*');
        $qb->from($this->table());
        $qb->orderBy('score', 'DESC');
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
