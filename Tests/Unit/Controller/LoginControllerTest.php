<?php

declare(strict_types=1);

namespace Tests\Unit\Controller;

use BNT\Controller\LoginController;
use BNT\Game\Servant\GameLoginServant;

class LoginControllerTest extends \Tests\UnitTestCase
{

    protected function prepareLoginPOST(array $parsedData = []): LoginController
    {
        $login = LoginController::new(self::$container);
        $login->acceptType = $login::ACCEPT_TYPE_JSON;
        $login->requestMethod = 'POST';
        $login->parsedBody = $parsedData;
        $login->acceptType = $login::ACCEPT_TYPE_JSON;
        $login->gamepath = 'gamepath';
        $login->serverClosed = false;
        $login->gamedomain = 'gamedomain';
        $login->serve();

        return $login;
    }

    public function testLoginPage(): void
    {
        $login = LoginController::new(self::$container);
        $login->acceptType = $login::ACCEPT_TYPE_HTML;
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
        self::assertEquals('main', $login->location);
    }

    public function testLoginClosed(): void
    {
        $login = LoginController::new(self::$container);
        $login->serverClosed = true;
        $login->acceptType = $login::ACCEPT_TYPE_JSON;
        $login->requestMethod = 'POST';
        $login->serve();

        self::assertEquals(self::$l->login_closed_message, $login->exception?->getMessage());
        self::assertEmpty($login->responseCookies);
        self::assertEmpty($login->location);
    }

    public function testWithoutEmail(): void
    {
        $login = $this->prepareLoginPOST();

        self::assertEquals(self::$l->login_email . ' ' . self::$l->is_required, $login->exception?->getMessage());
        self::assertEmpty($login->responseCookies);
        self::assertEmpty($login->location);
    }

    public function testWithEmailAndWithoutPass(): void
    {
        $login = $this->prepareLoginPOST([
            'email' => 'email@admin.com',
        ]);

        self::assertEquals(self::$l->login_pw . ' ' . self::$l->is_required, $login->exception?->getMessage());
        self::assertEmpty($login->responseCookies);
        self::assertEmpty($login->location);
    }

    public function testWithInvalidEmail(): void
    {
        $login = $this->prepareLoginPOST([
            'email' => 'emailadmin.com',
        ]);

        self::assertEquals(self::$l->login_email . ' ' . self::$l->is_invalid, $login->exception?->getMessage());
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
