<?php
/*
 * Part of the "charcoal-dev/database" package.
 * @link https://github.com/charcoal-dev/database
 */

declare(strict_types=1);

namespace Charcoal\Database\Events\Connection;

/**
 * Class ConnectionFailed
 * @package Charcoal\Database\Events\Connection
 */
class ConnectionFailed extends ConnectionStateEventContext
{
    public function __construct(
        ConnectionStateEvent       $event,
        public readonly \Throwable $exception,
    )
    {
        parent::__construct($event);
    }
}