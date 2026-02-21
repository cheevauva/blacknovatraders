<?php

declare(strict_types=1);

namespace BNT\Controller;

use Exception;
use BNT\Game\Servant\GameMigrationsExecuteServant;

class SchemaController extends BaseController
{

    public array $messages = [];

    #[\Override]
    protected function init(): void
    {
        $this->enableCheckAuth = false;
    }

    #[\Override]
    protected function processGetAsHtml(): void
    {
        $this->render('tpls/schema/schema_login.tpl.php');
    }

    #[\Override]
    protected function processPostAsHtml(): void
    {
        global $adminpass;
        global $l_schema_password;
        global $l_is_required;
        global $l_is_wrong;

        $password = strval($this->parsedBody['password'] ?? '') ?: throw new Exception($l_schema_password . ' ' . $l_is_required);

        if ($password !== $adminpass) {
            throw new \Exception($l_schema_password . ' ' . $l_is_wrong);
        }

        $this->messages = GameMigrationsExecuteServant::call($this->container)->messages;

        $this->render('tpls/schema/schema_messages.tpl.php');
    }
}
