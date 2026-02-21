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
        $this->operation = (string) $this->fromQueryParams('operation');
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
        global $l;

        if ($this->operation == 'save') {
            $adminpass = $this->fromParsedBody('adminpass');
            $universeSize = $this->fromParsedBody('universe_size', 'universe_size ' . $l->is_required);

            $config = [
                'admin_mail' => $this->fromParsedBody('admin_mail', 'admin_mail ' . $l->is_required),
                'universe_size' => $universeSize,
            ];

            if (!empty($adminpass)) {
                $config['adminpass'] = $adminpass;
            }

            ConfigUpdateDAO::call($this->container, $config);

            db()->q('UPDATE universe SET distance = FLOOR(RAND() * :universe_size) WHERE 1 = 1', [
                'universe_size' => $universeSize + 1,
            ]);
            $this->redirectTo('admin.php');
            return;
        }
        
        parent::processPostAsJson();
    }
}
