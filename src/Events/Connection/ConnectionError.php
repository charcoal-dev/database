<?php
/*
 * Part of the "charcoal-dev/database" package.
 * @link https://github.com/charcoal-dev/database
 */

declare(strict_types=1);

namespace Charcoal\Database\Events\Connection;

/**
 * Class ConnectionError
 * @package Charcoal\Database\Events\Connection
 */
readonly class ConnectionError implements ConnectionStateContext
{
    public function __construct(
        public \Throwable $exception,
    )
    {
    }
}