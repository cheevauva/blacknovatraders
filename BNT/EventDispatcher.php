<?php

declare(strict_types=1);

namespace BNT;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\StoppableEventInterface;
use BNT\UnitInterface;
use BNT\FromToInterface;

class EventDispatcher implements EventDispatcherInterface
{

    use Traits\ContainerTrait;

    public function dispatch(object $event): object
    {
        $events = $this->container->get('events');
        $class = get_class($event);

        if (!empty($events[$class])) {
            $handlers = $events[$class];
        } else {
            while (true) {
                $class = get_parent_class($class);

                if ($class === false) {
                    break;
                }

                if (!empty($events[$class])) {
                    $handlers = $events[$class];
                    break;
                }
            }
        }

        foreach ($handlers as $handler) {

            if ($event instanceof StoppableEventInterface) {
                if ($event->isPropagationStopped()) {
                    break;
                }
            }

            $handlerObject = new $handler($this->container);

            if ($handlerObject instanceof UnitInterface && $event instanceof FromToInterface) {
                $event->to($handlerObject);

                $handlerObject->serve();

                $event->from($handlerObject);
            }
        }

        return $event;
    }

}
