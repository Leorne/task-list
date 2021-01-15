<?php

namespace Infrastructure;

use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class EventDispatcher implements EventDispatcherInterface
{

    public function dispatch(object $event, string $eventName = null): object
    {
        // TODO: Implement dispatch() method.
        return $event;
    }
}