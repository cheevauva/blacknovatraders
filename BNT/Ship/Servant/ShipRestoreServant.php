<?php

declare(strict_types=1);

namespace BNT\Ship\Servant;

use BNT\Servant;
use BNT\Ship\Entity\Ship;
use BNT\Ship\DAO\ShipSaveDAO;

class ShipRestoreServant extends Servant
{
    public Ship $ship;

    public function serve(): void
    {
        $ship = $this->ship;

        if (!$ship->ship_destroyed) {
            throw new \Exception('Ship has not been destroyed');
        }

        if ($ship->dev_escapepod) {
            $ship->resetWithEscapePod();
        } else {
            $ship->resetWithoutEscapePod();
        }

        ShipSaveDAO::call($this->container, $ship);
    }
}
