<?php

declare(strict_types=1);

namespace Tests\Unit\Controller;

use BNT\Controller\CreateUniverseController;
use BNT\Config\DAO\ConfigUpdateDAO;
use BNT\Game\Servant\GameUniverseDeployServant;

class CreateUniverseControllerTest extends \Tests\UnitTestCase
{

    public static int $sched_igb = 664;
    public static int $sched_turns = 665;
    public static int $sched_ticks = 666;
    public static int $sector_max = 1000;
    public static int $universe_size = 200;
    public static int $ore_limit = 1000;
    public static int $organics_limit = 1000;
    public static int $goods_limit = 1000;
    public static int $energy_limit = 1000;
    public static string $admin_mail = 'admin@mail.com';
    public static string $admin_pass = 'admin_pass';
    public static string $adminpass = 'adminpass';
    public static ?array $configData;

    #[\Override]
    protected function setUp(): void
    {
        global $adminpass;
        global $admin_mail;
        global $admin_pass;
        global $sector_max;
        global $universe_size;
        global $ore_limit;
        global $organics_limit;
        global $goods_limit;
        global $energy_limit;
        global $sched_ticks;
        global $sched_turns;
        global $sched_igb;

        parent::setUp();

        $adminpass = self::$adminpass;
        $admin_mail = self::$admin_mail;
        $admin_pass = self::$admin_pass;
        $sector_max = self::$sector_max;
        $universe_size = self::$universe_size;
        $ore_limit = self::$ore_limit;
        $organics_limit = self::$organics_limit;
        $goods_limit = self::$goods_limit;
        $energy_limit = self::$energy_limit;
        $sched_ticks = self::$sched_ticks;
        $sched_turns = self::$sched_turns;
        $sched_igb = self::$sched_igb;

        self::$configData = null;
    }

    public function testInit(): void
    {
        $createUniverse = CreateUniverseController::new(self::$container);

        self::assertEquals(1000, $createUniverse->ore_limit);
        self::assertEquals(1000, $createUniverse->organics_limit);
        self::assertEquals(1000, $createUniverse->goods_limit);
        self::assertEquals(1000, $createUniverse->energy_limit);
        self::assertEquals(1000, $createUniverse->sector_max);
        self::assertEquals(200, $createUniverse->universe_size);
        self::assertEquals(5, $createUniverse->fedsecs);
    }

    public function testPrepareInputDefauts(): void
    {
        $createUniverse = CreateUniverseController::new(self::$container);
        $createUniverse->requestMethod = 'POST';
        $createUniverse->parsedBody = [];
        $createUniverse->serve();

        self::assertEquals(self::$sched_igb, $createUniverse->sched_igb);
        self::assertEquals(self::$sched_turns, $createUniverse->sched_turns);
        self::assertEquals(self::$sched_ticks, $createUniverse->sched_ticks);
        self::assertEquals(self::$ore_limit, $createUniverse->ore_limit);
        self::assertEquals(self::$organics_limit, $createUniverse->organics_limit);
        self::assertEquals(self::$goods_limit, $createUniverse->goods_limit);
        self::assertEquals(self::$energy_limit, $createUniverse->energy_limit);
        self::assertEquals(self::$sector_max, $createUniverse->sector_max);
        self::assertEquals(self::$universe_size, $createUniverse->universe_size);
        self::assertEquals(5, $createUniverse->fedsecs);
        self::assertEquals(1, $createUniverse->special);
        self::assertEquals(15, $createUniverse->ore);
        self::assertEquals(10, $createUniverse->organics);
        self::assertEquals(15, $createUniverse->goods);
        self::assertEquals(10, $createUniverse->energy);
        self::assertEquals(10, $createUniverse->planets);
        self::assertEquals(0, $createUniverse->step);
        self::assertEquals('tpls/create_universe/create_universe_login.tpl.php', $createUniverse->template);
    }

