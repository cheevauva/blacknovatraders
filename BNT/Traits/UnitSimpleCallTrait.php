<?php

declare(strict_types=1);

namespace BNT\Traits;

use Psr\Container\ContainerInterface;

trait UnitSimpleCallTrait
{

    public static function call(ContainerInterface $container): static
    {
        $self = self::new($container);
        $self->serve();

        return $self;
    }
}
