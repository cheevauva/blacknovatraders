<?php

declare(strict_types=1);

namespace Tests\Unit\Game\Servant;

use BNT\Game\Servant\GameAttackShipServant;
use BNT\Ship\Ship;
use BNT\Ship\DAO\ShipByIdDAO;
use BNT\Ship\DAO\ShipGenScoreDAO;
use BNT\Ship\DAO\ShipUpdateDAO;
use BNT\Sector\DAO\SectorByIdDAO;
use BNT\Zone\DAO\ZoneByIdDAO;
use BNT\Log\DAO\LogPlayerDAO;

class GameAttackShipServantTest extends \Tests\UnitTestCase
{

    public static string $allow_attack;
    public static Ship $shipAttack;
    public static Ship $shipUnderAttack;
    public static int $roll;
    public static int $roll2;
    public static int $randomValue;

    #[\Override]
    protected function setUp(): void
    {
        self::$roll = 45;
        self::$roll2 = 45;
        self::$randomValue = 45;
        self::$allow_attack = 'Y';
        self::$shipAttack = GameAttackShipServantTest::ship(1, 'Attacker', [
            'engines' => 10,
        ]);
        self::$shipUnderAttack = GameAttackShipServantTest::ship(2, 'UnderAttacked', [
            'engines' => 1,
        ]);
    }

    public static function ship(int $id, string $name, array $default = []): Ship
    {
        $ship = [
            'ship_id' => $id,
            'ship_name' => $name,
        ];
        $ship['hull'] = 0;
        $ship['engines'] = $default['engines'] ?? 0;
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
        $ship['ship_fighters'] = $default['ship_fighters'] ?? 0;
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

        return new Ship($ship);
    }

    public function testZoneNotAllowAttack(): void
    {
        self::$allow_attack = 'N';

        $attackShip = GameAttackShipServant::new($this->container());
        $attackShip->playerinfo = self::$shipAttack->ship;
        $attackShip->ship = self::$shipUnderAttack->id;
        $attackShip->serve();

        self::assertEquals('l_att_noatt', strval($attackShip->messages[0] ?? ''));
    }

    public function testFlee(): void
    {
        self::$shipUnderAttack = GameAttackShipServantTest::ship(2, 'UnderAttacked', [
            'engines' => 10,
        ]);

        $attackShip = GameAttackShipServant::new($this->container());
        $attackShip->playerinfo = self::ship(1, 'Attacker', [
            'engines' => 1,
        ])->ship;
        $attackShip->ship = 2;
        $attackShip->serve();

        self::assertEquals('l_att_flee', strval($attackShip->messages[0] ?? ''));
    }

    public function testMain(): void
    {
        $attackShip = GameAttackShipServant::new($this->container());
        $attackShip->playerinfo = self::ship(1, 'Attacker')->ship;
        $attackShip->ship = 2;
        $attackShip->serve();
    }

    #[\Override]
    protected function stubs(): array
    {
        $stubs = [
            GameAttackShipServant::class => fn($c) => new class($c) extends GameAttackShipServant {

                #[\Override]
                protected function roll(): int
                {
                    return GameAttackShipServantTest::$roll;
                }

                #[\Override]
                protected function roll2(): int
                {
                    return GameAttackShipServantTest::$roll2;
                }

                #[\Override]
                protected function randomValue(): int
                {
                    return GameAttackShipServantTest::$randomValue;
                }
            },
            ShipByIdDAO::class => fn($c) => new class($c) extends ShipByIdDAO {

                #[\Override]
                public function serve(): void
                {
                    if ($this->id === 1) {
                        $this->ship = GameAttackShipServantTest::$shipAttack->ship;
                    }

                    if ($this->id === 2) {
                        $this->ship = GameAttackShipServantTest::$shipUnderAttack->ship;
                    }
                }
            },
            ShipGenScoreDAO::class => fn($c) => new class($c) extends ShipGenScoreDAO {

                #[\Override]
                public function serve(): void
                {
                    $this->score = 10;
                }
            },
            ShipUpdateDAO::class => fn($c) => new class($c) extends ShipUpdateDAO {

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
                        'allow_attack' => GameAttackShipServantTest::$allow_attack,
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
