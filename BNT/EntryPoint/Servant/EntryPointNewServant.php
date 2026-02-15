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
use BNT\Ship\Servant\ShipNewServant;

class EntryPointNewServant extends \UUA\Servant
{

    public $username;
    public $password;
    public $character;
    public $shipname;
    public $ship;

    #[\Override]
    public function serve(): void
    {
        global $l_new_invalid;
        global $l_new_username;
        global $l_new_inuse;
        global $l_new_message;
        global $l_new_topic;
        global $ip;
        global $admin_mail;
        global $language;

        if (!filter_var($this->username, FILTER_VALIDATE_EMAIL)) {
            throw new \Exception($l_new_username . ' ' . $l_new_invalid);
        }

        if (ShipByEmailDAO::call($this->container, $this->username)->ship) {
            throw new \Exception($l_new_inuse);
        }

        $newShip = ShipNewServant::new($this->container);
        $newShip->language = $language;
        $newShip->email = $this->username;
        $newShip->password = $this->password;
        $newShip->character = $this->character;
        $newShip->shipname = $this->shipname;
        $newShip->serve();

        $sendEmail = EmailSendDAO::new($this->container);
        $sendEmail->from = $admin_mail;
        $sendEmail->replyTo = $admin_mail;
        $sendEmail->subject = $l_new_topic;
        $sendEmail->message = str_replace("[pass]", $this->password, $l_new_message);
        $sendEmail->to = $this->username;

        $log = LogPlayerDAO::new($this->container);
        $log->type = LogTypeConstants::LOG_LOGIN;
        $log->data = $ip;
        $log->ship = $newShip->ship['ship_id'];
        $log->serve();

        $this->ship = $newShip->ship;
    }
}
