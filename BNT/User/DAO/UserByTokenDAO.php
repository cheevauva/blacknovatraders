<?php

declare(strict_types=1);

namespace BNT\User\DAO;

use Psr\Container\ContainerInterface;

class UserByTokenDAO extends \UUA\DAO
{

    use \BNT\Traits\DatabaseMainTrait;

    public string $token;

    /**
     * @var array<string, mixed>|null
     */
    public protected(set) ?array $user;

    #[\Override]
    public function serve(): void
    {
        $this->user = $this->db()->fetch("SELECT * FROM users WHERE token = :token LIMIT 1", [
            'token' => $this->token,
        ]);
    }

    public static function call(ContainerInterface $container, string $token): self
    {
        $self = self::new($container);
        $self->token = $token;
        $self->serve();

        return $self;
    }
}
