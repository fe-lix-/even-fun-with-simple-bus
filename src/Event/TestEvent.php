<?php

namespace EventFun\Event;

use SimpleBus\Message\Name\NamedMessage;

class TestEvent implements NamedMessage
{
    public static function messageName()
    {
        return 'test_event';
    }
}
