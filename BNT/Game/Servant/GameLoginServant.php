<?php

declare(strict_types=1);

namespace BNT\Game\Servant;

use BNT\UUID;
use BNT\Log\DAO\LogPlayerDAO;
use BNT\Log\LogTypeConstants;
use BNT\User\DAO\UserUpdateDAO;
use BNT\User\DAO\UserByEmailDAO;
use Exception;

class GameLoginServant extends \UUA\Servant
{

    public string $email;
    public string $password;
    public ?array $user;

    #[\Override]
    public function serve(): void
    {
        global $l_login_noone;
        global $l_login_4gotpw1;
        global $ip;

        $user = UserByEmailDAO::call($this->container, $this->email)->user;

        if (empty($user)) {
            throw new Exception($l_login_noone);
        }

        if ($user['password'] !== md5($this->password)) {
            if (!empty($user['ship_id'])) {
                LogPlayerDAO::call($this->container, $user['ship_id'], LogTypeConstants::LOG_BADLOGIN, $ip);
            }

            throw new Exception($l_login_4gotpw1);
        }

        $user['token'] = UUID::v7();
        $user['last_login'] = gmdate('Y-m-d H:i:s');

        if (!empty($user['ship_id'])) {
            LogPlayerDAO::call($this->container, $user['ship_id'], LogTypeConstants::LOG_LOGIN, $ip);
        }

        UserUpdateDAO::call($this->container, $user, $user['id']);

        $this->user = $user;
    }
}
