<?php

declare(strict_types=1);

namespace BNT\Traits;

use Psr\EventDispatcher\EventDispatcherInterface;

trait EventTrait
{

    public function dispatch(EventDispatcherInterface $eventDispatcher): void
    {
        $eventDispatcher->dispatch($this);
    }

}
