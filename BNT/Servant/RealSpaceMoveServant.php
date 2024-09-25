<?php

declare(strict_types=1);

namespace BNT\Servant;

use BNT\ServantInterface;
use BNT\Ship\Entity\Ship;
use BNT\Ship\DAO\ShipRetrieveByIdDAO;
use BNT\Ship\DAO\ShipSaveDAO;
use BNT\Sector\Entity\Sector;
use BNT\Sector\DAO\SectorRetrieveByIdDAO;
use BNT\SectorDefence\DAO\SectorDefenceRetrieveManyByCriteriaDAO;
use BNT\Enum\BalanceEnum;

class RealSpaceMoveServant implements ServantInterface
{

    public bool $doIt = true;
    public Ship $ship;
    public Sector $sectorStart;
    public Sector $sectorFinish;
    public int $destination;
    public string $retval;
    public bool $hostile = false;

    public function serve(): void
    {
        $deg = pi() / 180;

        $this->sectorStart = SectorRetrieveByIdDAO::call($this->ship->sector);
        $this->sectorFinish = SectorRetrieveByIdDAO::call($this->destination);

        $sa1 = $this->sectorStart->angle1 * $deg;
        $sa2 = $this->sectorStart->angle2 * $deg;
        $fa1 = $this->sectorFinish->angle1 * $deg;
        $fa2 = $this->sectorFinish->angle2 * $deg;
        $x = ($this->sectorStart->distance * sin($sa1) * cos($sa2)) - ($this->sectorFinish->distance * sin($fa1) * cos($fa2));
        $y = ($this->sectorStart->distance * sin($sa1) * sin($sa2)) - ($this->sectorFinish->distance * sin($fa1) * sin($fa2));
        $z = ($this->sectorStart->distance * cos($sa1)) - ($this->sectorFinish->distance * cos($fa1));
        $distance = round(sqrt(mypw($x, 2) + mypw($y, 2) + mypw($z, 2)));
        $shipspeed = mypw(BalanceEnum::level_factor->val(), $this->ship->engines);
        $triptime = round($distance / $shipspeed);

        if ($triptime == 0 && $this->destination != $this->ship->sector) {
            $triptime = 1;
        }

        if ($this->ship->dev_fuelscoop) {
            $energyscooped = $distance * 100;
        } else {
            $energyscooped = 0;
        }

        if ($this->ship->dev_fuelscoop && $energyscooped == 0 && $triptime == 1) {
            $energyscooped = 100;
        }
        $free_power = $this->ship->getFreePower();

        // amount of energy that can be stored is less than amount scooped amount scooped is set to what can be stored
        if ($free_power < $energyscooped) {
            $energyscooped = $free_power;
        }

        // make sure energyscooped is not null
        if (!isset($energyscooped)) {
            $energyscooped = "0";
        }

        // make sure energyscooped not negative, or decimal
        if ($energyscooped < 1) {
            $energyscooped = 0;
        }

        // check to see if already in that sector
        if ($this->destination == $this->ship->sector) {
            $triptime = 0;
            $energyscooped = 0;
        }

        if ($triptime > $this->ship->turns) {
            $this->ship->cleared_defences = null;
            $this->retval = "BREAK-TURNS";
        } else {
            $this->hostile = false;

            $retrieveDefence = new SectorDefenceRetrieveManyByCriteriaDAO;
            $retrieveDefence->sector_id = $this->destination;
            $retrieveDefence->excludeShipId = $this->ship->ship_id;
            $retrieveDefence->limit = 1;
            $retrieveDefence->serve();

            $defence = $retrieveDefence->firstOfDefences;

            if ($defence) {
                $nsfighters = ShipRetrieveByIdDAO::call($defence->ship_id);

                if ($nsfighters->team != $this->ship->team || $this->ship->team == 0) {
                    $this->hostile = true;
                }
            }
            
            if ($this->hostile && ($this->ship->hull > BalanceEnum::mine_hullsize->val())) {
                $this->retval = "HOSTILE";
            } else {
                $this->ship->last_login = new \DateTime;
                $this->ship->sector = $this->destination;
                $this->ship->ship_energy += $energyscooped;
                $this->ship->turns -= $triptime;
                $this->ship->turns_used += $triptime;

                $this->retval = "GO";
            }
        }

        $this->doIt();
    }

    private function doIt(): void
    {
        if (!$this->doIt()) {
            return;
        }

        ShipSaveDAO::call($this->ship);
    }

}
