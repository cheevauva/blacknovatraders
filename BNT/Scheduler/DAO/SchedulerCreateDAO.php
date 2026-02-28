<?php

declare(strict_types=1);

namespace BNT\Scheduler\DAO;

class SchedulerCreateDAO extends \UUA\DAO
{

    use \BNT\Traits\DatabaseRowCreateTrait;

    #[\Override]
    public function serve(): void
    {
        $this->rowCreate('scheduler');
    }
}
