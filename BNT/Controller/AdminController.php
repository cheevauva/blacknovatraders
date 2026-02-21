<?php

declare(strict_types=1);

namespace BNT\Controller;

use BNT\Exception\ErrorException;

class AdminController extends BaseController
{

    public ?string $module = null;
    public ?string $operation = null;

    #[\Override]
    protected function preProcess(): void
    {
        $this->title = 'Administration';
        $this->module = $this->fromQueryParams('module');
        $this->operation = $this->fromQueryParams('operation');

        $this->isAdmin() ?: throw new ErrorException('You not admin');
    }

    #[\Override]
    protected function processGetAsHtml(): void
    {
        $this->render('tpls/admin/welcome.tpl.php');
    }
}
