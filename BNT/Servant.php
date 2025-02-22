<?php

declare(strict_types=1);

namespace BNT;

abstract class Servant implements UnitInterface
{

    use Traits\AsTrait;
    use Traits\BuildTrait;
    use Traits\ContainerTrait;
    use Traits\EventDispatcherTrait;

    abstract public function serve(): void;
   
}
