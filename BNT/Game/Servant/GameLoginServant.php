<?php

declare(strict_types=1);

namespace BNT\Game\Servant;

use BNT\UUID;
use BNT\Log\DAO\LogPlayerDAO;
use BNT\Log\LogTypeConstants;
use BNT\User\DAO\UserUpdateDAO;
use BNT\User\DAO\UserByEmailDAO;
use BNT\Ship\DAO\ShipByIdDAO;
use BNT\Exception\WarningException;

class GameLoginServant extends \UUA\Servant
{

    public string $email;
    public string $password;
    public ?array $user;

    #[\Override]
    public function serve(): void
    {
        global $l;
        global $ip;

        $user = UserByEmailDAO::call($this->container, $this->email)->user;

        if (empty($user)) {
            throw new WarningException($l->login_noone);
        }

        if ($user['password'] !== md5($this->password)) {
            throw new WarningException($l->login_4gotpw1);
        }

        $user['token'] = UUID::v7();
        $user['last_login'] = gmdate('Y-m-d H:i:s');

        if (!empty($user['ship_id'])) {
            $ship = ShipByIdDAO::call($this->container, $user['ship_id'])->ship;

            if ($user['id'] !== $ship['user_id']) {
                $user['ship_id'] = null;
            }

            if ($ship['ship_destroyed'] == 'Y') {
                $user['ship_id'] = null;
            }

            if (!empty($user['ship_id'])) {
                LogPlayerDAO::call($this->container, $user['ship_id'], LogTypeConstants::LOG_LOGIN, $ip);
            }
        }

        UserUpdateDAO::call($this->container, $user, $user['id']);

        $this->user = $user;
    }
}
