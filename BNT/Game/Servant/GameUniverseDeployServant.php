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

class GameUniverseDeployServant extends \UUA\Servant
{

    public string $admin_mail;
    public string $admin_pass;
    public int $sectorMax;
    public int $universeSize;
    public GameCalculateStartParamsServant $startParams;
    public array $messages;
    public bool $success = false;

    #[\Override]
    public function serve(): void
    {
        global $fed_max_hull;

        $startParams = $this->startParams;

        $sectorsGenerate = SectorGenerateDAO::new($this->container);
        $sectorsGenerate->zone = 1;
        $sectorsGenerate->sectorMax = $this->sectorMax;
        $sectorsGenerate->universe_size = $this->universeSize;
        $sectorsGenerate->serve();

        $zonesSetMaxHullInZone2 = ZonesSetMaxHullDAO::new($this->container);
        $zonesSetMaxHullInZone2->zone = 2;
        $zonesSetMaxHullInZone2->fedMaxHull = $fed_max_hull;
        $zonesSetMaxHullInZone2->serve();

        $assignZone = SectorsAssignZoneDAO::new($this->container);
        $assignZone->zone = 2;
        $assignZone->lessThanSector = $startParams->fedSectorsCount;
        $assignZone->serve();

        $reassignSpecialPorts = SectorsReassignSpecialPortsDAO::new($this->container);
        $reassignSpecialPorts->specialSectorsCount = $startParams->specialSectorsCount;
        $reassignSpecialPorts->serve();

        $reassignResourcePorts = SectorsReassignResourcesPortsDAO::new($this->container);
        $reassignResourcePorts->energySectorsCount = $startParams->energySectorsCount;
        $reassignResourcePorts->goodsSectorsCount = $startParams->goodsSectorsCount;
        $reassignResourcePorts->organicsSectorsCount = $startParams->organicsSectorsCount;
        $reassignResourcePorts->oreSectorsCount = $startParams->oreSectorsCount;
        $reassignResourcePorts->buyGoods = $startParams->initBuyGoods;
        $reassignResourcePorts->buyOre = $startParams->initBuyOre;
        $reassignResourcePorts->buyOrganics = $startParams->initBuyOrganics;
        $reassignResourcePorts->buyEnergy = $startParams->initBuyEnergy;
        $reassignResourcePorts->serve();

        $planetsGenerate = PlanetsGenerateDAO::new($this->container);
        $planetsGenerate->unownedPlanetsCount = $startParams->unownedPlanetsCount;
        $planetsGenerate->serve();

        $linkTwoWayGenerate = LinksTwoWayGenerateDAO::new($this->container);
        $linkTwoWayGenerate->sectorMax = $this->sectorMax;
        $linkTwoWayGenerate->serve();

        $linkTwoWayRandomGenerate = LinksTwoWayGenerateRandomDAO::new($this->container);
        $linkTwoWayRandomGenerate->sectorMax = $this->sectorMax;
        $linkTwoWayRandomGenerate->serve();

        $linkOneWayGenerate = LinksOneWayGenerateDAO::new($this->container);
        $linkOneWayGenerate->sectorMax = $this->sectorMax;
        $linkOneWayGenerate->serve();

        LinksTwoWayBackGenerateDAO::call($this->container);
        
        $this->success = true;
    }
}
