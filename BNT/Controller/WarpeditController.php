<?php

declare(strict_types=1);

namespace BNT\Controller;

use BNT\Link\Servant\LinkCheckAccessByShipServant;
use BNT\Link\Servant\LinkCreateServant;
use BNT\Link\Servant\LinkRemoveServant;
use BNT\Link\DAO\LinksByStartDAO;

class WarpeditController extends BaseController
{

    public array $links = [];

    #[\Override]
    protected function preProcess(): void
    {
        $this->checkTurns();

        $checkAccess = LinkCheckAccessByShipServant::new($this->container);
        $checkAccess->ship = $this->playerinfo;
        $checkAccess->serve();

        $this->links = LinksByStartDAO::call($this->container, $this->playerinfo['sector'])->links;
    }

    #[\Override]
    protected function processGetAsHtml(): void
    {
        $this->render('tpls/warpedit.tpl.php');
    }

    #[\Override]
    protected function processPostAsJson(): void
    {
        global $l;

        $action = $this->fromParsedBody('action', 'action ' . $l->is_required);

        switch ($action) {
            case 'link':
                $create = LinkCreateServant::new($this->container);
                $create->oneway = $this->fromParsedBody('oneway') == 'oneway';
                $create->sector = $this->playerinfo['sector'];
                $create->targetSector = (int) $this->fromParsedBody('target_sector', 'target_sector ' . $l->is_required);
                $create->serve();

                $this->playerinfo['dev_warpedit'] -= 1;
                $this->playerinfo['turns'] -= 1;
                $this->playerinfoUpdate();
                $this->redirectTo('index');
                break;
            case 'unlink':
                $remove = LinkRemoveServant::new($this->container);
                $remove->bothway = $this->fromParsedBody('bothway') == 'bothway';
                $remove->sector = $this->playerinfo['sector'];
                $remove->targetSector = (int) $this->fromParsedBody('target_sector', 'target_sector ' . $l->is_required);
                $remove->serve();

                $this->playerinfo['dev_warpedit'] -= 1;
                $this->playerinfo['turns'] -= 1;
                $this->playerinfoUpdate();
                $this->redirectTo('index');
                break;
        }
    }
}
