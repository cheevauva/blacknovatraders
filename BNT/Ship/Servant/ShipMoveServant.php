<?php

declare(strict_types=1);

namespace BNT\Ship\Servant;

use BNT\ServantInterface;
use BNT\Ship\Entity\Ship;
use BNT\Ship\DAO\ShipSaveDAO;
use BNT\Ship\Exception\ShipMoveTurnException;
use BNT\Link\DAO\LinkRetrieveManyByCriteriaDAO;
use BNT\SectorDefence\Servant\SectorDefenceCheckFightersServant;
use BNT\SectorDefence\Exception\SectorDefenceHasEmenyException;

class ShipMoveServant implements ServantInterface
{
    public Ship $ship;
    public SectorDefenceCheckFightersServant $checkFighters;
    public int $sector;
    public bool $doIt = true;
    public array $links = [];

    public function serve(): void
    {
        $retrieveLinks = LinkRetrieveManyByCriteriaDAO::build();
        $retrieveLinks->link_start = $this->ship->sector;
        $retrieveLinks->link_dest = $this->sector;
        $retrieveLinks->limit = 1;
        $retrieveLinks->serve();

        $this->links = $retrieveLinks->links;

        $this->checkFighters = $this->checkFighters ?? new SectorDefenceCheckFightersServant;
        $this->checkFighters->sector = $this->sector;
        $this->checkFighters->ship = $this->ship;
        $this->checkFighters->serve();

        $this->doIt();
    }

    private function doIt(): void
    {
        global $l_move_failed;
        global $l_move_turn;

        if (!$this->doIt) {
            return;
        }

        if ($this->checkFighters->hasEnemy) {
            throw new SectorDefenceHasEmenyException($l_move_turn);
        }

        try {
            $this->ship->last_login = new \DateTime;
            $this->ship->turn();
            $this->ship->sector = $this->sector;
        } catch (\Exception $ex) {
            throw new ShipMoveTurnException($l_move_turn);
        }

        if (empty($this->links)) {
            throw new ShipMoveTurnException($l_move_failed);
        }

        ShipSaveDAO::call($this->ship);
    }
}
