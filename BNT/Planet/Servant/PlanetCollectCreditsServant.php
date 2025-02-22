<?php

declare(strict_types=1);

namespace BNT\Planet\Servant;

use BNT\Servant;
use BNT\Planet\Entity\Planet;
use BNT\Planet\DAO\PlanetRetrieveByIdDAO;
use BNT\Ship\Entity\Ship;
use BNT\Ship\Servant\ShipRealSpaceMoveServant;
use BNT\Planet\Servant\PlanetTakeCreditsServant;
use BNT\Enum\CommandEnum;

class PlanetCollectCreditsServant extends Servant
{
    public Ship $ship;
    public array $planetIds;
    public array $planets;
    public bool $doIt = true;

    #[\Override]
    public function serve(): void
    {
        $this->planets = [];

        foreach ($this->planetIds as $planetId) {
            $this->planets[] = PlanetRetrieveByIdDAO::call($this->container, $planetId);
        }

        // Sort the array so that it is in order of sectors, lowest number first, not closest

        usort($this->planets, function (Planet $a, Planet $b) {
            return $a->sector_id <=> $b->sector_id;
        });

        foreach ($this->planets as $planet) {
            $realSpaceMove = ShipRealSpaceMoveServant::new($this->container);
            $realSpaceMove->ship = $this->ship;
            $realSpaceMove->destination = $planet->sector_id;
            $realSpaceMove->doIt = $this->doIt;
            $realSpaceMove->serve();

            $cs = $realSpaceMove->retval;

            if ($cs == CommandEnum::hostile) {
                $cs = CommandEnum::go;
                continue;
            }

            if ($cs == CommandEnum::go) {
                $takeCredits = PlanetTakeCreditsServant::new($this->container);
                $takeCredits->doIt = $this->doIt;
                $takeCredits->planet = $planet;
                $takeCredits->ship = $this->ship;
                $takeCredits->serve();

                $cs = $takeCredits->retval;
            }
        }
    }
}
