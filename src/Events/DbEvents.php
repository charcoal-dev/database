<?php
/*
 * Part of the "charcoal-dev/database" package.
 * @link https://github.com/charcoal-dev/database
 */

declare(strict_types=1);

namespace Charcoal\Database\Events;

use Charcoal\Database\DatabaseClient;
use Charcoal\Database\Events\Connection\ConnectionError;
use Charcoal\Database\Events\Connection\ConnectionStateContext;
use Charcoal\Database\Events\Connection\ConnectionSuccess;
use Charcoal\Database\Events\Connection\ConnectionWaiting;
use Charcoal\Events\BehaviorEvent;
use Charcoal\Events\Dispatch\DispatchReport;
use Charcoal\Events\Subscriptions\Subscription;

/**
 * Represents a database-related event management system that extends base behavior events.
 * This class facilitates subscribing to database connection events and dispatching state updates.
 */
class DbEvents extends BehaviorEvent
{
    /**
     * @param DatabaseClient $client
     */
    public function __construct(protected readonly DatabaseClient $client)
    {
        parent::__construct("onConnection", [
            ConnectionStateContext::class,
            ConnectionWaiting::class,
            ConnectionSuccess::class,
            ConnectionError::class,
        ]);
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
     * @param ConnectionStateContext $context
     * @return DispatchReport
     */
    public function dispatch(ConnectionStateContext $context): DispatchReport
    {
        return $this->dispatchEvent($context);
    }
}
