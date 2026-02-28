<?php

declare(strict_types=1);

namespace BNT\Controller;

use BNT\Log\DAO\LogPlayerDAO;
use BNT\Log\LogTypeConstants;
use BNT\Game\Servant\GameKillPlayerServant;
use BNT\Game\Servant\GameCancelBountyServant;

class SelfDestructController extends BaseController
{

    public int $sure = 0;

    #[\Override]
    protected function preProcess(): void
    {
        $this->title = $this->l->login_title;
        $this->sure = (int) $this->fromParsedBody('sure');
    }

    #[\Override]
    protected function processGetAsHtml(): void
    {
        $this->render('tpls/self_destruct.tpl.php');
    }

    #[\Override]
    protected function processPostAsJson(): void
    {
        global $ip;

        if ($this->sure !== 2) {
            $this->redirectTo('index');
            return;
        }

        GameKillPlayerServant::call($this->container, $this->playerinfo['ship_id'])->serve();
        GameCancelBountyServant::call($this->playerinfo['ship_id']);

        LogPlayerDAO(LogTypeConstants::LOG_ADMIN_HARAKIRI, [$this->playerinfo['ship_name'], $ip]);
        LogPlayerDAO::call($this->container, $this->playerinfo['ship_id'], LogTypeConstants::LOG_HARAKIRI, $ip);

        $this->redirectTo('index');
    }
}
