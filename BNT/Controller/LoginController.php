<?php

declare(strict_types=1);

namespace BNT\Controller;

use BNT\Game\Servant\GameLoginServant;
use BNT\Exception\WarningException;

class LoginController extends BaseController
{

    public bool $serverClosed;
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
        $this->title = $this->l->login_title;

        if ($this->serverClosed) {
            throw new WarningException($this->l->login_closed_message);
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


        $email = strval($this->parsedBody['email'] ?? '') ?: throw new WarningException($this->l->login_email . ' ' . $this->l->is_required);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new WarningException($this->l->login_email . ' ' . $this->l->is_invalid);
        }

        $password = strval($this->parsedBody['pass'] ?? '') ?: throw new WarningException($this->l->login_pw . ' ' . $this->l->is_required);

        $login = GameLoginServant::new($this->container);
        $login->email = $email;
        $login->password = $password;
        $login->serve();

        $this->setCookie('token', $login->user['token'], time() + (3600 * 24) * 365, $this->gamepath, $this->gamedomain);
        $this->redirectTo('main');
    }
}
