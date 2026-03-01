<?php

declare(strict_types=1);

namespace BNT\Controller;

class HelpController extends BaseController
{

    #[\Override]
    protected function init(): void
    {
        parent::init();

        $this->enableCheckAuth = false;
    }

    #[\Override]
    protected function preProcess(): void
    {
        $this->title = $this->l->help_title;
    }

    #[\Override]
    protected function processGetAsHtml(): void
    {
        $this->render(sprintf('tpls/help/help_%s.tpl.php', $this->userinfo['lang']));
    }
}
