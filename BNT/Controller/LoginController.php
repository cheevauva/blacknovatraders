<?php

declare(strict_types=1);

namespace BNT\Controller;

use BNT\Game\Servant\GameLoginServant;
use Exception;

class LoginController extends BaseController
{

    #[\Override]
    protected function processGet(): void
    {
        $this->render('tpls/login.tpl.php');
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
