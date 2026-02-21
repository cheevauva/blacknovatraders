<?php

declare(strict_types=1);

namespace BNT\User\DAO;

use Psr\Container\ContainerInterface;

class UserByIdDAO extends \UUA\DAO
{

    use \BNT\Traits\DatabaseMainTrait;

    public int $id;
    public ?array $user;

    #[\Override]
    public function serve(): void
    {
        $this->user = $this->db()->fetch("SELECT * FROM users WHERE id = :id LIMIT 1", [
            'id' => $this->id,
        ]) ?: null;
    }

    public static function call(ContainerInterface $container, int $id): self
    {
        $self = self::new($container);
        $self->id = $id;
        $self->serve();

        return $self;
    }
}
