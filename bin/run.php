#!/usr/bin/env php
<?php

require __DIR__ ."/../vendor/autoload.php";

$container = [];
$container['recorder'] = new \SimpleBus\Message\Recorder\PublicMessageRecorder();
$container['event_bus'] = createEventBus();

/**
 * @return \SimpleBus\Message\Bus\Middleware\MessageBusSupportingMiddleware
 */
function createBus($container)
{
    $bus = new \SimpleBus\Message\Bus\Middleware\MessageBusSupportingMiddleware();

    $serviceLocator = function ($serviceId) use ($container) {
        if ($serviceId === 'zoum') {
            return new \EventFun\Command\TestHandler($container['recorder'], $container['event_bus']);
        }
    };

    $callableMap = new \SimpleBus\Message\CallableResolver\CallableMap(
        [
            'test_command' => 'zoum'
        ],
        new \SimpleBus\Message\CallableResolver\ServiceLocatorAwareCallableResolver($serviceLocator)
    );
    $commandNameResolver = new \SimpleBus\Message\Name\NamedMessageNameResolver();
    $commandHandlerResolver = new \SimpleBus\Message\Handler\Resolver\NameBasedMessageHandlerResolver(
        $commandNameResolver,
        $callableMap
    );

    $bus->appendMiddleware(new \SimpleBus\Message\Handler\DelegatesToMessageHandlerMiddleware($commandHandlerResolver));

    return $bus;
}

function createEventBus()
{
    $eventBus = new \SimpleBus\Message\Bus\Middleware\MessageBusSupportingMiddleware();

    $eventBus->appendMiddleware(new \SimpleBus\Message\Bus\Middleware\FinishesHandlingMessageBeforeHandlingNext());
    $serviceLocator = function ($serviceId) {
        return new \EventFun\Event\TestSubscriber();
    };

    $eventSubscriberCollection = new \SimpleBus\Message\CallableResolver\CallableCollection(
        [
            'test_event' => [
                'test_handler'
            ]
        ],
        new \SimpleBus\Message\CallableResolver\ServiceLocatorAwareCallableResolver($serviceLocator)
    );

    $eventNameResolver = new \SimpleBus\Message\Name\NamedMessageNameResolver();

    $eventSubscribersResolver = new \SimpleBus\Message\Subscriber\Resolver\NameBasedMessageSubscriberResolver(
        $eventNameResolver,
        $eventSubscriberCollection
    );

    $eventBus->appendMiddleware(
        new \SimpleBus\Message\Subscriber\NotifiesMessageSubscribersMiddleware(
            $eventSubscribersResolver
        )
    );

    return $eventBus;
}

$bus = createBus($container);
$eventBus = $container['event_bus'];

$bus->appendMiddleware(
    new \SimpleBus\Message\Recorder\HandlesRecordedMessagesMiddleware(
        $container['recorder'],
        $eventBus
    )
);

var_dump('before command sent');
$bus->handle(new \EventFun\Command\TestCommand());
var_dump('after command sent');

