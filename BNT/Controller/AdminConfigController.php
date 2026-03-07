<?php

declare(strict_types=1);

namespace BNT\Controller;

use BNT\Config\DAO\ConfigUpdateDAO;
use BNT\Config\DAO\ConfigReadDAO;

class AdminConfigController extends BaseController
{

    public string $operation;
    public array $config;

    #[\Override]
    protected function preProcess(): void
    {
        $this->isAdmin() ?: throw new ErrorException('You not admin');
        $this->operation = $this->fromQueryParams('operation')->enum(['edit', 'save'])->asString();
    }

    #[\Override]
    protected function processGetAsHtml(): void
    {
        if ($this->operation === 'edit') {
            $this->config = ConfigReadDAO::call($this->container)->config;
            $this->render('tpls/admin/configedit.tpl.php');
            return;
        }

        parent::processGetAsHtml();
    }

    #[\Override]
    protected function processPostAsJson(): void
    {
        if ($this->operation == 'save') {
            $adminpass = $this->fromParsedBody('adminpass')->label('l_admin_adminpass')->trim()->asString();
            $universeSize = $this->fromParsedBody('universe_size')->label('l_admin_universe_size')->notEmpty()->asInt();
            $adminMail = $this->fromParsedBody('admin_mail')->label('l_admin_admin_mail')->notEmpty()->filter(FILTER_VALIDATE_EMAIL)->asString();

            $config = [
                'admin_mail' => $adminMail,
                'universe_size' => $universeSize,
            ];

            if (!empty($adminpass)) {
                $config['adminpass'] = $adminpass;
            }

            ConfigUpdateDAO::call($this->container, $config);

            db()->q('UPDATE universe SET distance = FLOOR(RAND() * :universe_size) WHERE 1 = 1', [
                'universe_size' => $universeSize + 1,
            ]);
            $this->redirectTo('admin');
            return;
        }

        parent::processPostAsJson();
    }
}
