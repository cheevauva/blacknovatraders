<?php

declare(strict_types=1);

namespace BNT\Ship\Servant;

use Psr\Container\ContainerInterface;
use BNT\Servant;
use BNT\Ship\Entity\Ship;
use BNT\Ship\DAO\ShipSaveDAO;
use BNT\Bounty\Bounty;
use BNT\Bounty\DAO\BountyRetrieveManyByCriteriaDAO;
use BNT\Bounty\DAO\BountyRemoveByCriteriaDAO;
use BNT\Planet\Entity\Planet;
use BNT\Planet\DAO\PlanetRetrieveManyByCriteriaDAO;
use BNT\Planet\DAO\PlanetSaveDAO;
use BNT\SectorDefence\Entity\SectorDefence;
use BNT\SectorDefence\DAO\SectorDefenceRemoveByCriteriaDAO;
use BNT\SectorDefence\DAO\SectorDefenceRetrieveManyByCriteriaDAO;
use BNT\Zone\DAO\ZoneRetrieveByCriteriaDAO;
use BNT\Sector\DAO\SectorRetrieveByCriteriaDAO;
use BNT\Sector\DAO\SectorSaveDAO;
use BNT\News\Entity\News;
use BNT\News\DAO\NewsSaveDAO;

class ShipKillServant extends Servant
{

    public Ship $ship;
    public array $news = [];
    public array $sectorsForChange = [];
    public array $planetsForChange = [];
    public array $bountiesForRemove = [];
    public array $sectorDefencesForRemove = [];

    public function serve(): void
    {
        global $gameroot;
        global $l_killheadline;
        global $l_news_killed;

        $this->ship->ship_destroyed = true;
        $this->ship->on_planet = false;
        $this->ship->sector = 0;
        $this->ship->cleared_defences = null;

        $retrieveBounties = BountyRetrieveManyByCriteriaDAO::new($this->container);
        $retrieveBounties->placed_by = $this->ship->ship_id;
        $retrieveBounties->serve();

        $this->bountiesForRemove = $retrieveBounties->bounties;

        $retrievePlanets = PlanetRetrieveManyByCriteriaDAO::new($this->container);
        $retrievePlanets->owner = $this->ship->ship_id;
        $retrievePlanets->serve();

        $sectorsWithBase = [];

        foreach ($retrievePlanets->planets as $planet) {
            $planet = Planet::as($planet);

            if ($planet->base) {
                $sectorsWithBase[] = $planet->sector_id;
            }

            $planet->owner = 0;
            $planet->fighters = 0;
            $planet->base = false;

            $this->planetsForChange[] = $planet;
        }

        foreach (array_unique($sectorsWithBase) as $sector) {
            calc_ownership($sector);
        }

        $retrieveSectorDefences = SectorDefenceRetrieveManyByCriteriaDAO::new($this->container);
        $retrieveSectorDefences->ship_id = $this->ship->ship_id;
        $retrieveSectorDefences->serve();

        $this->sectorDefencesForRemove = $retrieveSectorDefences->defences;

        $retrieveZone = ZoneRetrieveByCriteriaDAO::new($this->container);
        $retrieveZone->corp = false;
        $retrieveZone->owner = $this->ship->ship_id;
        $retrieveZone->serve();

        $zone = $retrieveZone->zone;

        $retrieveSector = SectorRetrieveByCriteriaDAO::new($this->container);
        $retrieveSector->zone_id = $zone->zone_id;
        $retrieveSector->serve();

        if (!empty($retrieveSector->sector)) {
            $sector = $retrieveSector->sector;
            $sector->zone_id = 1;

            $this->sectorsForChange[] = $sector;
        }

        $news = new News;
        $news->headline = $this->ship->character_name . $l_killheadline;
        $news->newstext = str_replace("[name]", $this->ship->character_name, $l_news_killed);
        $news->user_id = $this->ship->ship_id;
        $news->news_type = 'killed';

        $this->news[] = $news;

        foreach ($this->bountiesForRemove as $bounty) {
            $removeBounty = BountyRemoveByCriteriaDAO::new($this->container);
            $removeBounty->bounty_id = Bounty::as($bounty)->bounty_id;
            $removeBounty->serve();
        }

        foreach ($this->sectorDefencesForRemove as $defence) {
            $removeDefence = SectorDefenceRemoveByCriteriaDAO::new($this->container);
            $removeDefence->defence_id = SectorDefence::as($defence)->defence_id;
            $removeDefence->serve();
        }

        foreach ($this->planetsForChange as $planet) {
            PlanetSaveDAO::call($this->container, $planet);
        }

        foreach ($this->sectorsForChange as $sector) {
            SectorSaveDAO::call($this->container, $sector);
        }

        ShipSaveDAO::call($this->container, $this->ship);

        foreach ($this->news as $news) {
            NewsSaveDAO::call($this->container, $news);
        }
    }

    public static function call(ContainerInterface $container, Ship $ship): void
    {
        $self = static::new($container);
        $self->ship = $ship;
        $self->serve();
    }
}
