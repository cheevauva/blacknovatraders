<?php

declare(strict_types=1);

namespace BNT\Game\Servant;

use BNT\Sector\DAO\SectorGenerateDAO;
use BNT\Zone\DAO\ZonesSetMaxHullDAO;
use BNT\Sector\DAO\SectorsReassignSpecialPortsDAO;
use BNT\Sector\DAO\SectorsReassignResourcesPortsDAO;
use BNT\Link\DAO\LinksTwoWayGenerateDAO;
use BNT\Link\DAO\LinksTwoWayGenerateRandomDAO;
use BNT\Link\DAO\LinksOneWayGenerateDAO;
use BNT\Link\DAO\LinksTwoWayBackGenerateDAO;
use BNT\Planet\DAO\PlanetsGenerateDAO;
use BNT\Sector\DAO\SectorsAssignZoneDAO;
use BNT\Game\Servant\GameCalculateStartParamsServant;
use BNT\Zone\ZoneConstants;

class GameUniverseDeployServant extends \UUA\Servant
{

    public int $sectorMax;
    public int $universeSize;
    public GameCalculateStartParamsServant $startParams;
    public bool $success = false;

    #[\Override]
    public function serve(): void
    {
        global $fed_max_hull;

        $startParams = $this->startParams;

        $setMaxHullForZone = ZonesSetMaxHullDAO::new($this->container);
        $setMaxHullForZone->zone = ZoneConstants::ZONE_ID_FEDERATION_SPACE;
        $setMaxHullForZone->fedMaxHull = $fed_max_hull;
        $setMaxHullForZone->serve();
        
        $sectorsGenerate = SectorGenerateDAO::new($this->container);
        $sectorsGenerate->zone = ZoneConstants::ZONE_ID_UNCHARTERED_SPACE;
        $sectorsGenerate->limit = $this->sectorMax;
        $sectorsGenerate->universe_size = $this->universeSize;
        $sectorsGenerate->serve();

        $assignZone = SectorsAssignZoneDAO::new($this->container);
        $assignZone->zone = ZoneConstants::ZONE_ID_FEDERATION_SPACE;
        $assignZone->lessThanSector = $startParams->fedSectorsCount;
        $assignZone->serve();

        $reassignSpecialPorts = SectorsReassignSpecialPortsDAO::new($this->container);
        $reassignSpecialPorts->zone = ZoneConstants::ZONE_ID_FREE_TRADE_SPACE;
        $reassignSpecialPorts->limit = $startParams->specialSectorsCount;
        $reassignSpecialPorts->serve();

        $reassignResourcePorts = SectorsReassignResourcesPortsDAO::new($this->container);
        $reassignResourcePorts->energySectorsLimit = $startParams->energySectorsCount;
        $reassignResourcePorts->goodsSectorsLimit = $startParams->goodsSectorsCount;
        $reassignResourcePorts->organicsSectorsLimit = $startParams->organicsSectorsCount;
        $reassignResourcePorts->oreSectorsLimit = $startParams->oreSectorsCount;
        $reassignResourcePorts->buyGoods = $startParams->initBuyGoods;
        $reassignResourcePorts->buyOre = $startParams->initBuyOre;
        $reassignResourcePorts->buyOrganics = $startParams->initBuyOrganics;
        $reassignResourcePorts->buyEnergy = $startParams->initBuyEnergy;
        $reassignResourcePorts->sellGoods = $startParams->initSellGoods;
        $reassignResourcePorts->sellOre = $startParams->initSellOre;
        $reassignResourcePorts->sellOrganics = $startParams->initSellOrganics;
        $reassignResourcePorts->sellEnergy = $startParams->initSellEnergy;
        $reassignResourcePorts->serve();

        $planetsGenerate = PlanetsGenerateDAO::new($this->container);
        $planetsGenerate->limit = $startParams->unownedPlanetsCount;
        $planetsGenerate->serve();

        $linkTwoWayGenerate = LinksTwoWayGenerateDAO::new($this->container);
        $linkTwoWayGenerate->limit = $this->sectorMax;
        $linkTwoWayGenerate->serve();

        $linkTwoWayRandomGenerate = LinksTwoWayGenerateRandomDAO::new($this->container);
        $linkTwoWayRandomGenerate->limit = $this->sectorMax;
        $linkTwoWayRandomGenerate->serve();

        $linkOneWayGenerate = LinksOneWayGenerateDAO::new($this->container);
        $linkOneWayGenerate->limit = $this->sectorMax;
        $linkOneWayGenerate->serve();

        LinksTwoWayBackGenerateDAO::call($this->container);

        $this->success = true;
    }
}
