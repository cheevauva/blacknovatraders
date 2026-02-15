<?php

declare(strict_types=1);

namespace BNT\Scheduler\Servant;

use BNT\Scheduler\DAO\SchedulerCreateDAO;

class SchedulersDeployServant extends \UUA\Servant
{

    public int $sched_turns;
    public int $sched_igb;
    public int $sched_news;
    public int $sched_planets;
    public int $sched_ports;
    public int $sched_ranking;
    public int $sched_degrade;
    public int $sched_apocalypse;

    #[\Override]
    public function serve(): void
    {
        $schedules = [
            'sched_turns.php' => $this->sched_turns,
            'sched_defenses.php' => $this->sched_turns,
            'sched_xenobe.php' => $this->sched_turns,
            'sched_tow.php' => $this->sched_turns,
            'sched_igb.php' => $this->sched_igb,
            'sched_news.php' => $this->sched_news,
            'sched_planets.php' => $this->sched_planets,
            'sched_ports.php' => $this->sched_ports,
            'sched_ranking.php' => $this->sched_ranking,
            'sched_degrade.php' => $this->sched_degrade,
            'sched_apocalypse.php' => $this->sched_apocalypse,
        ];

        foreach ($schedules as $file => $full) {
            SchedulerCreateDAO::call($this->container, [
                'repeate' => 'Y',
                'ticks_full' => $full,
                'sched_file' => $file,
                'last_run' => time(),
            ]);
        }
    }
}
