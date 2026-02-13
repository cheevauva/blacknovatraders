<?php

declare(strict_types=1);

namespace BNT\Scheduler\DAO;

class SchedulersDefaultGenerateDAO extends \UUA\DAO
{
    use \BNT\Traits\DatabaseMainTrait;
    use \BNT\Traits\UnitSimpleCallTrait;

    #[\Override]
    public function serve(): void
    {
        global $sched_turns;
        global $sched_igb;
        global $sched_news;
        global $sched_planets;
        global $sched_ports;
        global $sched_ranking;
        global $sched_degrade;
        global $sched_apocalypse;

        $this->db()->q("INSERT INTO scheduler VALUES(NULL, 'Y', 0, $sched_turns, 0, 'sched_turns.php', NULL,unix_timestamp(now()))");
        $this->db()->q("INSERT INTO scheduler VALUES(NULL, 'Y', 0, $sched_turns, 0, 'sched_defenses.php', NULL,unix_timestamp(now()))");
        $this->db()->q("INSERT INTO scheduler VALUES(NULL, 'Y', 0, $sched_turns, 0, 'sched_xenobe.php', NULL,unix_timestamp(now()))");
        $this->db()->q("INSERT INTO scheduler VALUES(NULL, 'Y', 0, $sched_igb, 0, 'sched_igb.php', NULL,unix_timestamp(now()))");
        $this->db()->q("INSERT INTO scheduler VALUES(NULL, 'Y', 0, $sched_news, 0, 'sched_news.php', NULL,unix_timestamp(now()))");
        $this->db()->q("INSERT INTO scheduler VALUES(NULL, 'Y', 0, $sched_planets, 0, 'sched_planets.php', NULL,unix_timestamp(now()))");
        $this->db()->q("INSERT INTO scheduler VALUES(NULL, 'Y', 0, $sched_ports, 0, 'sched_ports.php', NULL,unix_timestamp(now()))");
        $this->db()->q("INSERT INTO scheduler VALUES(NULL, 'Y', 0, $sched_turns, 0, 'sched_tow.php', NULL,unix_timestamp(now()))");
        $this->db()->q("INSERT INTO scheduler VALUES(NULL, 'Y', 0, $sched_ranking, 0, 'sched_ranking.php', NULL,unix_timestamp(now()))");
        $this->db()->q("INSERT INTO scheduler VALUES(NULL, 'Y', 0, $sched_degrade, 0, 'sched_degrade.php', NULL,unix_timestamp(now()))");
        $this->db()->q("INSERT INTO scheduler VALUES(NULL, 'Y', 0, $sched_apocalypse, 0, 'sched_apocalypse.php', NULL,unix_timestamp(now()))");
    }
}
