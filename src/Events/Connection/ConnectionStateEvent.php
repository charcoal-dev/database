<?php
/*
 * Part of the "charcoal-dev/database" package.
 * @link https://github.com/charcoal-dev/database
 */

declare(strict_types=1);

namespace Charcoal\Database\Events\Connection;

use Charcoal\Events\AbstractEvent;
use Charcoal\Events\Subscriptions\Subscription;

/**
 * Class DbConnectionStateEvent
 * @package Charcoal\Database\Events
 */
class ConnectionStateEvent extends AbstractEvent
{
    public function subscribe(): Subscription
    {
        return $this->createSubscription("db-conn-event-" .
            count($this->subscribers()) . "-" . substr(uniqid(), 0, 4));
    }
}
