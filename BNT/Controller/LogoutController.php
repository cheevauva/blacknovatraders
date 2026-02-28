<?php

declare(strict_types=1);

namespace BNT\Controller;

use BNT\Log\LogTypeConstants;
use BNT\UUID;

class LogoutController extends BaseController
{

    #[\Override]
    protected function init(): void
    {
        $this->enableCheckAuth = false;
    }
    
    #[\Override]
    protected function processGet(): void
    {
        global $gamepath;
        global $gamedomain;
        global $ip;

        $this->setCookie('token', UUID::v7(), 0, $gamepath, $gamedomain);

        if (!empty($this->playerinfo['ship_id'])) {
            gen_score($this->playerinfo['ship_id']);
            playerlog($this->playerinfo['ship_id'], LogTypeConstants::LOG_LOGOUT, $ip);
        }

        $this->redirectTo('index');
    }
}
