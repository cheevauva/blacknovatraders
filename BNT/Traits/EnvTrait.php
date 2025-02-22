<?php

declare(strict_types=1);

namespace BNT\Traits;

trait EnvTrait
{

    use ContainerTrait;

    protected function env(): array
    {
        return $this->container->get('env');
    }

}
