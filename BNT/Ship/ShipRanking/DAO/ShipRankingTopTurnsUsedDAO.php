<?php

declare(strict_types=1);

namespace BNT\Ship\ShipRanking\DAO;

use BNT\Enum\BalanceEnum;

class ShipRankingTopTurnsUsedDAO extends ShipRankingTopDAO
{

    #[\Override]
    protected function ships(): array
    {
        $qb = $this->db()->createQueryBuilder();
        $qb->select('*');
        $qb->from($this->table());
        $qb->andWhere('ship_destroyed=:ship_destroyed');
        $qb->andWhere('email NOT LIKE :email');
        $qb->setParameters([
            'ship_destroyed' => 'N',
            'email' => '%@xenobe',
        ]);
        $qb->orderBy('turns_used', 'DESC');
        $qb->setMaxResults(BalanceEnum::max_rank->val());

        return $qb->fetchAllAssociative();
    }
}
