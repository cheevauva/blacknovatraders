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
    protected function processGet(): void
    {
        if (empty($this->userinfo)) {
            $this->render('tpls/new.tpl.php');
        } else {
            $this->redirectTo('index.php');
        }
    }

    #[\Override]
    protected function processPostAsJson(): void
    {

        if (!empty($this->userinfo)) {
            $this->redirectTo('index.php');
            return;
        }

        if ($this->accountCreationClosed) {
            throw new WarningException($this->l->new_closed_message);
        }

        $username = (string) $this->fromParsedBody('username', $this->l->new_username . ' ' . $this->l->is_required);

        if (!filter_var($username, FILTER_VALIDATE_EMAIL)) {
            throw new WarningException($this->l->new_username . ' ' . $this->l->is_invalid);
        }

        $gameNew = GameNewServant::new($this->container);
        $gameNew->email = $username;
        $gameNew->character = (string) $this->fromParsedBody('character', $this->l->new_character . ' ' . $this->l->is_required);
        $gameNew->shipname = (string) $this->fromParsedBody('shipname', $this->l->new_shipname . ' ' . $this->l->is_required);
        $gameNew->password = (string) $this->fromParsedBody('password', $this->l->new_password . ' ' . $this->l->is_required);
        $gameNew->serve();

        $this->setCookie('token', $gameNew->user['token'], time() + (3600 * 24) * 365, $this->gamepath, $this->gamedomain);
        $this->redirectTo('main.php');
    }
}
