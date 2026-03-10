<?php

declare(strict_types=1);

namespace BNT\Scheduler\DAO;

class SchedulerGetLastRunDAO extends \UUA\DAO
{

    use \BNT\Traits\DatabaseMainTrait;

    public $lastRun;

    #[\Override]
    public function serve(): void
    {
        $this->lastRun = db()->fetchColumn("SELECT last_run FROM scheduler LIMIT 1");
    }
}
