<?php

declare(strict_types=1);

namespace BNT\Scheduler\Servant;

use BNT\Ship\DAO\ShipsAddingTurnsDAO;
use BNT\Ship\DAO\ShipsEnsuringMaximumTurnsDAO;

/**
 *  Adding turns... 
 *  Ensuring maximum turns are max_turns...
 */
class SchedulerTurnServant extends SchedulerServant
{

    #[\Override]
    public function serve(): void
    {
        $addTurns = ShipsAddingTurnsDAO::new($this->container);
        $addTurns->multiplier = $this->multiplier;
        $addTurns->serve();

        ShipsEnsuringMaximumTurnsDAO::call($this->container);

        $this->multiplier = 0;
    }
}
