<?php

declare(strict_types=1);

namespace BNT\Controller;

use BNT\Game\Servant\GameNewServant;
use BNT\Exception\WarningException;

class NewController extends BaseController
{

    public bool $accountCreationClosed;
    public string $gamepath;
    public string $gamedomain;

    #[\Override]
    protected function init(): void
    {
        parent::init();

        $this->enableCheckAuth = false;
    }

    #[\Override]
    protected function preProcess(): void
    {
        $this->title = $this->t('l_new_title');

        if ($this->accountCreationClosed) {
            throw new WarningException('l_new_closed_message');
        }
    }

    #[\Override]
    protected function processGet(): void
    {
        if (empty($this->userinfo)) {
            $this->render('tpls/new.tpl.php');
        } else {
            $this->redirectTo('index');
        }
    }

    #[\Override]
    protected function processPostAsJson(): void
    {

        if (!empty($this->userinfo)) {
            $this->redirectTo('index');
            return;
        }

        $gameNew = GameNewServant::new($this->container);
        $gameNew->email = $this->fromParsedBody('username')->filter(FILTER_VALIDATE_EMAIL)->notEmpty()->label($this->l->new_username)->asString();
        $gameNew->character = $this->fromParsedBody('character')->trim()->notEmpty()->label($this->l->new_character)->asString();
        $gameNew->shipname = $this->fromParsedBody('shipname')->trim()->notEmpty()->label($this->l->new_shipname)->asString();
        $gameNew->password = $this->fromParsedBody('password')->trim()->notEmpty()->label($this->l->new_password)->asString();
        $gameNew->serve();

        $this->setCookie('token', $gameNew->user['token'], time() + (3600 * 24) * 365, $this->gamepath, $this->gamedomain);
        $this->redirectTo('main');
    }
}
