<?php

declare(strict_types=1);

namespace BNT\Controller;

use BNT\Game\Servant\GameGenesisServant;
use BNT\Exception\SuccessException;

class GenesisController extends BaseController
{

    #[\Override]
    protected function preProcess(): void
    {
        $this->title = $this->t('l_gns_title');
    }

    #[\Override]
    protected function processGetAsHtml(): void
    {
        $this->checkTurns();

        $genesis = GameGenesisServant::new($this->container);
        $genesis->ship = $this->playerinfo;
        $genesis->serve();

        $this->playerinfo['dev_genesis'] -= 1;
        $this->playerinfoTurn();
        $this->playerinfoUpdate();

        throw new SuccessException('l_gns_pcreate');
    }
}
