<?php

//declare(strict_types=1);

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

    public function serve()
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

        $shipByEmail = ShipByEmailDAO::_new($this->container);
        $shipByEmail->email = $this->email;
        $shipByEmail->serve();

        $ship = $shipByEmail->ship;

        if (empty($ship)) {
            throw new \Exception($l_login_noone);
        }

        if ($ship['password'] !== md5($this->password)) {
            $logBadLogin = LogPlayerDAO::_new($this->container);
            $logBadLogin->ship = $ship['ship_id'];
            $logBadLogin->type = LogTypeConstants::LOG_BADLOGIN;
            $logBadLogin->data = $ip;
            $logBadLogin->serve();

            throw new \Exception($l_login_4gotpw1);
        }

        if ($ship['ship_destroyed'] == 'N') {
            $token = uuidv7();
            
            $ship['token'] = $token;
            $ship['last_login'] = gmdate('Y-m-d H:i:s');

            $logLogin = LogPlayerDAO::_new($this->container);
            $logLogin->ship = $ship['ship_id'];
            $logLogin->type = LogTypeConstants::LOG_LOGIN;
            $logLogin->data = $ip;
            $logLogin->serve();

            $shipUpdate = ShipUpdateDAO::_new($this->container);
            $shipUpdate->ship = $ship;
            $shipUpdate->serve();

            $this->ship = $ship;
            return;
        }

        if ($ship['ship_destroyed'] == 'Y' && $ship['dev_escapepod'] == 'Y') {
            $escapepod = ShipEscapepodServant::_new($this->container);
            $escapepod->ship = $ship;
            $escapepod->serve();

            throw new \Exception($l_login_died);
        }

        $youHaveDied = "You have died in a horrible incident, <a href=log.php>here</a> is the blackbox information that was retrieved from your ships wreckage.";

        if ($ship['ship_destroyed'] == 'Y' && $newbie_nice !== 'YES') {
            throw new \Exception($youHaveDied . ' ' . $l_login_looser);
        }

        if ($ship['ship_destroyed'] == 'Y' && $newbie_nice == 'YES') {
            $checkNewbie = ShipCheckNewbieServant::_new($this->container);
            $checkNewbie->ship = $ship;
            $checkNewbie->serve();

            if (!$checkNewbie->isNewbie) {
                throw new \Exception($youHaveDied . ' ' . $l_login_looser);
            }

            $restore = ShipRestoreAsNewbieServant::_new($this->container);
            $restore->ship = $ship;
            $restore->serve();

            throw new \Exception($youHaveDied . ' ' . $l_login_newbie . ' ' . $l_login_newlife);
        }
    }
}
