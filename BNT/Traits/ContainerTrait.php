<?php

declare(strict_types=1);

namespace BNT\Traits;

use Psr\Container\ContainerInterface;

trait ContainerTrait
{

    protected ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

}
