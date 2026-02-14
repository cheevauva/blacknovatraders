<?php

declare(strict_types=1);

namespace BNT\EntryPoint\Servant;

use BNT\Planet\DAO\PlanetByIdDAO;
use BNT\Sector\DAO\SectorByIdDAO;
use BNT\Link\DAO\LinksByStartDAO;
use BNT\Planet\DAO\PlanetsBySectorDAO;
use BNT\SectorDefence\DAO\SectorDefencesBySectorDAO;
use BNT\Zone\DAO\ZoneByIdDAO;
use BNT\Traderoute\DAO\TraderoutesBySectorAndShipDAO;
use BNT\Ship\DAO\ShipsInSectorDAO;
use BNT\EntryPoint\Exception\EntryPointMainShipOnPlanetException;
use BNT\Ship\DAO\ShipUpdateDAO;

class EntryPointMainServant extends \UUA\Servant
{

    /**
     * @var array<string, mixed>
     */
    public array $playerinfo;
    public $links;

    /**
     * @var array<string, mixed>
     */
    public array $sector = [];
    public array $planets = [];
    public array $sectorDefences = [];
    public array $zone = [];
    public array $traderoutes  = [];
    public array $ships = [];
    public array $messages = [];

    #[\Override]
    public function serve(): void
    {
        global $l_nonexistant_pl;

        $this->messages = [];

        if ($this->playerinfo['on_planet'] == 'Y') {
            $planet = PlanetByIdDAO::call($this->container, $this->playerinfo['planet_id'])->planet;

            if ($planet) {
                $shipOnPlanet = new EntryPointMainShipOnPlanetException();
                $shipOnPlanet->planet = $this->playerinfo['planet_id'];

                throw $shipOnPlanet;
            } else {
                $this->playerinfo['on_planet'] = 'N';

                ShipUpdateDAO::call($this->container, $this->playerinfo, $this->playerinfo['ship_id']);

                $this->messages[] = $l_nonexistant_pl;
            }
        }

        $sector = SectorByIdDAO::call($this->container, $this->playerinfo['sector'])->sector;
 
        if (!$sector) {
            return;
        }

        $this->sector = $sector;

        $linkByStart = LinksByStartDAO::new($this->container);
        $linkByStart->start = $this->playerinfo['sector'];
        $linkByStart->serve();

        $planetsBySector = PlanetsBySectorDAO::new($this->container);
        $planetsBySector->sector = $this->playerinfo['sector'];
        $planetsBySector->serve();

        $defencesBySector = SectorDefencesBySectorDAO::new($this->container);
        $defencesBySector->sector = $this->playerinfo['sector'];
        $defencesBySector->serve();

        $traderoutesBySectorAndShip = TraderoutesBySectorAndShipDAO::new($this->container);
        $traderoutesBySectorAndShip->sector = $this->playerinfo['sector'];
        $traderoutesBySectorAndShip->ship = $this->playerinfo['ship_id'];
        $traderoutesBySectorAndShip->serve();

        $shipsInSector = ShipsInSectorDAO::new($this->container);
        $shipsInSector->sector = $this->playerinfo['sector'];
        $shipsInSector->excludeShip = $this->playerinfo['ship_id'];
        $shipsInSector->serve();

        $this->zone = ZoneByIdDAO::call($this->container, $this->sector['zone_id'])->zone;
        $this->links = $linkByStart->links;
        $this->planets = $planetsBySector->planets;
        $this->sectorDefences = $defencesBySector->sectorDefences;
        $this->traderoutes = $traderoutesBySectorAndShip->traderoutes;
        $this->ships = $shipsInSector->ships;
    }
}
