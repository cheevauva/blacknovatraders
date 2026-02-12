<?php

declare(strict_types=1);

namespace BNT\Ship\DAO;

use Psr\Container\ContainerInterface;

class ShipByEmailDAO extends \UUA\DAO
{

    use \BNT\Traits\DatabaseMainTrait;

    public string $email;
    public ?array $ship;

    public function serve(): void
    {
        $this->ship = $this->db()->fetch("SELECT * FROM ships WHERE email = :username LIMIT 1", [
            'username' => $this->email,
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
