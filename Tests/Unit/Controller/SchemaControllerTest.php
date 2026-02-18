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
        $schema->disablePrepareResponse = true;
        $schema->requestMethod = 'POST';
        $schema->parsedBody = $parsedData;
        $schema->serve();

        return $schema;
    }

    public function testSchemaLoginPage(): void
    {
        $schema = SchemaController::new(self::$container);
        $schema->disablePrepareResponse = true;
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
        global $l_schema_password;
        global $l_is_required;

        $schema = $this->prepareSchemaPOST();

        self::assertEquals($l_schema_password . ' ' . $l_is_required, $schema->exception?->getMessage());
        self::assertEquals('tpls/error.tpl.php', $schema->template);
    }

    public function testSchemaWithWrongPassword(): void
    {
        global $l_schema_password;
        global $l_is_wrong;

        $schema = $this->prepareSchemaPOST([
            'password' => 'wrongpassword',
        ]);

        self::assertEquals($l_schema_password . ' ' . $l_is_wrong, $schema->exception?->getMessage());
        self::assertEquals('tpls/error.tpl.php', $schema->template);
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