    public function testPrepareInputFromParsedBody(): void
    {
        $createUniverse = CreateUniverseController::new(self::$container);
        $createUniverse->requestMethod = 'POST';
        $createUniverse->parsedBody = [
            'sched_igb' => 228,
            'sched_turns' => 229,
            'sched_ticks' => 330,
            'ore_limit' => 331,
            'organics_limit' => 332,
            'goods_limit' => 333,
            'energy_limit' => 334,
            'sector_max' => 335,
            'universe_size' => 336,
            'fedsecs' => 337,
            'special' => 338,
            'ore' => 339,
            'organics' => 340,
            'goods' => 341,
            'energy' => 342,
            'planets' => 343,
            'step' => 344,
            'swordfish' => 'swordfish',
        ];
        $createUniverse->serve();

        self::assertEquals(228, $createUniverse->sched_igb);
        self::assertEquals(229, $createUniverse->sched_turns);
        self::assertEquals(330, $createUniverse->sched_ticks);
        self::assertEquals(331, $createUniverse->ore_limit);
        self::assertEquals(332, $createUniverse->organics_limit);
        self::assertEquals(333, $createUniverse->goods_limit);
        self::assertEquals(334, $createUniverse->energy_limit);
        self::assertEquals(335, $createUniverse->sector_max);
        self::assertEquals(336, $createUniverse->universe_size);
        self::assertEquals(337, $createUniverse->fedsecs);
        self::assertEquals(338, $createUniverse->special);
        self::assertEquals(339, $createUniverse->ore);
        self::assertEquals(340, $createUniverse->organics);
        self::assertEquals(341, $createUniverse->goods);
        self::assertEquals(342, $createUniverse->energy);
        self::assertEquals(343, $createUniverse->planets);
        self::assertEquals(344, $createUniverse->step);
        self::assertEquals('swordfish', $createUniverse->swordfish);
        self::assertEquals('tpls/create_universe/create_universe_login.tpl.php', $createUniverse->template);
    }

    public function testWrongPassword(): void
    {
        $createUniverse = CreateUniverseController::new(self::$container);
        $createUniverse->requestMethod = 'POST';
        $createUniverse->parsedBody = [
            'step' => 1,
            'swordfish' => 'wrong_pass',
        ];
        $createUniverse->serve();

        self::assertEmpty($createUniverse->startParams);
        self::assertEquals('tpls/create_universe/create_universe_login.tpl.php', $createUniverse->template);
    }

    public function testWrongStep(): void
    {
        $createUniverse = CreateUniverseController::new(self::$container);
        $createUniverse->requestMethod = 'POST';
        $createUniverse->parsedBody = [
            'step' => 111,
            'swordfish' => self::$adminpass,
        ];
        $createUniverse->serve();

        self::assertEmpty($createUniverse->startParams);
        self::assertEquals('tpls/create_universe/create_universe_login.tpl.php', $createUniverse->template);
    }

    public function testStep1(): void
    {
        $createUniverse = CreateUniverseController::new(self::$container);
        $createUniverse->requestMethod = 'POST';
        $createUniverse->parsedBody = [
            'step' => CreateUniverseController::STEP_1,
            'swordfish' => self::$adminpass,
        ];
        $createUniverse->serve();

        self::assertEmpty($createUniverse->startParams);
        self::assertEquals('tpls/create_universe/create_universe_step1.tpl.php', $createUniverse->template);
    }

    public function testStep2(): void
    {
        $createUniverse = CreateUniverseController::new(self::$container);
        $createUniverse->requestMethod = 'POST';
        $createUniverse->parsedBody = [
            'step' => CreateUniverseController::STEP_2,
            'swordfish' => self::$adminpass,
        ];
        $createUniverse->serve();

        self::assertNotEmpty($createUniverse->startParams);
        self::assertEquals('tpls/create_universe/create_universe_step2.tpl.php', $createUniverse->template);
        self::assertEquals([
            'sector_max' => self::$sector_max,
            'universe_size' => self::$universe_size,
            'organics_limit' => self::$organics_limit,
            'goods_limit' => self::$goods_limit,
            'ore_limit' => self::$ore_limit,
            'energy_limit' => self::$energy_limit,
            'sched_igb' => self::$sched_igb,
            'sched_ticks' => self::$sched_ticks,
            'sched_turns' => self::$sched_turns,
        ], self::$configData);
    }

    public function testStep3(): void
    {
        $createUniverse = CreateUniverseController::new(self::$container);
        $createUniverse->requestMethod = 'POST';
        $createUniverse->parsedBody = [
            'step' => CreateUniverseController::STEP_3,
            'swordfish' => self::$adminpass,
        ];
        $createUniverse->serve();

        self::assertNotEmpty($createUniverse->startParams);
        self::assertEquals('tpls/create_universe/create_universe_step3.tpl.php', $createUniverse->template);
    }

    #[\Override]
    protected function stubs(): array
    {
        return [
            CreateUniverseController::class => fn($c) => new class($c) extends CreateUniverseController {

                #[\Override]
                protected function prepareResponse(): void
                {
                    
                }
            },
            ConfigUpdateDAO::class => fn($c) => new class($c) extends ConfigUpdateDAO {

                #[\Override]
                public function serve(): void
                {
                    CreateUniverseControllerTest::$configData = $this->config;
                }
            },
            GameUniverseDeployServant::class => fn($c) => new class($c) extends GameUniverseDeployServant {

                #[\Override]
                public function serve(): void
                {
                    
                }
            },
        ];
    }
}
