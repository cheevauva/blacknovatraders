<?php

declare(strict_types=1);

namespace BNT\Scheduler\Servant;

use BNT\SectorDefence\DAO\SectorDefencesCleanUpDAO;

/**
 * Sector Defence Cleanup
 */
class SchedulerDefensesServant extends SchedulerServant
{

    #[\Override]
    public function serve(): void
    {
        SectorDefencesCleanUpDAO::call($this->container);

        $this->multiplier = 0; //no use to run this again
    }
}
