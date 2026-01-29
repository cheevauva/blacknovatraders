<?php

declare(strict_types=1);

namespace BNT\SectorDefence\Servant;

use BNT\Servant;
use BNT\Ship\Entity\Ship;
use BNT\Ship\DAO\ShipRetrieveByIdDAO;
use BNT\Ship\DAO\ShipSaveDAO;
use BNT\Enum\BalanceEnum;
use BNT\SectorDefence\DAO\SectorDefenceRetrieveTotalFightersBySectorIdDAO;
use BNT\SectorDefence\DAO\SectorDefenceRetrieveManyByCriteriaDAO;
use BNT\SectorDefence\SectorDefenceTypeEnum;
use BNT\SectorDefence\Entity\SectorDefence;
use BNT\Log\Event\LogEvent;
use BNT\Log\Event\LogTollPaidEvent;
use BNT\Log\Event\LogTollRecieveEvent;

class SectorDefencePayTollServant extends Servant
{

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

        $this->totalSectorFighters = SectorDefenceRetrieveTotalFightersBySectorIdDAO::call($this->container, $this->sector);
        $this->fightersToll = intval($this->totalSectorFighters * BalanceEnum::fighter_price->val() * 0.6);

        if ($this->ship->credits < $this->fightersToll) {
            throw new \Exception($l_chf_notenoughcreditstoll);
        }

        $log = new LogTollPaidEvent();
        $log->sector = $this->sector;
        $log->fightersToll = $this->fightersToll;

        $this->logs[] = $log;

        $this->ship->cleared_defences = null;
        $this->ship->credits -= $this->fightersToll;
        //
        $this->distributeToll();

        foreach ($this->distributeTolls as $distributeToll) {
            $distributeToll = SectorDefenceDistributeTollDTO::as($distributeToll);
            ShipSaveDAO::call($this->container, $distributeToll->ship);
        }

        ShipSaveDAO::call($this->container, $this->ship);

        foreach ($this->shipsForChange as $ship) {
            ShipSaveDAO::call($this->container, $ship);
        }

        foreach ($this->logs as $log) {
            LogEvent::as($log)->dispatch($this->eventDispatcher());
        }
    }

    private function distributeToll()
    {
        $retrieveDefences = SectorDefenceRetrieveManyByCriteriaDAO::new($this->container);
        $retrieveDefences->sector_id = $this->sector;
        $retrieveDefences->defence_type = SectorDefenceTypeEnum::Fighters;
        $retrieveDefences->serve();

        foreach ($retrieveDefences->defences as $defence) {
            $defence = SectorDefence::as($defence);

            $tollAmount = round(($defence->quantity / $this->totalSectorFighters) * $this->fightersToll);

            $ship = ShipRetrieveByIdDAO::call($this->container, $defence->ship_id);
            $ship->credits += $tollAmount;

            $this->shipsForChange[] = $ship;

            $log = new LogTollRecieveEvent();
            $log->tollAmount = $tollAmount;
            $log->sector = $this->sector;

            $this->logs[] = $log;
        }
    }


}
