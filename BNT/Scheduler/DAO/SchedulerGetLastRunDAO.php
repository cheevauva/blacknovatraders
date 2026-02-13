<?php

declare(strict_types=1);

namespace BNT\Scheduler\DAO;

class SchedulerGetLastRunDAO extends \UUA\DAO
{
    use \BNT\Traits\DatabaseMainTrait;

    public $lastRun;

    public function serve(): void
    {
        $this->lastRun = db()->column("SELECT last_run FROM scheduler LIMIT 1");
    }
}
