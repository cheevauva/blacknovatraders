<?php

declare(strict_types=1);

namespace BNT\Game\Servant;

use BNT\Sector\DAO\SectorByIdDAO;

class GameCalculateTriptimeServant extends \UUA\Servant
{

    public array $ship;
    public int $sector;
    public protected(set) int $triptime = 0;
    public protected(set) int $energyScooped = 0;

    #[\Override]
    public function serve(): void
    {
        global $level_factor;

        $deg = pi() / 180;
        $sector = abs($this->sector);

        if ($sector == $this->ship['sector']) {
            return;
        }

        $start = SectorByIdDAO::call($this->container, $this->ship['sector'])->sector;
        $finish = SectorByIdDAO::call($this->container, $sector)->sector;

        $sa1 = $start['angle1'] * $deg;
        $sa2 = $start['angle2'] * $deg;
        $fa1 = $finish['angle1'] * $deg;
        $fa2 = $finish['angle2'] * $deg;
        $x = ($start['distance'] * sin($sa1) * cos($sa2)) - ($finish['distance'] * sin($fa1) * cos($fa2));
        $y = ($start['distance'] * sin($sa1) * sin($sa2)) - ($finish['distance'] * sin($fa1) * sin($fa2));
        $z = ($start['distance'] * cos($sa1)) - ($finish['distance'] * cos($fa1));
        $distance = round(sqrt(mypw($x, 2) + mypw($y, 2) + mypw($z, 2)));
        $shipspeed = mypw($level_factor, $this->ship['engines']);

        $this->triptime = (int) round($distance / $shipspeed);

        if ($this->triptime == 0) {
            $this->triptime = 1;
        }

        $this->energyScooped = $this->energyScooped($distance);
    }

    public function energyScooped(float $distance): int
    {
        $energyScooped = 0;

        if ($this->ship['dev_fuelscoop'] == "Y") {
            $energyScooped = $distance * 100;
        }


        if ($this->ship['dev_fuelscoop'] == "Y" && $energyScooped == 0 && $this->triptime == 1) {
            $energyScooped = 100;
        }

        $freePower = NUM_ENERGY($this->ship['power']) - $this->ship['ship_energy'];

        if ($freePower < $energyScooped) {
            $energyScooped = $freePower;
        }

        if ($energyScooped < 1) {
            $energyScooped = 0;
        }

        return (int) $energyScooped;
    }
}
