<?php

declare(strict_types=1);

namespace BNT\Ship\Servant;

use BNT\Planet\DAO\PlanetByIdDAO;
use BNT\Sector\DAO\SectorByIdDAO;
use BNT\Link\DAO\LinksByStartDAO;
use BNT\Planet\DAO\PlanetsBySectorDAO;
use BNT\SectorDefence\DAO\SectorDefencesBySectorDAO;
use BNT\Zone\DAO\ZoneByIdDAO;
use BNT\Traderoute\DAO\TraderoutesBySectorAndShipDAO;
use BNT\Ship\DAO\ShipsInSectorDAO;
use BNT\Ship\Exception\ShipExploreSectorNotAllowOnPlanetException;
use BNT\Ship\DAO\ShipUpdateDAO;

class ShipExploreSectorServant extends \UUA\Servant
{

    /**
     * @var array<string, mixed>
     */
    public array $ship;

    /**
     * @var array<string, mixed>
     */
    public array $sector = [];
    public $links;
    public array $planets = [];
    public array $sectorDefences = [];
    public array $zone = [];
    public array $traderoutes = [];
    public array $ships = [];
    public array $messages = [];

    #[\Override]
    public function serve(): void
    {
        global $l_nonexistant_pl;

        $this->messages = [];

        if ($this->ship['on_planet'] == 'Y') {
            $planet = PlanetByIdDAO::call($this->container, $this->ship['planet_id'])->planet;
 
            if ($planet) {
                throw new ShipExploreSectorNotAllowOnPlanetException($this->ship['planet_id']);
            } else {
                $this->ship['on_planet'] = 'N';

                ShipUpdateDAO::call($this->container, $this->ship, $this->ship['ship_id']);

                $this->messages[] = $l_nonexistant_pl;
            }
        }

        $sector = SectorByIdDAO::call($this->container, $this->ship['sector'])->sector;

        if (!$sector) {
            return;
        }

        $this->sector = $sector;

        $linkByStart = LinksByStartDAO::new($this->container);
        $linkByStart->start = $this->ship['sector'];
        $linkByStart->serve();

        $planetsBySector = PlanetsBySectorDAO::new($this->container);
        $planetsBySector->sector = $this->ship['sector'];
        $planetsBySector->serve();

        $defencesBySector = SectorDefencesBySectorDAO::new($this->container);
        $defencesBySector->sector = $this->ship['sector'];
        $defencesBySector->serve();

        $traderoutesBySectorAndShip = TraderoutesBySectorAndShipDAO::new($this->container);
        $traderoutesBySectorAndShip->sector = $this->ship['sector'];
        $traderoutesBySectorAndShip->ship = $this->ship['ship_id'];
        $traderoutesBySectorAndShip->serve();

        $shipsInSector = ShipsInSectorDAO::new($this->container);
        $shipsInSector->sector = $this->ship['sector'];
        $shipsInSector->excludeShip = $this->ship['ship_id'];
        $shipsInSector->serve();

        $this->zone = ZoneByIdDAO::call($this->container, $this->sector['zone_id'])->zone;
        $this->links = $linkByStart->links;
        $this->planets = $planetsBySector->planets;
        $this->sectorDefences = $defencesBySector->sectorDefences;
        $this->traderoutes = $traderoutesBySectorAndShip->traderoutes;
        $this->ships = $shipsInSector->ships;
    }
}
