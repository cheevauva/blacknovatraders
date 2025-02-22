<?php

declare(strict_types=1);

namespace BNT\Ship\ShipRanking\DAO;

class ShipRankingTopScoreCachedDAO extends ShipRankingTopCachedDAO
{

    #[\Override]
    protected function cacheKey(): string
    {
        return 'ranking_top_score';
    }

    #[\Override]
    public function serve(): void
    {
        $this->shipRankingTop = ShipRankingTopScoreDAO::new($this->container);
        
        parent::serve();
    }
}
