<?php

declare(strict_types=1);

namespace Tests\Unit\Controller;

use BNT\Controller\LoginController;
use BNT\Game\Servant\GameLoginServant;

class LoginControllerTest extends \Tests\UnitTestCase
{

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();

        global $server_closed;
        global $gamepath;
        global $gamedomain;

        $gamepath = 'gamepath';
        $server_closed = false;
        $gamedomain = 'gamedomain';
    }

    protected function prepareLoginPOST(array $parsedData = []): LoginController
    {
        $login = LoginController::new(self::$container);
        $login->disablePrepareResponse = true;
        $login->requestMethod = 'POST';
        $login->parsedBody = $parsedData;
        $login->serve();

        return $login;
    }

    public function testLoginPage(): void
    {
        $login = LoginController::new(self::$container);
        $login->disablePrepareResponse = true;
        $login->requestMethod = 'GET';
        $login->serve();

        self::assertEquals('tpls/login.tpl.php', $login->template);
    }

    public function testLoginSuccess(): void
    {
        $login = $this->prepareLoginPOST([
            'email' => 'email@admin.com',
            'pass' => 'pass',
        ]);

        self::assertNotEmpty($login->responseCookies['token'] ?? null);
        self::assertEquals('main.php', $login->location);
    }

    public function testLoginClosed(): void
    {
        global $server_closed;
        global $l_login_closed_message;

        $server_closed = true;

        $login = $this->prepareLoginPOST();

        self::assertEquals($l_login_closed_message, $login->exception?->getMessage());
        self::assertEmpty($login->responseCookies);
        self::assertEmpty($login->location);
    }

    public function testWithoutEmail(): void
    {
        global $l_login_email;
        global $l_is_required;

        $login = $this->prepareLoginPOST();

        self::assertEquals($l_login_email . ' ' . $l_is_required, $login->exception?->getMessage());
        self::assertEmpty($login->responseCookies);
        self::assertEmpty($login->location);
    }

    public function testWithEmailAndWithoutPass(): void
    {
        global $l_login_pw;
        global $l_is_required;

        $login = $this->prepareLoginPOST([
            'email' => 'email@admin.com',
        ]);

        self::assertEquals($l_login_pw . ' ' . $l_is_required, $login->exception?->getMessage());
        self::assertEmpty($login->responseCookies);
        self::assertEmpty($login->location);
    }

    public function testWithInvalidEmail(): void
    {
        global $l_login_email;
        global $l_is_invalid;

        $login = $this->prepareLoginPOST([
            'email' => 'emailadmin.com',
        ]);

        self::assertEquals($l_login_email . ' ' . $l_is_invalid, $login->exception?->getMessage());
        self::assertEmpty($login->responseCookies);
        self::assertEmpty($login->location);
    }

    #[\Override]
    protected function stubs(): array
    {
        return [
            GameLoginServant::class => fn($c) => new class($c) extends GameLoginServant {

                #[\Override]
                public function serve(): void
                {
                    $this->user = [
                        'token' => 'token',
                        'ship_id' => 123,
                    ];
                }
            },
        ];
    }
}
