<?php

declare(strict_types=1);

namespace BNT\Ship\Mapper;

use BNT\Ship\Ship;
use Psr\Container\ContainerInterface;

class ShipEntityToRowMapper extends \UUA\Mapper
{

    public Ship $ship;
    public array $row;

    #[\Override]
    public function serve(): void
    {
        
    }

    public static function call(ContainerInterface $container, Ship $ship): self
    {
        $self = self::new($container);
        $self->ship = $ship;
        $self->serve();

        return $self;
    }
}
