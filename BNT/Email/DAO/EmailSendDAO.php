<?php

//declare(strict_types=1);

namespace BNT\Email\DAO;

class EmailSendDAO extends \UUA\DAO
{

    public $to;
    public $subject;
    public $message;
    public $from;
    public $replyTo;

    public function serve()
    {
        mail($this->to, $this->subject, $this->message, implode("\r\n", array_filter([
            $this->from ? sprintf('From: %s', $this->from) : null,
            $this->replyTo ? sprintf('Reply-To: %s', $this->replyTo) : null,
            'X-Mailer: PHP/' . phpversion(),
        ])));
    }
}
