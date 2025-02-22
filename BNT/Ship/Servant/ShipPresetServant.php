<?php

declare(strict_types=1);

namespace BNT\Ship\Servant;

use BNT\Servant;
use BNT\Enum\BalanceEnum;
use BNT\Ship\Entity\Ship;
use BNT\Ship\DAO\ShipSaveDAO;

class ShipPresetServant extends Servant
{
    public Ship $ship;
    public $preset1;
    public $preset2;
    public $preset3;

    public function serve(): void
    {
        global $l_pre_exceed;

        $sectorMax = BalanceEnum::sector_max->val();
        $presets = [
            1 => $this->preset1,
            2 => $this->preset2,
            3 => $this->preset3,
        ];

        foreach ($presets as $number => $preset) {
            if ($preset > $sectorMax) {
                throw new \Exception(strtr($l_pre_exceed, [
                    '[preset]' => $number,
                    '[sector_max]' => $sectorMax
                ]));
            }
        }

        $this->ship->preset1 = $this->preset1;
        $this->ship->preset2 = $this->preset2;
        $this->ship->preset3 = $this->preset3;

        ShipSaveDAO::call($this->container, $this->ship);
    }
}
