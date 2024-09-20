<?php

declare(strict_types=1);

namespace BNT\Ship\ShipRanking\DAO;

class ShipRankingTopTurnsUsedCachedDAO extends ShipRankingTopCachedDAO
{

    #[\Override]
    protected function cacheKey(): string
    {
        return 'ranking_top_turns_used';
    }

    #[\Override]
    public function serve(): void
    {
        $this->shipRankingTop = new ShipRankingTopTurnsUsedDAO;

        parent::serve();
    }
}
