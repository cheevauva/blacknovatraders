<?php

declare(strict_types=1);

namespace BNT\Email;

class Email
{

    public string $email;
    public string $subject;
    public string $message;
    public array $headers = [];

    protected function getHeaders()
    {
        global $admin_mail;
        
        return [
            'From' => $admin_mail,
            'Reply-To' => $admin_mail,
            'X-Mailer' => 'PHP/' . phpversion()
        ];
    }

}
