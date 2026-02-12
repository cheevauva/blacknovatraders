<?php

declare(strict_types=1);

namespace BNT\EntryPoint\Servant;

use BNT\Ship\DAO\ShipCreateDAO;
use BNT\Sector\DAO\SectorGenerateDAO;
use BNT\Zone\DAO\ZonesSetMaxHullDAO;
use BNT\Sector\DAO\SectorsReassignSpecialPortsDAO;
use BNT\Sector\DAO\SectorsReassignResourcesPortsDAO;
use BNT\Link\DAO\LinksTwoWayGenerateDAO;
use BNT\Link\DAO\LinksTwoWayGenerateRandomDAO;
use BNT\Link\DAO\LinksOneWayGenerateDAO;
use BNT\Link\DAO\LinksTwoWayBackGenerateDAO;
use BNT\Scheduler\DAO\SchedulersDefaultGenerateDAO;
use BNT\Planet\DAO\PlanetsGenerateDAO;
use BNT\Sector\DAO\SectorsAssignZoneDAO;
use BNT\Game\Servant\GameCalculateStartParamsServant;
use BNT\Zone\DAO\ZoneCreateDAO;
use BNT\IBankAccount\DAO\IBankAccountCreateDAO;

class EntryPointCreateUniverseStep2Servant extends \UUA\Servant
{

    public int $sectorMax;
    public int $universeSize;
    public GameCalculateStartParamsServant $startParams;

    public function serve(): void
    {
        global $fed_max_hull;
        global $admin_pass;
        global $admin_mail;
        global $start_armor;
        global $start_credits;
        global $start_energy;
        global $start_fighters;
        global $start_turns;
        global $language;
        
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
        SchedulersDefaultGenerateDAO::call($this->container);

        $shipAdminId = ShipCreateDAO::call($this->container, [
            'ship_name' => 'WebMaster',
            'character_name' => 'WebMaster',
            'password' => md5($admin_pass),
            'email' => $admin_mail,
            'armor_pts' => $start_armor,
            'credits' => $start_credits,
            'ship_energy' => $start_energy,
            'ship_fighters' => $start_fighters,
            'turns' => $start_turns,
            'last_login' => date('Y-m-d H:i:s'),
            'lang' => $language,
            'role' => 'admin'
        ])->id;

        ZoneCreateDAO::call($this->container, [
            'zone_name' => 'WebMaster\'s Territory',
            'owner' => $shipAdminId,
        ]);
        IBankAccountCreateDAO::call($this->container, [
            'ship_id' => $shipAdminId,
        ]);
    }
}
