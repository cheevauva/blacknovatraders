<?php

declare(strict_types=1);

namespace Tests\Unit\Controller;

use BNT\Controller\NewController;
use BNT\Game\Servant\GameNewServant;
use PHPUnit\Framework\Attributes\DataProvider;

class NewControllerTest extends \Tests\UnitTestCase
{

    protected function prepareNewPOST(array $parsedData = []): NewController
    {
        $new = NewController::new(self::$container);
        $new->requestMethod = 'POST';
        $new->parsedBody = $parsedData;
        $new->acceptType = $new::ACCEPT_TYPE_JSON;
        $new->enableThrowExceptionOnProcess = true;
        $new->gamedomain = 'gamedomain';
        $new->gamepath = 'gamepath';
        $new->accountCreationClosed = false;

        return $new;
    }

    public function testNewPage(): void
    {
        $new = NewController::new(self::$container);
        $new->acceptType = $new::ACCEPT_TYPE_HTML;
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
        $new->serve();

        self::assertNotEmpty($new->responseCookies['token'] ?? null);
        self::assertEquals('main', $new->location);
    }

    #[DataProvider('parsedBodyProvider')]
    public function testParsedBody(array $parsedBody, string $exceptionMessage, ?\Closure $closure = null): void
    {
        $this->expectExceptionMessage($exceptionMessage);

        $new = $this->prepareNewPOST($parsedBody);

        if ($closure) {
            $closure($new);
        }
        
        $new->serve();
    }

    public static function parsedBodyProvider(): array
    {
        global $l;

        return [
            'testNewAccountCreationClosed' => [
                [],
                $l->new_closed_message,
                fn(NewController $c) => $c->accountCreationClosed = true,
            ],
            'testWithoutUsername' => [
                [],
                $l->new_username . ' ' . $l->is_required
            ],
            'testWithInvalidUsername' => [
                [
                    'username' => 'invalid-email',
                ],
                $l->new_username . ' ' . $l->is_invalid
            ],
            'testWithoutCharacter' => [
                [
                    'username' => 'user@example.com',
                ],
                $l->new_character . ' ' . $l->is_required
            ],
            'testWithoutShipname' => [
                [
                    'username' => 'user@example.com',
                    'character' => 'Test Character',
                ],
                $l->new_shipname . ' ' . $l->is_required
            ],
            'testWithoutPassword' => [
                [
                    'username' => 'user@example.com',
                    'character' => 'Test Character',
                    'shipname' => 'Test Ship',
                ],
                $l->new_password . ' ' . $l->is_required,
            ]
        ];
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
