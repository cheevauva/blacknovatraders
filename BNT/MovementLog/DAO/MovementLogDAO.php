<?php

declare(strict_types=1);

namespace BNT\MovementLog\DAO;

use Psr\Container\ContainerInterface;

class MovementLogDAO extends \UUA\DAO
{

    use \BNT\Traits\DatabaseRowCreateTrait;

    public int $ship;
    public int $sector;

    #[\Override]
    public function serve(): void
    {
        $this->rowCreate('movement_log', [
            'ship_id' => $this->ship, 
            'sector_id' => $this->sector,
            'time' => gmdate('Y-m-d H:i:s'),
        ]);
    }

    public static function call(ContainerInterface $container, int $ship, int $sector): self
    {
        $self = self::new($container);
        $self->ship = $ship;
        $self->sector = $sector;
        $self->serve();

        return $self;
    }
}
