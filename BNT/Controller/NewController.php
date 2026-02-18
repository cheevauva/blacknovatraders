<?php

declare(strict_types=1);

namespace BNT\Controller;

use BNT\Game\Servant\GameNewServant;
use Exception;

class NewController extends BaseController
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
            $this->render('tpls/new.tpl.php');
        } else {
            $this->redirectTo('index.php');
        }
    }

    #[\Override]
    protected function processPost(): void
    {
        global $account_creation_closed;
        global $l_new_closed_message;
        global $gamepath;
        global $gamedomain;
        global $l_new_username;
        global $l_new_character;
        global $l_new_shipname;
        global $l_new_password;
        global $l_is_required;
        global $l_is_invalid;

        if (!empty($this->userinfo)) {
            $this->redirectTo('index.php');
            return;
        }

        try {
            if ($account_creation_closed) {
                throw new Exception($l_new_closed_message);
            }

            $username = strval($this->parsedBody['username'] ?? '') ?: throw new Exception($l_new_username . ' ' . $l_is_required);

            if (!filter_var($username, FILTER_VALIDATE_EMAIL)) {
                throw new Exception($l_new_username . ' ' . $l_is_invalid);
            }

            $gameNew = GameNewServant::new($this->container);
            $gameNew->email = $username;
            $gameNew->character = strval($this->parsedBody['character'] ?? '') ?: throw new Exception($l_new_character . ' ' . $l_is_required);
            $gameNew->shipname = strval($this->parsedBody['shipname'] ?? '') ?: throw new Exception($l_new_shipname . ' ' . $l_is_required);
            $gameNew->password = strval($this->parsedBody['password'] ?? '') ?: throw new Exception($l_new_password . ' ' . $l_is_required);
            $gameNew->serve();

            $this->setCookie('token', $gameNew->user['token'], time() + (3600 * 24) * 365, $gamepath, $gamedomain);
            $this->redirectTo('main.php');
        } catch (Exception $ex) {
            $this->responseJsonByException($ex);
        }
    }
}
