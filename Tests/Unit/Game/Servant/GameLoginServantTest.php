<?php

declare(strict_types=1);

namespace Tests\Unit\Game\Servant;

use BNT\Log\LogTypeConstants;
use BNT\Log\DAO\LogPlayerDAO;
use BNT\Game\Servant\GameLoginServant;
use BNT\Ship\DAO\ShipByEmailDAO;
use BNT\Ship\DAO\ShipUpdateDAO;
use BNT\UUID;

class GameLoginServantTest extends \Tests\UnitTestCase
{

    public static array $logs;
    public static ?array $ship = null;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();

        self::$logs = [];
        self::$ship = [
            'ship_id' => 1,
            'email' => 'e@a.com',
            'password' => md5('pass'),
            'token' => UUID::v7(),
            'ship_destroyed' => 'N',
        ];
    }

    public function testLoginSuccess(): void
    {
        global $ip;

        $ip = 1;

        $login = GameLoginServant::new(self::$container);
        $login->email = 'e@a.com';
        $login->password = 'pass';
        $login->serve();

        self::assertEquals([LogTypeConstants::LOG_LOGIN, 1, 1], self::$logs[0] ?? null);
    }

    public function testEmailNotFound(): void
    {
        global $l_login_noone;

        self::$ship = null;

        $this->expectExceptionMessage($l_login_noone);

        $login = GameLoginServant::new(self::$container);
        $login->email = 'e@a.com';
        $login->password = 'pass';
        $login->serve();
    }

    public function testPasswordWrong(): void
    {
        global $l_login_4gotpw1;
        global $ip;

        $ip = 1;

        $this->expectExceptionMessage($l_login_4gotpw1);

        try {
            $login = GameLoginServant::new(self::$container);
            $login->email = 'e@a.com';
            $login->password = 'pass1';
            $login->serve();
        } catch (\Exception $ex) {
            self::assertEquals([LogTypeConstants::LOG_BADLOGIN, 1, 1], self::$logs[0] ?? null);
            throw $ex;
        }
    }

    #[\Override]
    protected function stubs(): array
    {
        return [
            ShipUpdateDAO::class => fn($c) => new class($c) extends ShipUpdateDAO {

                #[\Override]
                public function serve(): void
                {
                    
                }
            },
            LogPlayerDAO::class => fn($c) => new class($c) extends LogPlayerDAO {

                #[\Override]
                public function serve(): void
                {
                    GameLoginServantTest::$logs[] = [$this->type, $this->ship, $this->data];
                }
            },
            ShipByEmailDAO::class => fn($c) => new class($c) extends ShipByEmailDAO {

                #[\Override]
                public function serve(): void
                {
                    $this->ship = GameLoginServantTest::$ship;
                }
            },
        ];
    }
}
