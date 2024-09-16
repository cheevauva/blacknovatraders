<?php

declare(strict_types=1);

namespace BNT\Ship\Servant;

use BNT\ServantInterface;
use BNT\Ship\Ship;
use BNT\Ship\DAO\ShipSaveDAO;
use BNT\Ship\Exception\ShipMoveTurnException;
use BNT\Link\DAO\LinkRetrieveManyByCriteriaDAO;

class ShipMoveServant implements ServantInterface
{

    public Ship $ship;
    public int $sector;
    public bool $doIt = true;
    public array $links = [];

    public function serve(): void
    {
        $retrieveLinks = new LinkRetrieveManyByCriteriaDAO;
        $retrieveLinks->link_start = $this->ship->sector;
        $retrieveLinks->link_dest = $this->sector;
        $retrieveLinks->limit = 1;
        $retrieveLinks->serve();

        $this->links = $retrieveLinks->links;
        $this->doIt();
    }

    private function doIt(): void
    {
        global $l_move_failed;
        global $l_move_turn;

        if (!$this->doIt) {
            return;
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
