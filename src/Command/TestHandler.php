<?php

namespace EventFun\Command;

use EventFun\Event\TestEvent;
use SimpleBus\Message\Bus\MessageBus;
use SimpleBus\Message\Recorder\PublicMessageRecorder;

class TestHandler
{
    /**
     * @var PublicMessageRecorder
     */
    private $recorder;
    /**
     * @var MessageBus
     */
    private $eventBus;

    public function __construct(PublicMessageRecorder $recorder, MessageBus $eventBus)
    {
        $this->recorder = $recorder;
        $this->eventBus = $eventBus;
    }

    public function handle(TestCommand $command)
    {
        var_dump(spl_object_hash($command));
        
        var_dump('bus before event sent');
        $this->eventBus->handle(new TestEvent());
        var_dump('bus after event sent');

        var_dump('rec before event sent');
        $this->recorder->record(new TestEvent());
        var_dump('rec after event sent');
        $this->aoesuthaeous();
    }
}
