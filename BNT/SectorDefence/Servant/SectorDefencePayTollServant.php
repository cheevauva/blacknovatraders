<?php

declare(strict_types=1);

namespace BNT\SectorDefence\Servant;

use BNT\Ship\Ship;
use BNT\Ship\DAO\ShipRetrieveByIdDAO;
use BNT\Ship\DAO\ShipSaveDAO;
use BNT\Enum\BalanceEnum;
use BNT\SectorDefence\DAO\SectorDefenceRetrieveTotalFightersBySectorIdDAO;
use BNT\SectorDefence\DAO\SectorDefenceRetrieveManyByCriteriaDAO;
use BNT\SectorDefence\SectorDefenceTypeEnum;
use BNT\SectorDefence\SectorDefence;
use BNT\Log\LogTollPaid;
use BNT\Log\LogTollRecieve;
use BNT\Log\Log;

class SectorDefencePayTollServant implements \BNT\ServantInterface
{
    public bool $doIt = true;
    public Ship $ship;
    public int $sector;
    //

    public int $fightersToll = 0;
    public int $totalSectorFighters = 0;
    public array $shipsForChange = [];
    public array $logs = [];

    public function serve(): void
    {
        global $l_chf_notenoughcreditstoll;

        $this->totalSectorFighters = SectorDefenceRetrieveTotalFightersBySectorIdDAO::call($this->sector);
        $this->fightersToll = intval($this->totalSectorFighters * BalanceEnum::fighter_price->val() * 0.6);

        if ($this->ship->credits < $this->fightersToll) {
            throw new \Exception($l_chf_notenoughcreditstoll);
        }

        $log = new LogTollPaid;
        $log->sector = $this->sector;
        $log->fightersToll = $this->fightersToll;

        $this->logs[] = $log;

        $this->ship->cleared_defences = null;
        $this->ship->credits -= $this->fightersToll;
        //
        $this->distributeToll();

        $this->doIt();
    }

    private function distributeToll()
    {
        $retrieveDefences = new SectorDefenceRetrieveManyByCriteriaDAO;
        $retrieveDefences->sector_id = $this->sector;
        $retrieveDefences->defence_type = SectorDefenceTypeEnum::Fighters;
        $retrieveDefences->serve();

        foreach ($retrieveDefences->defences as $defence) {
            $defence = SectorDefence::as($defence);

            $tollAmount = round(($defence->quantity / $this->totalSectorFighters) * $this->fightersToll);

            $ship = ShipRetrieveByIdDAO::call($defence->ship_id);
            $ship->credits += $tollAmount;

            $this->shipsForChange[] = $ship;

            $log = new LogTollRecieve;
            $log->tollAmount = $tollAmount;
            $log->sector = $this->sector;

            $this->logs[] = $log;
        }
    }

    private function doIt(): void
    {
        foreach ($this->distributeTolls as $distributeToll) {
            $distributeToll = SectorDefenceDistributeTollDTO::as($distributeToll);
            ShipSaveDAO::call($distributeToll->ship);
        }

        ShipSaveDAO::call($this->ship);

        foreach ($this->shipsForChange as $ship) {
            ShipSaveDAO::call($ship);
        }

        foreach ($this->logs as $log) {
            Log::as($log)->dispatch();
        }
    }
}
