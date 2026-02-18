<?php

declare(strict_types=1);

namespace BNT\Controller;

use BNT\Game\Servant\GameLoginServant;
use Exception;

class LoginController extends BaseController
{

    #[\Override]
    protected function init(): void
    {
        $this->enableCheckAuth = false;
    }

    #[\Override]
    protected function processGet(): void
    {
        if (!empty($this->userinfo)) {
            $this->redirectTo('index.php');
        } else {
            $this->render('tpls/login.tpl.php');
        }
    }

    #[\Override]
    protected function processPost(): void
    {
        global $gamepath;
        global $gamedomain;
        global $server_closed;
        global $l_login_closed_message;
        global $l_login_email;
        global $l_login_pw;
        global $l_is_required;
        global $l_is_invalid;

        if (!empty($this->playerinfo)) {
            $this->redirectTo('index.php');
            return;
        }

        try {
            if ($server_closed) {
                throw new Exception($l_login_closed_message);
            }

            $email = strval($this->parsedBody['email'] ?? '') ?: throw new Exception($l_login_email . ' ' . $l_is_required);

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception($l_login_email . ' ' . $l_is_invalid);
            }

            $password = strval($this->parsedBody['pass'] ?? '') ?: throw new Exception($l_login_pw . ' ' . $l_is_required);

            $login = GameLoginServant::new($this->container);
            $login->email = $email;
            $login->password = $password;
            $login->serve();

            $this->setCookie('token', $login->user['token'], time() + (3600 * 24) * 365, $gamepath, $gamedomain);
            $this->redirectTo('main.php');
        } catch (\Exception $ex) {
            $this->responseJsonByException($ex);
        }
    }
}
