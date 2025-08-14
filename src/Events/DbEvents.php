<?php
/*
 * Part of the "charcoal-dev/database" package.
 * @link https://github.com/charcoal-dev/database
 */

declare(strict_types=1);

namespace Charcoal\Database\Events;

use Charcoal\Database\Events\Connection\ConnectionFailed;
use Charcoal\Database\Events\Connection\ConnectionStateEvent;
use Charcoal\Database\Events\Connection\ConnectionStateEventContext;
use Charcoal\Database\Events\Connection\ConnectionSuccessful;
use Charcoal\Database\Events\Connection\ConnectionWaiting;

/**
 * Class DbEvents
 * @package Charcoal\Database\Events
 */
readonly class DbEvents
{
    public ConnectionStateEvent $connectionState;

    public function __construct()
    {
        $this->connectionState = new ConnectionStateEvent("onConnection", [
            ConnectionStateEventContext::class,
            ConnectionWaiting::class,
            ConnectionSuccessful::class,
            ConnectionFailed::class,
        ]);
    }
}