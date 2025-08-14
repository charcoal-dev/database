<?php
/*
 * Part of the "charcoal-dev/database" package.
 * @link https://github.com/charcoal-dev/database
 */

declare(strict_types=1);

namespace Charcoal\Database\Events\Connection;

use Charcoal\Database\DbCredentials;

/**
 * Class ConnectionWaiting
 * @package Charcoal\Database\Events\Connection
 */
class ConnectionWaiting extends ConnectionStateEventContext
{
    public function __construct(
        ConnectionStateEvent          $event,
        #[\SensitiveParameter]
        public readonly DbCredentials $credentials
    )
    {
        parent::__construct($event);
    }
}