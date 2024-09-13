<?php

declare(strict_types=1);

namespace BNT\Servant;

use BNT\Ship\DAO\ShipSaveDAO;
use BNT\Log\LogTollPaid;
use BNT\Log\LogTollRecieve;
use BNT\Log\DAO\LogCreateDAO;

class SectorDefencePayTollAsTurnServant extends SectorDefencePayTollServant
{

    public function serve(): void
    {
        global $l_chf_notenoughcreditstoll;
        
        parent::serve();

        if ($this->ship->credits < $this->fightersToll) {
            throw new \Exception($l_chf_notenoughcreditstoll);
        }

        ShipSaveDAO::call($this->ship);

        $log = new LogTollPaid;
        $log->sector = $this->sector;
        $log->fightersToll = $this->fightersToll;

        foreach ($this->distributeTolls as $distributeToll) {
            $distributeToll = SectorDefenceDistributeTollDTO::as($distributeToll);

            ShipSaveDAO::call($distributeToll->ship);

            $log = new LogTollRecieve;
            $log->tollAmount = $distributeToll->tollAmount;
            $log->sector = $distributeToll->sector;

            LogCreateDAO::call($log);
        }

        LogCreateDAO::call($log);
    }

}
