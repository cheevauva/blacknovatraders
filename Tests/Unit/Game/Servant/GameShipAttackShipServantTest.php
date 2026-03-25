<?php

declare(strict_types=1);

namespace Tests\Unit\Game\Servant;

use BNT\Game\Servant\GameShipAttackShipServant;
use BNT\Ship\DAO\ShipGenScoreDAO;
use BNT\Sector\DAO\SectorByIdDAO;
use BNT\Zone\DAO\ZoneByIdDAO;
use BNT\Log\DAO\LogPlayerDAO;
use BNT\Ship\Servant\ShipSaveServant;
use BNT\Game\Servant\GameShipScanShipServant;
use BNT\Game\Servant\GameShipEscapedFromShipServant;
use BNT\Game\Servant\GameShipEmergencyWarpServant;

class GameShipAttackShipServantTest extends \Tests\UnitTestCase
{

    public static string $allow_attack;
    public static array $shipAttack;
    public static array $shipUnderAttack;
    public static bool $scanSuccess;
    public static bool $escapeFromShipSuccess;
    public static bool $emergencyWarpSuccess;

    #[\Override]
    protected function setUp(): void
    {
        self::$scanSuccess = true;
        self::$escapeFromShipSuccess = false;
        self::$emergencyWarpSuccess = false;
        self::$allow_attack = 'Y';
        self::$shipAttack = GameShipAttackShipServantTest::ship(1, 'Attacker', [
            'engines' => 10,
        ]);
        self::$shipUnderAttack = GameShipAttackShipServantTest::ship(2, 'UnderAttacked', [
            'engines' => 1,
        ]);
    }

    public static function ship(int $id, string $name, array $default = []): array
    {
        $ship = [
            'ship_id' => $id,
            'ship_name' => $name,
        ];
        $ship['hull'] = 0;
        $ship['engines'] = 0;
        $ship['power'] = 0;
        $ship['sensors'] = 0;
        $ship['computer'] = 0;
        $ship['beams'] = 3;
        $ship['torp_launchers'] = 100;
        $ship['torps'] = 50;
        $ship['armor'] = 0;
        $ship['armor_pts'] = 900;
        $ship['cloak'] = 0;
        $ship['shields'] = 1;
        $ship['sector'] = 0;
        $ship['ship_organics'] = 0;
        $ship['ship_ore'] = 0;
        $ship['ship_goods'] = 0;
        $ship['ship_energy'] = 1000;
        $ship['ship_colonists'] = 0;
        $ship['ship_fighters'] = 0;
        $ship['dev_warpedit'] = 0;
        $ship['dev_genesis'] = 0;
        $ship['dev_beacon'] = 0;
        $ship['dev_emerwarp'] = 0;
        $ship['dev_escapepod'] = 'Y';
        $ship['dev_fuelscoop'] = 'N';
        $ship['dev_minedeflector'] = 0;
        $ship['ship_destroyed'] = 'N';
        $ship['on_planet'] = 'N';
        $ship['cleared_defences'] = '';
        $ship['dev_lssd'] = 'N';
        $ship['turns'] = 100;
        $ship['turns_used'] = 0;

        return array_merge($ship, $default);
    }

    protected function attack(): GameShipAttackShipServant
    {
        $attackShip = GameShipAttackShipServant::new($this->container());
        $attackShip->playerinfo = self::$shipAttack;
        $attackShip->targetinfo = self::$shipUnderAttack;
        $attackShip->serve();

        return $attackShip;
    }

    public function testZoneNotAllowAttack(): void
    {
        self::$allow_attack = 'N';

        self::assertEquals('l_att_noatt', strval($this->attack()->messages[0] ?? ''));
    }

    public function testOutman(): void
    {
        self::$escapeFromShipSuccess = true;

        self::assertEquals('l_att_flee', strval($this->attack()->messages[0] ?? ''));
    }

    public function testOutscan(): void
    {
        self::$scanSuccess = false;

        self::assertEquals('l_planet_noscan', strval($this->attack()->messages[0] ?? ''));
    }

    protected function testEmergencyWarp(): void
    {
        self::$emergencyWarpSuccess = true;

        self::assertEquals('l_att_ewd', strval($this->attack()->messages[0] ?? ''));
    }

    protected function testMain(): void
    {
        $attackShip = GameShipAttackShipServant::new($this->container());
        $attackShip->playerinfo = self::ship(1, 'Attacker');
        $attackShip->targetinfo = self::$shipUnderAttack;
        $attackShip->serve();
    }

    #[\Override]
    protected function stubs(): array
    {
        $stubs = [
            GameShipEscapedFromShipServant::class => fn($c) => new class($c) extends GameShipEscapedFromShipServant {

                #[\Override]
                public function serve(): void
                {
                    $this->isSuccess = GameShipAttackShipServantTest::$escapeFromShipSuccess;
                }
            },
            GameShipScanShipServant::class => fn($c) => new class($c) extends GameShipScanShipServant {

                #[\Override]
                public function serve(): void
                {
                    $this->isSuccess = GameShipAttackShipServantTest::$scanSuccess;
                }
            },
            GameShipEmergencyWarpServant::class => fn($c) => new class($c) extends GameShipEmergencyWarpServant {

                #[\Override]
                public function serve(): void
                {
                    $this->isSuccess = GameShipAttackShipServantTest::$emergencyWarpSuccess;
                }
            },
            ShipGenScoreDAO::class => fn($c) => new class($c) extends ShipGenScoreDAO {

                #[\Override]
                public function serve(): void
                {
                    $this->score = 10;
                }
            },
            ShipSaveServant::class => fn($c) => new class($c) extends ShipSaveServant {

                #[\Override]
                public function serve(): void
                {
                    
                }
            },
            SectorByIdDAO::class => fn($c) => new class($c) extends SectorByIdDAO {

                #[\Override]
                public function serve(): void
                {
                    $this->sector = [
                        'sector' => 1,
                        'zone_id' => 1,
                    ];
                }
            },
            ZoneByIdDAO::class => fn($c) => new class($c) extends ZoneByIdDAO {

                #[\Override]
                public function serve(): void
                {
                    $this->zone = [
                        'zone_id' => 1,
                        'allow_attack' => GameShipAttackShipServantTest::$allow_attack,
                    ];
                }
            },
            LogPlayerDAO::class => fn($c) => new class($c) extends LogPlayerDAO {

                #[\Override]
                public function serve(): void
                {
                    
                }
            },
        ];
        return array_merge(parent::stubs(), $stubs);
    }
}
