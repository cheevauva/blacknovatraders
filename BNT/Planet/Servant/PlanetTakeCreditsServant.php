<?php

declare(strict_types=1);

namespace BNT\Planet\Servant;

use BNT\ServantInterface;
use BNT\Ship\Entity\Ship;
use BNT\Ship\DAO\ShipSaveDAO;
use BNT\Planet\Entity\Planet;
use BNT\Planet\DAO\PlanetSaveDAO;
use BNT\Enum\CommandEnum;
use BNT\Traits\BuildTrait;

class PlanetTakeCreditsServant implements ServantInterface
{
    use BuildTrait;
    
    public Ship $ship;
    public Planet $planet;
    public bool $doIt = true;
    public CommandEnum $retval;
    public int $creditsTaken;
    public int $creditsOnShip;
    public int $newShipCredits;

    #[\Override]
    public function serve(): void
    {
        global $l_unnamed;

        // Set the name for unamed planets to be "unnamed"
        if (empty($this->planet->name)) {
            $this->planet->name = $l_unnamed;
        }

        //verify player is still in same sector as the planet
        if ($this->ship->sector != $this->planet->sector_id) {
            $this->retval = CommandEnum::breakSector;
            return;
        }

        if ($this->ship->turns < 1) {
            $this->retval = CommandEnum::breakTurns;
            return;
        }

        $this->retval = CommandEnum::go;

        // verify player owns the planet to take credits from
        if ($this->planet->owner == $this->ship->ship_id) {
            // get number of credits from the planet and current number player has on ship
            $this->creditsTaken = $this->planet->credits;
            $this->creditsOnShip = $this->ship->credits;
            $this->newShipCredits = $this->creditsTaken + $this->creditsOnShip;

            $this->planet->credits = 0;

            $this->ship->credits = $this->newShipCredits;
            $this->ship->turn();
        }

        $this->doIt();
    }

    private function doIt(): void
    {
        if (!$this->doIt) {
            return;
        }

        ShipSaveDAO::call($this->ship);
        PlanetSaveDAO::call($this->planet);
    }
}
