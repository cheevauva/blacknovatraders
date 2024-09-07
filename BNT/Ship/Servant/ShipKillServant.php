<?php

declare(strict_types=1);

namespace BNT\Ship\Servant;

use BNT\ServantInterface;
use BNT\Ship\Ship;
use BNT\Ship\DAO\ShipSaveDAO;
use BNT\Bounty\DAO\BountyRemoveByCriteriaDAO;
use BNT\Planet\Planet;
use BNT\Planet\DAO\PlanetRetrieveManyByCriteria;
use BNT\Planet\DAO\PlanetSaveDAO;
use BNT\SectorDefence\DAO\SectorDefenceRemoveByCriteriaDAO;
use BNT\Zone\DAO\ZoneRetrieveByCriteriaDAO;
use BNT\Sector\DAO\SectorRetrieveByCriteriaDAO;
use BNT\Sector\DAO\SectorSaveDAO;
use BNT\News\News;
use BNT\News\DAO\NewsSaveDAO;

class ShipKillServant implements ServantInterface
{

    public Ship $ship;

    public function serve(): void
    {
        global $gameroot;

        include("languages/english.inc");

        $this->ship->ship_destroyed = true;
        $this->ship->on_planet = false;
        $this->ship->sector = 0;
        $this->ship->cleared_defences = null;

        ShipSaveDAO::call($this->ship);

        $removeBounty = new BountyRemoveByCriteriaDAO;
        $removeBounty->placedBy = $this->ship->ship_id;
        $removeBounty->serve();

        $retrievePlanets = new PlanetRetrieveManyByCriteria;
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

            PlanetSaveDAO::call($planet);
        }

        foreach (array_unique($sectorsWithBase) as $sector) {
            calc_ownership($sector);
        }

        $removeSectorDefence = new SectorDefenceRemoveByCriteriaDAO;
        $removeSectorDefence->ship_id = $this->ship->ship_id;
        $removeSectorDefence->serve();

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

            SectorSaveDAO::call($sector);
        }
        
        $news = new News;
        $news->headline = $this->ship->character_name . $l_killheadline;
        $news->newstext = str_replace("[name]", $this->ship->character_name, $l_news_killed);
        $news->user_id = $this->ship->ship_id;
        $news->news_type = 'killed';

        NewsSaveDAO::call($news);
    }

    public static function call(Ship $ship): void
    {
        $self = new static;
        $self->ship = $ship;
        $self->serve();
    }

}
