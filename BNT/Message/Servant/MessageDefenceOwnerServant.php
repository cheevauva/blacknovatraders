<?php

declare(strict_types=1);

namespace BNT\Message\Servant;

use BNT\Servant;
use BNT\SectorDefence\DAO\SectorDefenceRetrieveManyByCriteriaDAO;
use BNT\SectorDefence\Entity\SectorDefence;
use BNT\Log\Event\LogEvent;
use BNT\Log\Event\LogRawEvent;

class MessageDefenceOwnerServant extends Servant
{

    public int $sector;
    public string $message;
    public bool $doIt = true;

    /**
     * @var LogEvent
     */
    public array $logs = [];

    public function serve(): void
    {
        $retrieveSectorDefences = SectorDefenceRetrieveManyByCriteriaDAO::new($this->container);
        $retrieveSectorDefences->sector_id = $this->sector;
        $retrieveSectorDefences->serve();

        foreach ($retrieveSectorDefences->defences as $defence) {
            $defence = SectorDefence::as($defence);

            $log = new LogRawEvent();
            $log->shipId = $defence->ship_id;
            $log->message = $this->message;

            $this->logs[] = $log;
        }

        $this->doIt();
    }

    private function doIt(): void
    {
        if (!$this->doIt) {
            return;
        }

        foreach ($this->logs as $log) {
            LogEvent::as($log)->dispatch($this->eventDispatcher());
        }
    }

}
