<?php

declare(strict_types=1);

namespace BNT\User\Servant;

use BNT\User\DAO\UserCreateDAO;
use BNT\UUID;

class UserNewServant extends \UUA\Servant
{

    public string $email;
    public string $password;
    public string $character;
    public string $role = 'user';
    public array $user;
    public string $language = 'english';

    #[\Override]
    public function serve(): void
    {
        $this->user = $this->newUser();
        $this->user['id'] = UserCreateDAO::call($this->container, $this->user)->id;
    }

    protected function newUser(): array
    {
        return [
            'email' => $this->email,
            'password' => md5($this->password),
            'character_name' => $this->character,
            'role' => $this->role,
            'last_login' => date('Y-m-d H:i:s'),
            'token' => UUID::v7(),
            'lang' => $this->language,
        ];
    }
}
