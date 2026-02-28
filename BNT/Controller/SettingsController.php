<?php

declare(strict_types=1);

namespace BNT\Controller;

class SettingsController extends BaseController
{
    #[\Override]
    protected function preProcess(): void
    {
        $this->title = $this->l->settings_game;
    }

    #[\Override]
    protected function processGetAsHtml(): void
    {
        $this->render('tpls/settings.tpl.php');
    }
}
