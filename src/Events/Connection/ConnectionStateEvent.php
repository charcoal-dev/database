<?php
/*
 * Part of the "charcoal-dev/database" package.
 * @link https://github.com/charcoal-dev/database
 */

declare(strict_types=1);

namespace Charcoal\Database\Events\Connection;

use Charcoal\Database\DatabaseClient;
use Charcoal\Database\DbCredentials;
use Charcoal\Database\Exception\DbConnectionException;
use Charcoal\Events\AbstractEvent;
use Charcoal\Events\Dispatch\DispatchReport;
use Charcoal\Events\Subscriptions\Subscription;

/**
 * Class DbConnectionStateEvent
 * @package Charcoal\Database\Events
 */
class ConnectionStateEvent extends AbstractEvent
{
    /**
     * @return Subscription
     */
    public function subscribe(): Subscription
    {
        return $this->createSubscription("db-conn-event-" .
            count($this->subscribers()) . "-" . substr(uniqid(), 0, 4));
    }

    /**
     * @param DbCredentials $credentials
     * @return DispatchReport
     */
    public function dispatchConnectionWaiting(DbCredentials $credentials): DispatchReport
    {
        return $this->dispatchEvent(new ConnectionWaiting($this, $credentials));
    }

    /**
     * @param DbCredentials $credentials
     * @param DatabaseClient $client
     * @return DispatchReport
     */
    public function dispatchConnectionSuccess(
        DbCredentials  $credentials,
        DatabaseClient $client
    ): DispatchReport
    {
        return $this->dispatchEvent(new ConnectionSuccessful($this, $credentials, $client));
    }

    /**
     * @param DbConnectionException $exception
     * @return DispatchReport
     */
    public function dispatchConnectionFailed(\Throwable $exception): DispatchReport
    {
        return $this->dispatchEvent(new ConnectionFailed($this, $exception));
    }
}
