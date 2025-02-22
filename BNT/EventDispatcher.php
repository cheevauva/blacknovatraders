<?php

declare(strict_types=1);

namespace BNT;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\StoppableEventInterface;
use BNT\Servant;
use BNT\FromToInterface;

class EventDispatcher implements EventDispatcherInterface
{

    use Traits\ContainerTrait;

    public function dispatch(object $event): object
    {
        $events = $this->container->get('events');

        $handlers = $events[get_class($event)] ?? ($events[get_parent_class($event)] ?? []);

        foreach ($handlers as $handler) {
            if ($event instanceof StoppableEventInterface) {
                if ($event->isPropagationStopped()) {
                    break;
                }
            }

            if (is_a($handler, Servant::class, true) && is_a($handler, FromToInterface::class, true)) {
                $handlerObject = new $handler($this->container);

                $event->to($handlerObject);

                $handlerObject->serve();

                $event->from($handlerObject);
            }
        }
        
        return $event;
    }

}
