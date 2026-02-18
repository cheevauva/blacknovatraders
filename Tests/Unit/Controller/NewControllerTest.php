<?php

declare(strict_types=1);

namespace Tests\Unit\Controller;

use BNT\Controller\NewController;
use BNT\Game\Servant\GameNewServant;
use Exception;

class NewControllerTest extends \Tests\UnitTestCase
{

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();

        global $account_creation_closed;
        global $gamepath;
        global $gamedomain;

        $gamepath = 'gamepath';
        $account_creation_closed = false;
        $gamedomain = 'gamedomain';
    }

    protected function prepareNewPOST(array $parsedData = []): NewController
    {
        $new = NewController::new(self::$container);
        $new->disablePrepareResponse = true;
        $new->requestMethod = 'POST';
        $new->parsedBody = $parsedData;
        $new->serve();

        return $new;
    }

    public function testNewPage(): void
    {
        $new = NewController::new(self::$container);
        $new->disablePrepareResponse = true;
        $new->requestMethod = 'GET';
        $new->serve();

        self::assertEquals('tpls/new.tpl.php', $new->template);
    }

    public function testNewSuccess(): void
    {
        $new = $this->prepareNewPOST([
            'username' => 'user@example.com',
            'character' => 'Test Character',
            'shipname' => 'Test Ship',
            'password' => 'password123',
        ]);

        self::assertNotEmpty($new->responseCookies['token'] ?? null);
        self::assertEquals('main.php', $new->location);
    }

    public function testNewAccountCreationClosed(): void
    {
        global $account_creation_closed;
        global $l_new_closed_message;

        $account_creation_closed = true;

        $new = $this->prepareNewPOST();

        self::assertEquals($l_new_closed_message, $new->exception?->getMessage());
        self::assertEmpty($new->responseCookies);
        self::assertEmpty($new->location);
    }

    public function testWithoutUsername(): void
    {
        global $l_new_username;
        global $l_is_required;

        $new = $this->prepareNewPOST();

        self::assertEquals($l_new_username . ' ' . $l_is_required, $new->exception?->getMessage());
        self::assertEmpty($new->responseCookies);
        self::assertEmpty($new->location);
    }

    public function testWithInvalidUsername(): void
    {
        global $l_new_username;
        global $l_is_invalid;

        $new = $this->prepareNewPOST([
            'username' => 'invalid-email',
        ]);

        self::assertEquals($l_new_username . ' ' . $l_is_invalid, $new->exception?->getMessage());
        self::assertEmpty($new->responseCookies);
        self::assertEmpty($new->location);
    }

    public function testWithoutCharacter(): void
    {
        global $l_new_character;
        global $l_is_required;

        $new = $this->prepareNewPOST([
            'username' => 'user@example.com',
        ]);

        self::assertEquals($l_new_character . ' ' . $l_is_required, $new->exception?->getMessage());
        self::assertEmpty($new->responseCookies);
        self::assertEmpty($new->location);
    }

    public function testWithoutShipname(): void
    {
        global $l_new_shipname;
        global $l_is_required;

        $new = $this->prepareNewPOST([
            'username' => 'user@example.com',
            'character' => 'Test Character',
        ]);

        self::assertEquals($l_new_shipname . ' ' . $l_is_required, $new->exception?->getMessage());
        self::assertEmpty($new->responseCookies);
        self::assertEmpty($new->location);
    }

    public function testWithoutPassword(): void
    {
        global $l_new_password;
        global $l_is_required;

        $new = $this->prepareNewPOST([
            'username' => 'user@example.com',
            'character' => 'Test Character',
            'shipname' => 'Test Ship',
        ]);

        self::assertEquals($l_new_password . ' ' . $l_is_required, $new->exception?->getMessage());
        self::assertEmpty($new->responseCookies);
        self::assertEmpty($new->location);
    }

    #[\Override]
    protected function stubs(): array
    {
        return [
            GameNewServant::class => fn($c) => new class($c) extends GameNewServant {

                #[\Override]
                public function serve(): void
                {
                    $this->user = [
                        'token' => 'new_user_token',
                        'ship_id' => 456,
                    ];
                }
            },
        ];
    }
}
