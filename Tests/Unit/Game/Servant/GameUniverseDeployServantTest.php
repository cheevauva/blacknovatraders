<?php

declare(strict_types=1);

namespace Tests\Unit\Game\Servant;

use BNT\Game\Servant\GameCalculateStartParamsServant;
use BNT\Game\Servant\GameUniverseDeployServant;
use BNT\Sector\DAO\SectorGenerateDAO;
use BNT\Zone\DAO\ZonesSetMaxHullDAO;
use BNT\Sector\DAO\SectorsAssignZoneDAO;
use BNT\Sector\DAO\SectorsReassignSpecialPortsDAO;
use BNT\Sector\DAO\SectorsReassignResourcesPortsDAO;
use BNT\Planet\DAO\PlanetsGenerateDAO;
use BNT\Link\DAO\LinksTwoWayGenerateDAO;
use BNT\Link\DAO\LinksTwoWayGenerateRandomDAO;
use BNT\Link\DAO\LinksOneWayGenerateDAO;
use BNT\Link\DAO\LinksTwoWayBackGenerateDAO;

class GameUniverseDeployServantTest extends \Tests\UnitTestCase
{

    public static ?array $shipData;
    public static ?array $zoneData;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();

    }

    public function testMain()
    {
        global $fed_max_hull;

        $fed_max_hull = 10;

        $startParams = GameCalculateStartParamsServant::new(self::$container);
        $startParams->special = 2;
        $startParams->ore = 13;
        $startParams->organics = 15;
        $startParams->goods = 12;
        $startParams->fedsecs = 50;
        $startParams->energy = 20;
        $startParams->planets = 30;
        $startParams->buyCommod = 30;
        $startParams->sellCommod = 24;
        $startParams->sectorMax = 500;
        $startParams->serve();

        $universeDeploy = GameUniverseDeployServant::new(self::$container);
        $universeDeploy->admin_mail = 'admin@mail.ru';
        $universeDeploy->admin_pass = 'admin_pass';
        $universeDeploy->sectorMax = 500;
        $universeDeploy->universeSize = 200;
        $universeDeploy->startParams = $startParams;
        $universeDeploy->serve();
        
        self::assertTrue($universeDeploy->success);
    }

    #[\Override]
    protected function stubs(): array
    {
        return [
            SectorGenerateDAO::class => fn($c) => new class($c) extends SectorGenerateDAO {

                #[\Override]
                public function serve(): void
                {
                    $this->numbersOfCreatedSectors = $this->sectorMax;
                }
            },
            ZonesSetMaxHullDAO::class => fn($c) => new class($c) extends ZonesSetMaxHullDAO {

                #[\Override]
                public function serve(): void
                {
                    
                }
            },
            SectorsAssignZoneDAO::class => fn($c) => new class($c) extends SectorsAssignZoneDAO {

                #[\Override]
                public function serve(): void
                {
                    
                }
            },
            SectorsReassignSpecialPortsDAO::class => fn($c) => new class($c) extends SectorsReassignSpecialPortsDAO {

                #[\Override]
                public function serve(): void
                {
                    
                }
            },
            SectorsReassignResourcesPortsDAO::class => fn($c) => new class($c) extends SectorsReassignResourcesPortsDAO {

                #[\Override]
                public function serve(): void
                {
                    
                }
            },
            PlanetsGenerateDAO::class => fn($c) => new class($c) extends PlanetsGenerateDAO {

                #[\Override]
                public function serve(): void
                {
                    
                }
            },
            LinksTwoWayGenerateDAO::class => fn($c) => new class($c) extends LinksTwoWayGenerateDAO {

                #[\Override]
                public function serve(): void
                {
                    
                }
            },
            LinksTwoWayGenerateRandomDAO::class => fn($c) => new class($c) extends LinksTwoWayGenerateRandomDAO {

                #[\Override]
                public function serve(): void
                {
                    
                }
            },
            LinksOneWayGenerateDAO::class => fn($c) => new class($c) extends LinksOneWayGenerateDAO {

                #[\Override]
                public function serve(): void
                {
                    
                }
            },
            LinksTwoWayBackGenerateDAO::class => fn($c) => new class($c) extends LinksTwoWayBackGenerateDAO {

                #[\Override]
                public function serve(): void
                {
                    
                }
            },
        ];
    }
}
