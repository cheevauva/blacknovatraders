<?php

declare(strict_types=1);

namespace BNT\User\DAO;

use Psr\Container\ContainerInterface;

class UserUpdateDAO extends \UUA\DAO
{

    use \BNT\Traits\DatabaseMainTrait;

    public int $id;

    /**
     * @var array<string, mixed>
     */
    public $user;

    #[\Override]
    public function serve(): void
    {
        $parameters = [];
        $values = [];

        foreach ($this->user as $key => $value) {
            $values[] = sprintf('%s = :%s', $key, $key);
            $parameters[$key] = $value;
        }

        $parameters['id'] = $this->id;

        $this->db()->q(sprintf('UPDATE users SET %s WHERE id = :id', implode(', ', $values)), $parameters);
    }

    public static function call(ContainerInterface $container, array $user, int $id): self
    {
        $self = self::new($container);
        $self->user = $user;
        $self->id = $id;
        $self->serve();

        return $self;
    }
}
