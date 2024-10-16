<?php

declare(strict_types=1);

namespace BNT\Message\Servant;

use BNT\ServantInterface;
use BNT\SectorDefence\DAO\SectorDefenceRetrieveManyByCriteriaDAO;
use BNT\SectorDefence\Entity\SectorDefence;
use BNT\Log\Log;
use BNT\Log\LogRaw;
use BNT\Log\DAO\LogCreateDAO;
use BNT\Traits\BuildTrait;

class MessageDefenceOwnerServant implements ServantInterface
{
    use BuildTrait;
    
    public int $sector;
    public string $message;
    public bool $doIt = true;

    /**
     * @var LogRaw|LogCreateDAO[]
     */
    public array $logs = [];

    public function serve(): void
    {
        $retrieveSectorDefences = SectorDefenceRetrieveManyByCriteriaDAO::build();
        $retrieveSectorDefences->sector_id = $this->sector;
        $retrieveSectorDefences->serve();

        foreach ($retrieveSectorDefences->defences as $defence) {
            $defence = SectorDefence::as($defence);

            $log = new LogRaw();
            $log->ship_id = $defence->ship_id;
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
            Log::as($log)->dispatch();
        }
    }
}
