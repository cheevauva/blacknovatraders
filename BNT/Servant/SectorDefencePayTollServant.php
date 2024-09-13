<?php

declare(strict_types=1);

namespace BNT\Servant;

use BNT\Ship\Ship;
use BNT\Ship\DAO\ShipRetrieveByIdDAO;
use BNT\BalanceEnum;
use BNT\SectorDefence\DAO\SectorDefenceRetrieveTotalFightersBySectorIdDAO;
use BNT\SectorDefence\DAO\SectorDefenceRetrieveManyByCriteriaDAO;
use BNT\SectorDefence\SectorDefenceTypeEnum;
use BNT\SectorDefence\SectorDefence;
use BNT\DTO\SectorDefenceDistributeTollDTO;

class SectorDefencePayTollServant implements \BNT\ServantInterface
{

    public Ship $ship;
    public int $sector;
    //

    /**
     * @var SectorDefenceDistributeTollDTO[]
     */
    public array $distributeTolls = [];
    public int $fightersToll;
    public int $totalSectorFighters;

    public function serve(): void
    {
        $this->totalSectorFighters = SectorDefenceRetrieveTotalFightersBySectorIdDAO::call($this->sector);
        $this->fightersToll = intval($this->totalSectorFighters * BalanceEnum::fighter_price->val() * 0.6);
        //
        $this->ship->cleared_defences = null;
        $this->ship->credits -= $this->fightersToll;
        //
        $this->distributeToll();
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

            $distributeToll = new SectorDefenceDistributeTollDTO;
            $distributeToll->defence = $defence;
            $distributeToll->ship = $ship;
            $distributeToll->tollAmount = $tollAmount;
            $distributeToll->sector = $this->sector;

            $this->distributeTolls[] = $distributeToll;
        }
    }

}
