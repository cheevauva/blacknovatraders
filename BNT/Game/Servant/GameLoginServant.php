<?php

declare(strict_types=1);

namespace BNT\Game\Servant;

use BNT\UUID;
use BNT\Log\DAO\LogPlayerDAO;
use BNT\Log\LogTypeConstants;
use BNT\Ship\DAO\ShipByEmailDAO;
use BNT\Ship\DAO\ShipUpdateDAO;
use BNT\Ship\Servant\ShipCheckNewbieServant;
use BNT\Ship\Servant\ShipRestoreFromEscapePodServant;
use BNT\Ship\Servant\ShipRestoreAsNewbieServant;
use Exception;

class GameLoginServant extends \UUA\Servant
{

    public string $email;
    public string $password;
    public ?array $ship;

    #[\Override]
    public function serve(): void
    {
        global $l_login_noone;
        global $l_login_died;
        global $l_login_looser;
        global $l_login_newbie;
        global $l_login_newlife;
        global $l_login_4gotpw1;
        global $l_login_you_have_die;
        global $ip;
        global $newbie_nice;

        $ship = ShipByEmailDAO::call($this->container, $this->email)->ship;

        if (empty($ship)) {
            throw new Exception($l_login_noone);
        }

        if ($ship['password'] !== md5($this->password)) {
            LogPlayerDAO::call($this->container, $ship['ship_id'], LogTypeConstants::LOG_BADLOGIN, $ip);

            throw new Exception($l_login_4gotpw1);
        }

        if ($ship['ship_destroyed'] == 'N') {
            $this->tokenToShip($ship);
            return;
        }

        if ($ship['ship_destroyed'] == 'Y' && $ship['dev_escapepod'] == 'Y') {
            $escapepod = ShipRestoreFromEscapePodServant::new($this->container);
            $escapepod->ship = $ship;
            $escapepod->serve();

            $this->tokenToShip($escapepod->ship);

            throw new Exception($l_login_died);
        }

        if ($ship['ship_destroyed'] == 'Y' && $newbie_nice !== 'YES') {
            throw new Exception($l_login_you_have_die . ' ' . $l_login_looser);
        }

        if ($ship['ship_destroyed'] == 'Y' && $newbie_nice == 'YES') {
            $checkNewbie = ShipCheckNewbieServant::new($this->container);
            $checkNewbie->ship = $ship;
            $checkNewbie->serve();

            if (!$checkNewbie->isNewbie) {
                throw new Exception($l_login_you_have_die . ' ' . $l_login_looser);
            }

            $restore = ShipRestoreAsNewbieServant::new($this->container);
            $restore->ship = $ship;
            $restore->serve();

            $this->tokenToShip($escapepod->ship);

            throw new Exception($l_login_you_have_die . ' ' . $l_login_newbie . ' ' . $l_login_newlife);
        }
    }

    protected function tokenToShip(array $ship): void
    {
        global $ip;

        $ship['token'] = UUID::v7();
        $ship['last_login'] = gmdate('Y-m-d H:i:s');

        LogPlayerDAO::call($this->container, $ship['ship_id'], LogTypeConstants::LOG_LOGIN, $ip);
        ShipUpdateDAO::call($this->container, $ship, $ship['ship_id']);

        $this->ship = $ship;
    }
}
