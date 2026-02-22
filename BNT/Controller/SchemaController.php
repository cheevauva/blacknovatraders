<?php

declare(strict_types=1);

namespace BNT\Controller;

use BNT\Exception\WarningException;
use BNT\Game\Servant\GameMigrationsExecuteServant;

class SchemaController extends BaseController
{

    public array $messages = [];

    #[\Override]
    protected function init(): void
    {
        parent::init();
        
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

        $password = $this->fromParsedBody('password' ,$this->l->schema_password . ' ' . $this->l->is_required);

        if ($password !== $adminpass) {
            throw new WarningException($this->l->schema_password . ' ' . $this->l->is_wrong);
        }

        $this->messages = GameMigrationsExecuteServant::call($this->container)->messages;

        $this->render('tpls/schema/schema_messages.tpl.php');
    }
}
