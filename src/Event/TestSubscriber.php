<?php

namespace EventFun\Event;

class TestSubscriber
{
    public function handle(TestEvent $event)
    {
        var_dump('event received');
    }
}
