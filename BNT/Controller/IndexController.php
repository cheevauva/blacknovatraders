<?php

declare(strict_types=1);

namespace BNT\Controller;

class IndexController extends BaseController
{

    #[\Override]
    protected function init(): void
    {
        $this->enableCheckAuth = false;
    }

    #[\Override]
    protected function processGet(): void
    {
        if (empty($this->userinfo)) {
            $this->redirectTo('login');
            return;
        }

        if (empty($this->playerinfo)) {
            $this->redirectTo('ships');
            return;
        }

        if ($this->playerinfo['ship_destroyed'] === 'Y') {
            $this->redirectTo('ships');
            return;
        }

        $this->redirectTo('main');
    }
}
