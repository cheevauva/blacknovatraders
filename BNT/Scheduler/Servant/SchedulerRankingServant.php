<?php

declare(strict_types=1);

namespace BNT\Scheduler\Servant;

use BNT\Ship\DAO\ShipsByCriteriaDAO;
use BNT\Ship\DAO\ShipGenScoreDAO;

class SchedulerRankingServant extends SchedulerServant
{
    use \BNT\Traits\UnitSimpleCallTrait;
    
    #[\Override]
    public function serve(): void
    {
        $ships = ShipsByCriteriaDAO::call($this->container, ['ship_destroyed' => 'N'])->ships;

        foreach ($ships as $ship) {
            $genScore = ShipGenScoreDAO::new($this->container);
            $genScore->ship = $ship['ship_id'];
            $genScore->serve();
        }
        
        $this->multiplier = 0;
    }
}
