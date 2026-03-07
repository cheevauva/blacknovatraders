<?php

declare(strict_types=1);

namespace BNT\Controller;

use BNT\Game\DAO\GameInfoPubliclistDAO;
use BNT\Exception\ErrorException;
class InfoPubliclistController extends BaseController
{

    public array $info;

    #[\Override]
    protected function preProcess(): void
    {
        $this->isAdmin() ?: throw new ErrorException('You not admin');
    }

    #[\Override]
    protected function processGetAsHtml(): void
    {
        $this->info = GameInfoPubliclistDAO::call($this->container)->info;
        $this->render('tpls/info_publiclist.tpl.php');
    }
}
