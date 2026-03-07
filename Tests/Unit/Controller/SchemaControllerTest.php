<?php

declare(strict_types=1);

namespace Tests\Unit\Controller;

use BNT\Controller\SchemaController;
use BNT\Game\Servant\GameMigrationsExecuteServant;

class SchemaControllerTest extends \Tests\UnitTestCase
{

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();

        global $adminpass;

        $adminpass = 'admin123';
    }

    protected function prepareSchemaPOST(array $parsedData = []): SchemaController
    {
        $schema = SchemaController::new(self::$container);
        $schema->l = self::$l;
        $schema->requestMethod = 'POST';
        $schema->acceptType = $schema::ACCEPT_TYPE_HTML;
        $schema->enableThrowExceptionOnProcess = true;
        $schema->parsedBody = $parsedData;
        $schema->serve();

        return $schema;
    }

    public function testSchemaLoginPage(): void
    {
        $schema = SchemaController::new(self::$container);
        $schema->acceptType = $schema::ACCEPT_TYPE_HTML;
        $schema->requestMethod = 'GET';
        $schema->serve();

        self::assertEquals('tpls/schema/schema_login.tpl.php', $schema->template);
    }

    public function testSchemaSuccess(): void
    {
        global $adminpass;

        $schema = $this->prepareSchemaPOST([
            'password' => $adminpass,
        ]);

        self::assertEquals(['Migration executed successfully'], $schema->messages);
        self::assertEquals('tpls/schema/schema_messages.tpl.php', $schema->template);
        self::assertNull($schema->exception);
    }

    public function testSchemaWithoutPassword(): void
    {
        $this->expectExceptionMessage(self::$l->l_schema_password . ' ' . self::$l->l_is_not_empty);
        $this->prepareSchemaPOST();
    }

    public function testSchemaWithWrongPassword(): void
    {
        $this->expectExceptionMessage(self::$l->l_schema_password . ' ' . self::$l->l_is_wrong);
        $this->prepareSchemaPOST([
            'password' => 'wrongpassword',
        ]);
    }

    #[\Override]
    protected function stubs(): array
    {
        return [
            GameMigrationsExecuteServant::class => fn($c) => new class($c) extends GameMigrationsExecuteServant {

                #[\Override]
                public function serve(): void
                {
                    $this->messages = ['Migration executed successfully'];
                }
            },
        ];
    }
}
