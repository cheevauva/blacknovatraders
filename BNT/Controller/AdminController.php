<?php

declare(strict_types=1);

namespace BNT\Controller;

use BNT\Exception\ErrorException;

class AdminController extends BaseController
{

    #[\Override]
    protected function preProcess(): void
    {
        $this->title = 'Administration';
        $this->isAdmin() ?: throw new ErrorException('You not admin');
    }

    #[\Override]
    protected function processGetAsHtml(): void
    {
        $this->render('tpls/admin/welcome.tpl.php');
    }
}
