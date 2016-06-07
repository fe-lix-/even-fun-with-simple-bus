<?php

namespace EventFun\Command;

use SimpleBus\Message\Name\NamedMessage;

class TestCommand implements NamedMessage
{
    public static function messageName()
    {
        return 'test_command';
    }
}
