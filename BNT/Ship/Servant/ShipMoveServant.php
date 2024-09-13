<?php

declare(strict_types=1);

namespace BNT\Ship\Servant;

use BNT\ServantInterface;
use BNT\Enum\CalledFromEnum;
use BNT\Ship\Ship;
use BNT\Ship\DAO\ShipSaveDAO;
use BNT\Ship\Exception\ShipMoveTurnException;
use BNT\Sector\DAO\SectorRetrieveByIdDAO;
use BNT\Link\DAO\LinkRetrieveManyByCriteriaDAO;
use BNT\Link\Link;
use BNT\Servant\SectorDefenceCheckFightersServant;

class ShipMoveServant implements ServantInterface
{

    public Ship $ship;
    public int $sector;

    public function serve(): void
    {
        global $admin_mail;
        global $l_move_failed;
        global $l_move_turn;

        try {
            $this->ship->last_login = new \DateTime;
            $this->ship->turn();
            $this->ship->sector = $this->sector;
        } catch (\Exception $ex) {
            throw new ShipMoveTurnException($l_move_turn);
        }

        $sectorinfo = SectorRetrieveByIdDAO::call($this->ship->sector);

        $retrieveLinks = new LinkRetrieveManyByCriteriaDAO;
        $retrieveLinks->link_start = $this->ship->sector;
        $retrieveLinks->link_dest = $this->sector;
        $retrieveLinks->serve();

        if (empty($retrieveLinks->links)) {
            throw new ShipMoveTurnException($l_move_failed);
        }

        try {
            $checkFighters = new SectorDefenceCheckFightersServant;
            $checkFighters->ship = $this->ship;
            $checkFighters->sector = $this->sector;
            $checkFighters->serve();

            if ($checkFighters->ok) {
                ShipSaveDAO::call($this->ship);
                log_move($this->ship->ship_id, $this->sector);
            }
        } catch (\Throwable $ex) {
            mail($admin_mail, "Move Error", sprintf("Start Sector: %s\nEnd Sector: %s\nPlayer: %s - %s\n\nError: %s", ...[
                $sectorinfo->sector_id,
                $this->sector,
                $this->ship->character_name,
                $this->ship->ship_id,
                $ex->getMessage(),
            ]));

            throw $ex;
        }
    }

}
