<?php

declare(strict_types=1);

namespace BNT\Game\Servant;

use BNT\Log\LogTypeConstants;
use BNT\Log\DAO\LogPlayerDAO;
use BNT\Email\DAO\EmailSendDAO;
use BNT\User\DAO\UserByEmailDAO;
use BNT\User\Servant\UserWithShipNewServant;

class GameNewServant extends \UUA\Servant
{

    public string $email;
    public string $password;
    public string $character;
    public string $shipname;
    public array $ship;
    public array $user;

    #[\Override]
    public function serve(): void
    {
        global $l_new_inuse;
        global $l_new_message;
        global $l_new_topic;
        global $ip;
        global $admin_mail;
        global $language;

        if (UserByEmailDAO::call($this->container, $this->email)->user) {
            throw new \Exception($l_new_inuse);
        }

        $userWithShipNew = UserWithShipNewServant::new($this->container);
        $userWithShipNew->language = $language;
        $userWithShipNew->email = $this->email;
        $userWithShipNew->password = $this->password;
        $userWithShipNew->character = $this->character;
        $userWithShipNew->shipname = $this->shipname;
        $userWithShipNew->serve();

        $user = $userWithShipNew->user;
        $ship = $userWithShipNew->ship;

        $sendEmail = EmailSendDAO::new($this->container);
        $sendEmail->from = $admin_mail;
        $sendEmail->replyTo = $admin_mail;
        $sendEmail->subject = $l_new_topic;
        $sendEmail->message = str_replace("[pass]", $this->password, $l_new_message);
        $sendEmail->to = $this->email;

        LogPlayerDAO::call($this->container, $ship['ship_id'], LogTypeConstants::LOG_LOGIN, $ip);

        $this->user = $user;
        $this->ship = $ship;
    }
}
