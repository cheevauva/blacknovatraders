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

    public $playerinfo;
    public $links;
    public $sector;
    public $planets;
    public $sectorDefences;
    public $zone;
    public $traderoutes;
    public $ships;
    public $messages = [];

    public function serve(): void
    {
        global $l_nonexistant_pl;

        $this->messages = [];
        
        if ($this->playerinfo['on_planet'] == 'Y') {
            $planetById = PlanetByIdDAO::new($this->container);
            $planetById->id = $this->playerinfo['planet_id'];
            $planetById->serve();

            if ($planetById->planet) {
                $shipOnPlanet = new EntryPointMainShipOnPlanetException();
                $shipOnPlanet->planet = $this->playerinfo['planet_id'];

                throw $shipOnPlanet;
            } else {
                $this->playerinfo['on_planet'] = 'N';

                ShipUpdateDAO::call($this->container, $this->playerinfo);

                $this->messages[] = $l_nonexistant_pl;
            }
        }

        $sectorById = SectorByIdDAO::new($this->container);
        $sectorById->id = $this->playerinfo['sector'];
        $sectorById->serve();

        $this->sector = $sectorById->sector;

        $linkByStart = LinksByStartDAO::new($this->container);
        $linkByStart->start = $this->playerinfo['sector'];
        $linkByStart->serve();

        $planetsBySector = PlanetsBySectorDAO::new($this->container);
        $planetsBySector->sector = $this->playerinfo['sector'];
        $planetsBySector->serve();

        $defencesBySector = SectorDefencesBySectorDAO::new($this->container);
        $defencesBySector->sector = $this->playerinfo['sector'];
        $defencesBySector->serve();

        $zoneById = ZoneByIdDAO::new($this->container);
        $zoneById->id = $this->sector['zone_id'];
        $zoneById->serve();

        $traderoutesBySectorAndShip = TraderoutesBySectorAndShipDAO::new($this->container);
        $traderoutesBySectorAndShip->sector = $this->playerinfo['sector'];
        $traderoutesBySectorAndShip->ship = $this->playerinfo['ship_id'];
        $traderoutesBySectorAndShip->serve();

        $shipsInSector = ShipsInSectorDAO::new($this->container);
        $shipsInSector->sector = $this->playerinfo['sector'];
        $shipsInSector->excludeShip = $this->playerinfo['ship_id'];
        $shipsInSector->serve();

        $this->zone = $zoneById->zone;
        $this->links = $linkByStart->links;
        $this->planets = $planetsBySector->planets;
        $this->sectorDefences = $defencesBySector->sectorDefences;
        $this->traderoutes = $traderoutesBySectorAndShip->traderoutes;
        $this->ships = $shipsInSector->ships;
    }
}
