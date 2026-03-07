<?php

declare(strict_types=1);

namespace BNT\Controller;

class SettingsController extends BaseController
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
        $this->title = $this->t('l_settings_game');
    }

    #[\Override]
    protected function processGetAsHtml(): void
    {
        $this->render('tpls/settings.tpl.php');
    }
}
