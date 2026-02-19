<?php

declare(strict_types=1);

namespace BNT\Ship\DAO;

use Psr\Container\ContainerInterface;

class ShipsByUserIdDAO extends \UUA\DAO
{

    use \BNT\Traits\DatabaseMainTrait;

    public int $userId;
    public array $ships;

    #[\Override]
    public function serve(): void
    {
        $this->ships = $this->db()->fetchAll('SELECT * FROM ships WHERE user_id = :userId', [
            'userId' => $this->userId,
        ]);
    }

    public static function call(ContainerInterface $container, int $userId): self
    {
        $self = self::new($container);
        $self->userId = $userId;
        $self->serve();

        return $self;
    }
}
