<?php

declare(strict_types=1);

namespace BNT\Ship\DAO;

use Psr\Container\ContainerInterface;

class ShipByIdDAO extends \UUA\DAO
{

    /**
     * @var int
     */
    public int $id;

    /**
     * @var array<string, mixed>|null
     */
    public ?array $ship;

    #[\Override]
    public function serve(): void
    {
        $this->ship = db()->fetch("SELECT * FROM ships WHERE ship_id = :id LIMIT 1", [
            'id' => $this->id,
        ]);
    }

    public static function call(ContainerInterface $container, int $id): self
    {
        $self = self::new($container);
        $self->id = $id;
        $self->serve();

        return $self;
    }
}
