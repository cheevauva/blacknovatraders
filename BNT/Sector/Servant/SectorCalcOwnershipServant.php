<?php

declare(strict_types=1);

namespace BNT\Sector\Servant;

use BNT\Servant;
use BNT\Planet\Entity\Planet;
use BNT\Planet\DAO\PlanetRetrieveManyByCriteriaDAO;
use BNT\Ship\DAO\ShipRetrieveByIdDAO;
use BNT\Ship\DAO\ShipRetrieveManyByCriteriaDAO;
use BNT\Sector\Entity\Sector;
use BNT\Sector\DAO\SectorRetrieveByIdDAO;
use BNT\Sector\DAO\SectorSaveDAO;
use BNT\Enum\BalanceEnum;
use BNT\DTO\CalcOwnershipDTO;
use BNT\Zone\Entity\Zone;
use BNT\Zone\DAO\ZoneRetrieveManyByCriteriaDAO;

class SectorCalcOwnershipServant extends Servant
{

    public int $sector_id;
    public Sector $sector;
    public array $planetsWithBaseOnSector;
    public array $ownerTypes;
    public array $ownerCorps;
    public array $ownerShips;
    public ?CalcOwnershipDTO $winner;

    public function serve(): void
    {
        $this->process();
        
        SectorSaveDAO::call($this->container, $this->sector);
    }

    protected function prepareOwnerTypes(): void
    {
        foreach ($this->planetsWithBaseOnSector as $planet) {
            $planet = Planet::as($planet);

            if (!empty($planet->corp) && empty($this->ownerCorps[$planet->corp])) {
                $ownerCorp = new CalcOwnershipDTO();
                $ownerCorp->id = $planet->owner;
                $ownerCorp->num = 1;
                $ownerCorp->type = CalcOwnershipDTO::TYPE_CORP;

                $this->ownerCorps[$planet->corp] = $ownerCorp;
            }

            if (!empty($planet->owner) && empty($this->ownerShips[$planet->owner])) {
                $ownerShip = new CalcOwnershipDTO();
                $ownerShip->id = $planet->owner;
                $ownerShip->num = 1;
                $ownerShip->type = CalcOwnershipDTO::TYPE_SHIP;

                $this->ownerShips[$planet->owner] = $ownerShip;
            }

            if (!empty($planet->corp) && !empty($this->ownerCorps[$planet->corp])) {
                CalcOwnershipDTO::as($this->ownerCorps[$planet->corp])->num++;
            }

            if (!empty($planet->owner) && !empty($this->ownerShips[$planet->owner])) {
                CalcOwnershipDTO::as($this->ownerShips[$planet->owner])->num++;
            }
        }
    }

    protected function process(): void
    {
        $this->sector = SectorRetrieveByIdDAO::call($this->container, $this->sector_id);

        $retrievePlanet = PlanetRetrieveManyByCriteriaDAO::new($this->container);
        $retrievePlanet->sector_id = $this->sector_id;
        $retrievePlanet->base = true;
        $retrievePlanet->serve();

        if ($retrievePlanet->planets) {
            return;
        }

        $this->planetsWithBaseOnSector = $retrievePlanet->planets;
        $this->prepareOwnerTypes();

        // We've got all the contenders with their bases.
        // Time to test for conflict

        $nbcorps = count($this->ownerCorps);
        $nbships = count($this->ownerShips);
        $ships = [];
        $scorps = [];

        foreach ($this->ownerShips as $ownerShip) {
            $ownerShip->team = ShipRetrieveByIdDAO::call($this->container, $ownerShip->id)->team;
            $scorps[] = $ownerShip->team;
            $ships[] = $ownerShip->id;
        }


        // More than one corp, war
        if ($nbcorps > 1) {
            $this->sector->zone_id = Zone::ZONE_ID_WAR;
            return;
        }

        // More than one unallied ship, war
        $numunallied = 0;

        foreach ($scorps as $corp) {
            if ($corp == 0) {
                $numunallied++;
            }
        }

        if ($numunallied > 1) {
            $this->sector->zone_id = Zone::ZONE_ID_WAR;
            return;
        }

        // Unallied ship, another corp present, war
        if ($numunallied > 0 && $nbcorps > 0) {
            $this->sector->zone_id = Zone::ZONE_ID_WAR;
            return;
        }

        if ($numunallied > 0) {
            $shipsWithTeam = ShipRetrieveManyByCriteriaDAO::new($this->container);
            $shipsWithTeam->ships = $ships;
            $shipsWithTeam->excludeTeam = 0;
            $shipsWithTeam->limit = 1;
            $shipsWithTeam->serve();

            if ($shipsWithTeam->firstOfShip) {
                $this->sector->zone_id = Zone::ZONE_ID_WAR;
                return;
            }
        }

        $this->winner = $this->makeWinner();

        if ($this->winner->num < BalanceEnum::min_bases_to_own->val()) {
            $this->sector->zone_id = Zone::ZONE_ID_UNCHARTERED_SPACE;
            return;
        }

        if ($this->winner->type == CalcOwnershipDTO::TYPE_CORP) {
            $retrieveZone = ZoneRetrieveManyByCriteriaDAO::new($this->container);
            $retrieveZone->corp_zone = true;
            $retrieveZone->owner = $this->winner->id;
            $retrieveZone->limit = 1;
            $retrieveZone->serve();

            $this->sector->zone_id = $retrieveZone->firstOfZones->zone_id;
            return;
        } else {
            foreach ($this->ownerShips as $ownerShip) {
                $ownerShip = CalcOwnershipDTO::as($ownerShip);
                // Two allies have the same number of bases
                if ($ownerShip->id != $this->winner->id && $ownerShip->num == $this->winner->num) {
                    $this->sector->zone_id = Zone::ZONE_ID_UNCHARTERED_SPACE;
                    return;
                }
            }

            $retrieveZone2 = ZoneRetrieveManyByCriteriaDAO::new($this->container);
            $retrieveZone2->corp_zone = false;
            $retrieveZone2->owner = $this->winner->id;
            $retrieveZone2->limit = 1;
            $retrieveZone2->serve();

            $this->sector->zone_id = $retrieveZone->firstOfZones->zone_id;
        }
    }

    protected function makeWinner(): ?CalcOwnershipDTO
    {
        $shipWinner = null;
        $corpWinner = null;

        // Ok, all bases are allied at this point. Let's make a winner.

        foreach ($this->ownerShips as $ownerShip) {
            if (!$shipWinner) {
                $shipWinner = $ownerShip;
                continue;
            }

            $ownerShip = CalcOwnershipDTO::as($ownerShip);
            $shipWinner = CalcOwnershipDTO::as($shipWinner);

            if ($ownerShip->num > $shipWinner->num) {
                $shipWinner = $ownerShip;
                continue;
            }
        }

        foreach ($this->ownerCorps as $ownerCorp) {
            if (!$corpWinner) {
                $corpWinner = $ownerCorp;
                continue;
            }
            $ownerCorp = CalcOwnershipDTO::as($ownerCorp);

            if ($ownerCorp->num == $corpWinner->num) {
                $corpWinner = $ownerCorp;
                continue;
            }
        }

        return $corpWinner?->num >= $shipWinner?->num ? $corpWinner : $shipWinner;
    }

    public static function call(\Psr\Container\ContainerInterface $container, int $sector): self
    {
        $self = static::new($container);
        $self->sector_id = $sector;
        $self->serve();

        return $self;
    }
}
