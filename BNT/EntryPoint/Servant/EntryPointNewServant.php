<?php

declare(strict_types=1);

namespace BNT\EntryPoint\Servant;

use BNT\Ship\DAO\ShipByEmailDAO;
use BNT\Log\LogTypeConstants;
use BNT\Log\DAO\LogPlayerDAO;
use BNT\Ship\DAO\ShipCreateDAO;
use BNT\Email\DAO\EmailSendDAO;
use BNT\Zone\DAO\ZoneCreateDAO;
use BNT\IBankAccount\DAO\IBankAccountCreateDAO;

class EntryPointNewServant extends \UUA\Servant
{
    public $username;
    public $password;
    public $character;
    public $shipname;
    public $ship;

    public function serve(): void
    {
        global $l_new_invalid;
        global $l_new_username;
        global $l_new_inuse;
        global $l_new_message;
        global $l_new_topic;
        global $ip;
        global $admin_mail;

        if (!filter_var($this->username, FILTER_VALIDATE_EMAIL)) {
            throw new \Exception($l_new_username . ' ' . $l_new_invalid);
        }

        if (ShipByEmailDAO::call($this->container, $this->username)->ship) {
            throw new \Exception($l_new_inuse);
        }

        $ship = $this->newShip();
        $ship['ship_id'] = ShipCreateDAO::call($this->container, $ship)->id;

        $sendEmail = EmailSendDAO::new($this->container);
        $sendEmail->from = $admin_mail;
        $sendEmail->replyTo = $admin_mail;
        $sendEmail->subject = $l_new_topic;
        $sendEmail->message = str_replace("[pass]", $this->password, $l_new_message);
        $sendEmail->to = $this->username;

        $zoneCreate = ZoneCreateDAO::new($this->container);
        $zoneCreate->zone = [
            'owner' => $ship['ship_id'],
            'zone_name' => $this->character . "'s Territory",
        ];
        $zoneCreate->serve();

        $ibankAccountCreate = IBankAccountCreateDAO::new($this->container);
        $ibankAccountCreate->ibackAccount = [
            'ship_id' => $ship['ship_id'],
        ];
        $ibankAccountCreate->serve();

        $log = LogPlayerDAO::new($this->container);
        $log->type = LogTypeConstants::LOG_LOGIN;
        $log->data = $ip;
        $log->ship = $ship['ship_id'];
        $log->serve();

        $this->ship = $ship;
    }

    protected function newShip()
    {
        global $language;
        global $start_armor;
        global $start_credits;
        global $start_energy;
        global $start_fighters;

        return [
            'ship_name' => $this->shipname,
            'ship_destroyed' => 'N',
            'character_name' => $this->character,
            'password' => md5($this->password),
            'email' => $this->username,
            'armor_pts' => (int) $start_armor,
            'credits' => (int) $start_credits,
            'ship_energy' => (int) $start_energy,
            'ship_fighters' => (int) $start_fighters,
            'turns' => 1200,//(int) $this->mturnsMax(),
            'on_planet' => 'N',
            'dev_warpedit' => 0,
            'dev_genesis' => 0,
            'dev_beacon' => 0,
            'dev_emerwarp' => 0,
            'dev_escapepod' => 'N',
            'dev_fuelscoop' => 'N',
            'dev_minedeflector' => 0,
            'last_login' => date('Y-m-d H:i:s'),
            'interface' => 'N',
            'token' => uuidv7(),
            'trade_colonists' => 'Y',
            'trade_fighters' => 'N',
            'trade_torps' => 'N',
            'trade_energy' => 'Y',
            'cleared_defences' => null,
            'lang' => $language,
            'dev_lssd' => (int) 'N',
        ];
    }

    protected function mturnsMax()
    {
        global $max_turns;

        $mturns = db()->column("SELECT MAX(turns_used + turns) AS mturns FROM ships");

        if ($mturns > $max_turns) {
            $mturns = $max_turns;
        }

        return $mturns;
    }
}
