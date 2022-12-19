<?php

declare(strict_types=1);

namespace BNT\Ship\Servant;

use BNT\ServantInterface;
use BNT\Ship\Ship;

class ShipRestoreServant implements ServantInterface
{

    public Ship $ship;

    public function serve(): void
    {
        $ship = $this->ship;

        if (!$ship->isDestroyed) {
            throw new \Exception('Ship has not been destroyed');
        }

        if ($ship->hasEscapePod) {
            $ship->resetWithEscapePod();
        } else {
            
        }

        $save = new ShipSaveDAO;
        $save->ship = $save;
        $save->serve();
    }

}
