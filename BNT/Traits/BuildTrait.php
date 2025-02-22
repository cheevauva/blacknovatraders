<?php

declare(strict_types=1);

namespace BNT\Traits;

use Psr\Container\ContainerInterface;

trait BuildTrait
{

    use EnvTrait;

    public static function new(ContainerInterface $container): static
    {
        return new static($container);
    }

    public static function instance(ContainerInterface $container): static
    {
        return $container->get(static::class);
    }

}
