<?php
/*
 * Part of the "charcoal-dev/database" package.
 * @link https://github.com/charcoal-dev/database
 */

declare(strict_types=1);

namespace Charcoal\Database\Events;

use Charcoal\Database\DatabaseClient;
use Charcoal\Database\Events\Connection\ConnectionFailed;
use Charcoal\Database\Events\Connection\ConnectionStateContext;
use Charcoal\Database\Events\Connection\ConnectionSuccessful;
use Charcoal\Database\Events\Connection\ConnectionWaiting;
use Charcoal\Events\AbstractEvent;
use Charcoal\Events\BehaviorEvent;
use Charcoal\Events\Dispatch\DispatchReport;
use Charcoal\Events\Subscriptions\Subscription;
use Charcoal\Events\Support\Traits\EventStaticScopeTrait;

/**
 * Class DbConnectionStateEvent
 * @package Charcoal\Database\Events
 * @template T of ConnectionEvent
 * @template S of DatabaseClient
 * @template E of ConnectionSuccessful|ConnectionFailed|ConnectionWaiting
 */
class ConnectionEvent extends BehaviorEvent
{
    use EventStaticScopeTrait;

    /**
     * @param DatabaseClient $client
     */
    public function __construct(protected readonly DatabaseClient $client)
    {
        parent::__construct("onConnection", [
            ConnectionStateContext::class,
            ConnectionWaiting::class,
            ConnectionSuccessful::class,
            ConnectionFailed::class,
        ]);

        $this->registerStaticEventStore($this->client);
    }

    /**
     * @return Subscription
     */
    public function subscribe(): Subscription
    {
        return $this->createSubscription("db-conn-event-" .
            count($this->subscribers()) . "-" . substr(uniqid(), 0, 4));
    }

    /**
     * @param E $context
     * @return DispatchReport
     */
    public function dispatch(ConnectionStateContext $context): DispatchReport
    {
        return $this->dispatchEvent($context);
    }
}
