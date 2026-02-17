<?php

declare(strict_types=1);

namespace BNT\User\Servant;

use BNT\User\DAO\UserCreateDAO;
use BNT\UUID;

class UserNewServant extends \UUA\Servant
{

    public string $email;
    public string $password;
    public string $role = 'user';
    public array $user;

    #[\Override]
    public function serve(): void
    {
        $this->user = $this->newUser();

        $newUser = UserCreateDAO::new($this->container);
        $newUser->user = $this->user;
        $newUser->serve();

        $this->user['id'] = $newUser->id;
    }

    protected function newUser(): array
    {
        return [
            'email' => $this->email,
            'password' => md5($this->password),
            'role' => $this->role,
            'last_login' => date('Y-m-d H:i:s'),
            'token' => UUID::v7(),
            'lang' => 'english',
        ];
    }
}
