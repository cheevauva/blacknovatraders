<?php

declare(strict_types=1);

namespace BNT\Ship\ShipRanking\DAO;

class ShipRankingTopRatingCachedDAO extends ShipRankingTopCachedDAO
{

    #[\Override]
    protected function cacheKey(): string
    {
        return 'ranking_top_rating';
    }

    #[\Override]
    public function serve(): void
    {
        $this->shipRankingTop = ShipRankingTopRatingDAO::build();
        
        parent::serve();
    }
}
