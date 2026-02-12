<?php

declare(strict_types=1);

namespace BNT\Ship\DAO;

use Psr\Container\ContainerInterface;

class ShipCreateDAO extends \UUA\DAO
{

    use \BNT\Traits\DatabaseRowCreateTrait;

    public array $ship;
    public int $id;

    #[\Override]
    public function serve(): void
    {
        $this->ship['ship_id'] = $this->id = (int) $this->rowCreate('ships', $this->ship);
    }

    public static function call(ContainerInterface $container, array $ship): self
    {
        $self = self::new($container);
        $self->ship = $ship;
        $self->serve();

        return $self;
    }
}
