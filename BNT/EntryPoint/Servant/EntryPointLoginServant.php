<?php

declare(strict_types=1);

namespace BNT\EntryPoint\Servant;

use BNT\Ship\Servant\ShipEscapepodServant;
use BNT\Ship\Servant\ShipCheckNewbieServant;
use BNT\Ship\Servant\ShipRestoreAsNewbieServant;
use BNT\Ship\DAO\ShipByEmailDAO;
use BNT\Log\LogTypeConstants;
use BNT\Ship\DAO\ShipUpdateDAO;
use BNT\Log\DAO\LogPlayerDAO;

class EntryPointLoginServant extends \UUA\Servant
{

    public $email;
    public $password;
    public $ship;

    public function serve(): void
    {
        global $l_login_email;
        global $l_login_pw;
        global $l_is_required;
        global $l_login_closed_message;
        global $l_login_noone;
        global $l_login_died;
        global $l_login_looser;
        global $l_login_newbie;
        global $l_login_newlife;
        global $l_login_4gotpw1;
        global $ip;
        global $newbie_nice;
        global $server_closed;

        if ($server_closed) {
            throw new \Exception($l_login_closed_message);
        }

        if (empty($this->email)) {
            throw new \Exception($l_login_email . ' ' . $l_is_required);
        }

        if (empty($this->password)) {
            throw new \Exception($l_login_pw . ' ' . $l_is_required);
        }

        $ship = ShipByEmailDAO::call($this->container, $this->email)->ship;

        if (empty($ship)) {
            throw new \Exception($l_login_noone);
        }

        if ($ship['password'] !== md5($this->password)) {
            LogPlayerDAO::call($this->container, $ship['ship_id'], LogTypeConstants::LOG_BADLOGIN, $ip);

            throw new \Exception($l_login_4gotpw1);
        }

        if ($ship['ship_destroyed'] == 'N') {
            $token = uuidv7();
            
            $ship['token'] = $token;
            $ship['last_login'] = gmdate('Y-m-d H:i:s');

            LogPlayerDAO::call($this->container, $ship['ship_id'], LogTypeConstants::LOG_LOGIN, $ip);
            ShipUpdateDAO::call($this->container, $ship);
            
            $this->ship = $ship;
            return;
        }

        if ($ship['ship_destroyed'] == 'Y' && $ship['dev_escapepod'] == 'Y') {
            $escapepod = ShipEscapepodServant::new($this->container);
            $escapepod->ship = $ship;
            $escapepod->serve();

            throw new \Exception($l_login_died);
        }

        $youHaveDied = "You have died in a horrible incident, <a href=log.php>here</a> is the blackbox information that was retrieved from your ships wreckage.";

        if ($ship['ship_destroyed'] == 'Y' && $newbie_nice !== 'YES') {
            throw new \Exception($youHaveDied . ' ' . $l_login_looser);
        }

        if ($ship['ship_destroyed'] == 'Y' && $newbie_nice == 'YES') {
            $checkNewbie = ShipCheckNewbieServant::new($this->container);
            $checkNewbie->ship = $ship;
            $checkNewbie->serve();

            if (!$checkNewbie->isNewbie) {
                throw new \Exception($youHaveDied . ' ' . $l_login_looser);
            }

            $restore = ShipRestoreAsNewbieServant::new($this->container);
            $restore->ship = $ship;
            $restore->serve();

            throw new \Exception($youHaveDied . ' ' . $l_login_newbie . ' ' . $l_login_newlife);
        }
    }
}
