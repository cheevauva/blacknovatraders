<?php

declare(strict_types=1);

namespace BNT\Ship\Servant;

use BNT\ServantInterface;
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

class ShipKillServant implements ServantInterface
{
    public Ship $ship;
    public bool $doIt = true;
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

        $retrieveBounties = new BountyRetrieveManyByCriteriaDAO;
        $retrieveBounties->placed_by = $this->ship->ship_id;
        $retrieveBounties->serve();

        $this->bountiesForRemove = $retrieveBounties->bounties;

        $retrievePlanets = new PlanetRetrieveManyByCriteriaDAO;
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

        $retrieveSectorDefences = new SectorDefenceRetrieveManyByCriteriaDAO;
        $retrieveSectorDefences->ship_id = $this->ship->ship_id;
        $retrieveSectorDefences->serve();

        $this->sectorDefencesForRemove = $retrieveSectorDefences->defences;

        $retrieveZone = new ZoneRetrieveByCriteriaDAO;
        $retrieveZone->corp = false;
        $retrieveZone->owner = $this->ship->ship_id;
        $retrieveZone->serve();

        $zone = $retrieveZone->zone;

        $retrieveSector = new SectorRetrieveByCriteriaDAO;
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

        $this->doIt();
    }

    private function doIt(): void
    {
        if (!$this->doIt) {
            return;
        }

        foreach ($this->bountiesForRemove as $bounty) {
            $removeBounty = new BountyRemoveByCriteriaDAO;
            $removeBounty->bounty_id = Bounty::as($bounty)->bounty_id;
            $removeBounty->serve();
        }

        foreach ($this->sectorDefencesForRemove as $defence) {
            $removeDefence = new SectorDefenceRemoveByCriteriaDAO;
            $removeDefence->defence_id = SectorDefence::as($defence)->defence_id;
            $removeDefence->serve();
        }

        foreach ($this->planetsForChange as $planet) {
            PlanetSaveDAO::call($planet);
        }

        foreach ($this->sectorsForChange as $sector) {
            SectorSaveDAO::call($sector);
        }

        ShipSaveDAO::call($this->ship);

        foreach ($this->news as $news) {
            NewsSaveDAO::call($news);
        }
    }

    public static function call(Ship $ship): void
    {
        $self = new static;
        $self->ship = $ship;
        $self->serve();
    }
}
