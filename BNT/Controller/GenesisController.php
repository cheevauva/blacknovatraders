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
        $this->title = $this->l->gns_title;
    }

    #[\Override]
    protected function processGetAsHtml(): void
    {
        $this->checkTurns();

        try {
            $genesis = GameGenesisServant::new($this->container);
            $genesis->ship = $this->playerinfo;
            $genesis->serve();
        } catch (SuccessException $ex) {
            $this->playerinfo['turns_used'] += 1;
            $this->playerinfo['turns'] += 1;
            $this->playerinfo['dev_genesis'] -= 1;
            $this->playerinfoUpdate();
            
            throw $ex;
        }
    }
}
