<?php

declare(strict_types=1);

namespace BNT\Xenobe\Servant;

use BNT\Game\Servant\GameSectorFightersServant;
use BNT\Game\Servant\GameSectorMinesServant;
use BNT\Xenobe\Exception\XenobeIsDeadException;

class XenobeToSecDefServant extends \UUA\Servant
{

    use \BNT\Traits\PlayerinfoTrait;

    public int $sector;

    #[\Override]
    public function serve(): void
    {
        if (empty($this->sector)) {
            return;
        }

        $totalSectorFighters = array_sum(array_column(SectorDefencesByCriteriaDAO::call($this->container, [
            'sector_id' => $this->sector,
            'defence_type' => 'F'
        ])->defences, 'quantity'));

        $totalSectorMines = array_sum(array_column(SectorDefencesByCriteriaDAO::call($this->container, [
            'sector_id' => $this->sector,
            'defence_type' => 'M'
        ])->defences, 'quantity'));

        if (!empty($totalSectorFighters) || !empty($totalSectorMines)) {
            LogPlayerDAO::call($this->container, $this->playerinfo['ship_id'], LogTypeConstants::LOG_RAW, sprintf(...[
                "ATTACKING SECTOR DEFENCES %s fighters and %s mines.",
                $totalSectorFighters,
                $totalSectorMines
            ]));
        }

        $sectorFighter = GameSectorFightersServant::new($this->container);
        $sectorFighter->totalSectorFighters = $totalSectorFighters;
        $sectorFighter->playerinfo = $this->playerinfo;
        $sectorFighter->sector = $this->sector;
        $sectorFighter->serve();

        if ($sectorFighter->shipDestroyed) {
            throw new XenobeIsDeadException();
        }

        $sectorMines = GameSectorMinesServant::new($this->container);
        $sectorMines->playerinfo = $this->playerinfo;
        $sectorMines->totalSectorMines = $totalSectorMines;
        $sectorMines->sector = $this->sector;
        $sectorMines->serve();

        if ($sectorMines->shipDestroyed) {
            throw new XenobeIsDeadException();
        }
    }
}
