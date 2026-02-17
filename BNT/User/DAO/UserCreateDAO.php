<?php

declare(strict_types=1);

namespace BNT\User\DAO;

use Psr\Container\ContainerInterface;

class UserCreateDAO extends \UUA\DAO
{

    use \BNT\Traits\DatabaseRowCreateTrait;

    /**
     * @var array<string, mixed>
     */
    public array $user;
    public int $id;

    #[\Override]
    public function serve(): void
    {
        $this->id = (int) $this->rowCreate('users', $this->user);
    }

    public static function call(ContainerInterface $container, array $user): self
    {
        $self = self::new($container);
        $self->user = $user;
        $self->serve();

        return $self;
    }
}
