<?php
/*
 * Part of the "charcoal-dev/database" package.
 * @link https://github.com/charcoal-dev/database
 */

declare(strict_types=1);

namespace Charcoal\Database\Events\Connection;

use Charcoal\Database\Exception\DbConnectionException;

/**
 * Class ConnectionFailed
 * @package Charcoal\Database\Events\Connection
 */
class ConnectionFailed extends ConnectionStateEventContext
{
    public function __construct(
        ConnectionStateEvent                  $event,
        public readonly DbConnectionException $exception,
        public readonly \Throwable            $previous,
    )
    {
        parent::__construct($event);
    }
}