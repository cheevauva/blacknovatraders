<?php

declare(strict_types=1);

namespace Tests\Unit\Game\Servant;

use BNT\Log\LogTypeConstants;
use BNT\Log\DAO\LogPlayerDAO;
use BNT\Game\Servant\GameLoginServant;
use BNT\User\DAO\UserByEmailDAO;
use BNT\User\DAO\UserUpdateDAO;
use BNT\Ship\DAO\ShipByIdDAO;
use BNT\UUID;

class GameLoginServantTest extends \Tests\UnitTestCase
{

    public static array $logs;
    public static ?array $ship;
    public static ?array $user;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();

        self::$logs = [];
        self::$user = [
            'id' => 444,
            'email' => 'e@a.com',
            'password' => md5('pass'),
            'token' => UUID::v7(),
            'ship_id' => 555,
        ];
        self::$ship = [
            'ship_id' => 555,
            'ship_destroyed' => 'N',
            'user_id' => 444,
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

        self::assertEquals([LogTypeConstants::LOG_LOGIN, 555, 1], self::$logs[0] ?? null);
    }

    public function testEmailNotFound(): void
    {
        self::$user = null;

        $this->expectExceptionMessage(self::$l->login_noone);

        $login = GameLoginServant::new(self::$container);
        $login->email = 'e@a.com';
        $login->password = 'pass';
        $login->serve();
    }

    public function testPasswordWrong(): void
    {
        global $ip;

        $ip = 1;

        $this->expectExceptionMessage(self::$l->login_4gotpw1);

        $login = GameLoginServant::new(self::$container);
        $login->email = 'e@a.com';
        $login->password = 'pass1';
        $login->serve();
    }

    #[\Override]
    protected function stubs(): array
    {
        return [
            UserUpdateDAO::class => fn($c) => new class($c) extends UserUpdateDAO {

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
            UserByEmailDAO::class => fn($c) => new class($c) extends UserByEmailDAO {

                #[\Override]
                public function serve(): void
                {
                    $this->user = GameLoginServantTest::$user;
                }
            },
            ShipByIdDAO::class => fn($c) => new class($c) extends ShipByIdDAO {

                #[\Override]
                public function serve(): void
                {
                    $this->ship = GameLoginServantTest::$ship;
                }
            },
        ];
    }
}
