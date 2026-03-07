<?php

declare(strict_types=1);

namespace BNT\Controller;

use BNT\Game\Servant\GameLoginServant;
use BNT\Exception\WarningException;

class LoginController extends BaseController
{

    public bool $serverClosed = false;
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
        $this->title = $this->t('l_login_title');

        if ($this->serverClosed) {
            throw new WarningException('l_login_closed_message');
        }
    }

    #[\Override]
    protected function processGetAsHtml(): void
    {
        if (!empty($this->userinfo)) {
            $this->redirectTo('index');
        } else {
            $this->render('tpls/login.tpl.php');
        }
    }

    #[\Override]
    protected function processPostAsJson(): void
    {
        if (!empty($this->playerinfo)) {
            $this->redirectTo('index');
            return;
        }

        $email = $this->fromParsedBody('email')->filter(FILTER_VALIDATE_EMAIL)->notEmpty()->trim()->label('l_login_email')->asString();
        $password = $this->fromParsedBody('pass')->trim()->notEmpty()->label('l_login_pw')->asString();
    
        $login = GameLoginServant::new($this->container);
        $login->email = $email;
        $login->password = $password;
        $login->serve();

        $this->setCookie('token', $login->user['token'], time() + (3600 * 24) * 365, $this->gamepath, $this->gamedomain);
        $this->redirectTo('main');
    }
}
