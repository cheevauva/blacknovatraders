<?php

declare(strict_types=1);

namespace BNT\Scheduler\Servant;

use BNT\IBankAccount\DAO\IBankAccountExpointerDAO;

class SchedulerIGBServant extends SchedulerServant
{

    #[\Override]
    public function serve(): void
    {
        $expointer = IBankAccountExpointerDAO::new($this->container);
        $expointer->multiplier = $this->multiplier;
        $expointer->serve();

        $this->multiplier = 0;
    }
}
