<?php

declare(strict_types=1);

namespace BNT\User\DAO;

use Psr\Container\ContainerInterface;

class UserByEmailDAO extends \UUA\DAO
{

    use \BNT\Traits\DatabaseMainTrait;

    public string $email;
    public ?array $user;

    #[\Override]
    public function serve(): void
    {
        $this->user = $this->db()->fetch("SELECT * FROM users WHERE email = :email LIMIT 1", [
            'email' => $this->email,
        ]) ?: null;
    }

    public static function call(ContainerInterface $container, string $email): self
    {
        $self = self::new($container);
        $self->email = $email;
        $self->serve();

        return $self;
    }
}
