<?php

declare(strict_types=1);

namespace BNT\Scheduler\Servant;

use BNT\Sector\DAO\SectorPortsDAO;

class SchedulerPortsServant extends SchedulerServant
{

    #[\Override]
    public function serve(): void
    {
        $ports = SectorPortsDAO::new($this->container);
        $ports->multiplier = $this->multiplier;
        $ports->serve();

        $this->multiplier = 0;
    }
}
