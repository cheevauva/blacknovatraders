<?php

declare(strict_types=1);

namespace BNT\Game\Servant;

use BNT\Log\LogTypeConstants;
use BNT\Log\DAO\LogPlayerDAO;
use BNT\Email\DAO\EmailSendDAO;
use BNT\User\DAO\UserByEmailDAO;
use BNT\User\Servant\UserWithShipNewServant;
use BNT\Exception\WarningException;
use BNT\Language;

class GameNewServant extends \UUA\Servant
{

    public Language $l;
    public string $language;
    public string $email;
    public string $password;
    public string $character;
    public string $shipname;
    public array $ship;
    public array $user;

    #[\Override]
    public function serve(): void
    {
        global $ip;
        global $admin_mail;

        if (UserByEmailDAO::call($this->container, $this->email)->user) {
            throw new WarningException('l_new_inuse');
        }

        $userWithShipNew = UserWithShipNewServant::new($this->container);
        $userWithShipNew->language = $this->l->languageName();
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
        $sendEmail->subject = $this->l->t('l_new_topic');
        $sendEmail->message = $this->l->t('l_new_message', [
            'pass' => $this->password,
        ]);
        $sendEmail->to = $this->email;

        LogPlayerDAO::call($this->container, $ship['ship_id'], LogTypeConstants::LOG_LOGIN, $ip);

        $this->user = $user;
        $this->ship = $ship;
    }
}
